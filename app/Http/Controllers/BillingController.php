<?php

namespace App\Http\Controllers;

use App\Models\CnamAffiliation;
use App\Models\CnamNomenclature;
use App\Models\ExamRequest;
use App\Models\ExamRequestItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Notification;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillingController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (! auth()->check() || ! auth()->user()->staff) {
                return redirect()->route('home');
            }

            return $next($request);
        });
    }

    /**
     * List the invoices of the current user's laboratory, paginated.
     *
     * @return View
     */
    public function index()
    {
        // invoices belong to the current user's laboratory
        $lab = auth()->user()->staff->laboratory;
        $invoices = Invoice::where('labo_id', $lab->id)
            ->with(['patient.user', 'examRequest'])
            ->latest()
            ->paginate(15);

        return view('center.billing.index', compact('invoices'));
    }

    /**
     * Show the billing form with completed exam requests and active CNAM codes.
     *
     * @return View
     */
    public function create()
    {
        // only completed requests that are not already invoiced are billable
        $lab = auth()->user()->staff->laboratory;
        $completedRequests = ExamRequest::where('labo_id', $lab->id)
            ->where('status', 'completed')
            ->whereDoesntHave('invoices')
            ->with(['patient.user', 'items.exam'])
            ->latest()
            ->get();
        $nomenclatures = CnamNomenclature::where('is_active', true)->get();

        return view('center.billing.create', compact('completedRequests', 'nomenclatures'));
    }

    /**
     * Create an invoice from a completed exam request and its billed items.
     *
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $lab = auth()->user()->staff->laboratory;

        // validate invoice and line item payload
        $validated = $request->validate([
            'exam_request_id' => 'required|exists:exam_requests,id',
            'items' => 'required|array|min:1',
            'items.*.exam_request_item_id' => 'required|exists:exam_request_items,id',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.cnam_code' => 'nullable|string',
            'items.*.valeur_b' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // load the exam request and make sure it belongs to this laboratory
        $examRequest = ExamRequest::findOrFail($validated['exam_request_id']);
        if ($examRequest->labo_id !== $lab->id) {
            abort(403);
        }
        $patient = $examRequest->patient;
        // load the patient's active CNAM affiliation with its rate
        $cnamAffiliation = CnamAffiliation::where('patient_id', $patient->id)->where('is_active', true)->with('rate')->first();

        DB::transaction(function () use ($validated, $lab, $examRequest, $patient, $cnamAffiliation, &$invoice) {
            // generate a unique invoice number
            $invoiceNumber = 'INV-'.$lab->id.'-'.date('Ymd').'-'.strtoupper(substr(uniqid(), -6));

            $totalAmount = 0;
            $cnamAmount = 0;

            // create the invoice header
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'patient_id' => $patient->id,
                'labo_id' => $lab->id,
                'exam_request_id' => $examRequest->id,
                'status' => 'pending',
                'total_amount' => 0,
                'cnam_amount' => 0,
                'patient_amount' => 0,
                'paid_amount' => 0,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                // compute the line total and the CNAM coverage if applicable
                $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                $itemCnamCoverage = 0;

                if ($itemData['cnam_code'] && $cnamAffiliation) {
                    $nomenclature = CnamNomenclature::where('code_cnam', $itemData['cnam_code'])->first();
                    if ($nomenclature) {
                        $valeurB = $itemData['valeur_b'] ?? $nomenclature->valeur_b;
                        $taux = $cnamAffiliation->rate->taux;
                        $itemCnamCoverage = $valeurB * $itemData['quantity'] * ($taux / 100);
                    }
                }

                $examRequestItem = ExamRequestItem::findOrFail($itemData['exam_request_item_id']);
                if ($examRequestItem->exam_request_id !== $examRequest->id) {
                    abort(403);
                }

                // attach the invoice line item
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'exam_id' => $examRequestItem->exam_id,
                    'exam_request_item_id' => $examRequestItem->id,
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'total' => $itemTotal,
                    'cnam_code' => $itemData['cnam_code'] ?? null,
                    'valeur_b' => $itemData['valeur_b'] ?? null,
                    'cnam_coverage' => $itemCnamCoverage,
                ]);

                $totalAmount += $itemTotal;
                $cnamAmount += $itemCnamCoverage;
            }

            // store the computed totals on the invoice
            $patientAmount = max(0, $totalAmount - $cnamAmount);

            $invoice->update([
                'total_amount' => $totalAmount,
                'cnam_amount' => $cnamAmount,
                'patient_amount' => $patientAmount,
            ]);
        });

        // redirect to the newly created invoice
        return redirect()->route('center.billing.show', $invoice->id)
            ->with('success', 'Facture créée avec succès.');
    }

    /**
     * Display a single invoice with its items and payments.
     *
     * @return View
     */
    public function show(Invoice $invoice)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($invoice->labo_id !== $lab->id) {
            abort(403);
        }
        $invoice->load(['patient.user', 'items.exam', 'payments']);

        return view('center.billing.show', compact('invoice'));
    }

    /**
     * Show the printable version of an invoice.
     *
     * @return View
     */
    public function print(Invoice $invoice)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($invoice->labo_id !== $lab->id) {
            abort(403);
        }
        $invoice->load(['patient.user', 'items.exam', 'labo']);

        return view('center.billing.print', compact('invoice'));
    }

    /**
     * Show the printable version of an invoice for the CNAM traité.
     *
     * @return View
     */
    public function printTraite(Invoice $invoice)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($invoice->labo_id !== $lab->id) {
            abort(403);
        }
        $invoice->load(['patient.user', 'patient.cnamAffiliation.rate', 'items.exam', 'labo']);

        return view('center.billing.traite', compact('invoice'));
    }

    /**
     * Register a payment against an invoice and recompute its status.
     *
     * @return RedirectResponse
     */
    public function registerPayment(Request $request, Invoice $invoice)
    {
        // make sure the invoice belongs to this laboratory
        $lab = auth()->user()->staff->laboratory;
        if ($invoice->labo_id !== $lab->id) {
            abort(403);
        }

        // validate the payment amount and method
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.001|max:'.$invoice->balance,
            'payment_method' => 'required|in:cash,card,cheque,bank_transfer,online',
            'transaction_id' => 'nullable|string|max:100',
        ]);

        DB::transaction(function () use ($validated, $invoice) {
            // record the payment
            Payment::create([
                'invoice_id' => $invoice->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'payment_date' => now(),
            ]);

            // recompute paid amount and invoice status
            $newPaid = $invoice->payments()->sum('amount');
            $status = $newPaid >= $invoice->patient_amount ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $status,
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['transaction_id'] ?? $invoice->payment_reference,
                'paid_at' => $status === 'paid' ? now() : $invoice->paid_at,
            ]);
        });

        // redirect back to the invoice
        return redirect()->route('center.billing.show', $invoice->id)
            ->with('success', 'Paiement enregistré.');
    }

    /**
     * Cancel an invoice.
     *
     * @return RedirectResponse
     */
    public function cancel(Invoice $invoice)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($invoice->labo_id !== $lab->id) {
            abort(403);
        }
        $invoice->update(['status' => 'cancelled']);

        return redirect()->route('center.billing.index')->with('success', 'Facture annulée.');
    }

    /**
     * Confirm a pending payment and notify the patient.
     *
     * @return RedirectResponse
     */
    public function confirmPayment(Payment $payment)
    {
        // make sure the payment's invoice belongs to this laboratory
        $lab = auth()->user()->staff->laboratory;
        $invoice = $payment->invoice;
        if (! $invoice || $invoice->labo_id !== $lab->id) {
            abort(403);
        }

        // reject payments that were already processed
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Ce paiement a déjà été traité.');
        }

        DB::transaction(function () use ($payment, $invoice) {
            // mark the payment as confirmed
            $payment->update(['status' => 'confirmed', 'confirmed_at' => now()]);

            // recompute paid amount and invoice status
            $newPaid = $invoice->payments()->where('status', 'confirmed')->sum('amount');
            $status = $newPaid >= $invoice->patient_amount ? 'paid' : 'partially_paid';

            $invoice->update([
                'paid_amount' => $newPaid,
                'status' => $status,
                'payment_method' => $payment->payment_method,
                'paid_at' => $status === 'paid' ? now() : $invoice->paid_at,
            ]);
        });

        // Notify patient
        if ($invoice->patient && $invoice->patient->user) {
            Notification::create([
                'user_id' => $invoice->patient->user->id,
                'title' => 'Paiement confirmé',
                'message' => 'Votre paiement de '.number_format($payment->amount, 3).' TND sur la facture '.$invoice->invoice_number.' a été confirmé par le laboratoire.',
                'notification_type' => 'payment',
                'reference_id' => $payment->id,
            ]);
        }

        return back()->with('success', 'Paiement confirmé.');
    }

    /**
     * List the active CNAM nomenclatures.
     *
     * @return View
     */
    public function cnamIndex()
    {
        $nomenclatures = CnamNomenclature::where('is_active', true)->paginate(20);

        return view('center.billing.cnam', compact('nomenclatures'));
    }

    /**
     * Store a new CNAM nomenclature.
     *
     * @return RedirectResponse
     */
    public function cnamStore(Request $request)
    {
        // validate the nomenclature fields
        $validated = $request->validate([
            'code_cnam' => 'required|string|max:20|unique:cnam_nomenclatures',
            'exam_name' => 'required|string|max:255',
            'valeur_b' => 'required|numeric|min:0',
            'taux' => 'required|numeric|min:0|max:100',
            'description' => 'nullable|string',
        ]);

        CnamNomenclature::create($validated);

        return redirect()->route('center.cnam.index')->with('success', 'Nomenclature CNAM ajoutée.');
    }

    /**
     * Export an invoice as an el-Fatoora compliant XML attachment.
     *
     * @return Response
     */
    public function elFatooraExport(Invoice $invoice)
    {
        $lab = auth()->user()->staff->laboratory;
        if ($invoice->labo_id !== $lab->id) {
            abort(403);
        }
        $invoice->load(['patient.user', 'items', 'labo']);

        $xml = $this->generateElFatooraXml($invoice);

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="fatoora-'.$invoice->invoice_number.'.xml"',
        ]);
    }

    /**
     * Build the UBL el-Fatoora XML for an invoice.
     *
     * @return string
     */
    private function generateElFatooraXml(Invoice $invoice)
    {
        $lab = $invoice->labo;
        $patient = $invoice->patient;

        // create the XML document and its root invoice element
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;

        $root = $doc->createElementNS('urn:oasis:names:specification:ubl:schema:xsd:Invoice-2', 'Invoice');
        $doc->appendChild($root);

        // invoice identification and currency
        $root->appendChild($doc->createElement('cbc:ID', $invoice->invoice_number));
        $root->appendChild($doc->createElement('cbc:IssueDate', $invoice->created_at->format('Y-m-d')));
        $root->appendChild($doc->createElement('cbc:InvoiceTypeCode', '380'));
        $root->appendChild($doc->createElement('cbc:DocumentCurrencyCode', 'TND'));

        // supplier party (the laboratory)
        $accParty = $doc->createElement('cac:AccountingSupplierParty');
        $party = $doc->createElement('cac:Party');
        $party->appendChild($doc->createElement('cbc:Name', htmlspecialchars($lab->name)));
        $accParty->appendChild($party);
        $root->appendChild($accParty);

        // customer party (the patient)
        $custParty = $doc->createElement('cac:AccountingCustomerParty');
        $cust = $doc->createElement('cac:Party');
        $cust->appendChild($doc->createElement('cbc:Name', htmlspecialchars($patient->user->first_name.' '.$patient->user->last_name)));
        $custParty->appendChild($cust);
        $root->appendChild($custParty);

        // invoice lines with quantities and prices
        foreach ($invoice->items as $item) {
            $line = $doc->createElement('cac:InvoiceLine');
            $line->appendChild($doc->createElement('cbc:ID', $item->id));
            $line->appendChild($doc->createElement('cbc:InvoicedQuantity', $item->quantity));
            $line->appendChild($doc->createElement('cbc:LineExtensionAmount', number_format($item->total, 3, '.', '')));

            $itemNode = $doc->createElement('cac:Item');
            $itemNode->appendChild($doc->createElement('cbc:Name', htmlspecialchars($item->description)));
            $line->appendChild($itemNode);

            $price = $doc->createElement('cac:Price');
            $price->appendChild($doc->createElement('cbc:PriceAmount', number_format($item->unit_price, 3, '.', '')));
            $line->appendChild($price);

            $root->appendChild($line);
        }

        // tax totals (no taxes in this version)
        $taxTotal = $doc->createElement('cac:TaxTotal');
        $taxTotal->appendChild($doc->createElement('cbc:TaxAmount', '0.000'));
        $root->appendChild($taxTotal);

        // monetary totals and payable amount
        $legalMonetary = $doc->createElement('cac:LegalMonetaryTotal');
        $legalMonetary->appendChild($doc->createElement('cbc:LineExtensionAmount', number_format($invoice->total_amount, 3, '.', '')));
        $legalMonetary->appendChild($doc->createElement('cbc:TaxExclusiveAmount', number_format($invoice->total_amount, 3, '.', '')));
        $legalMonetary->appendChild($doc->createElement('cbc:PayableAmount', number_format($invoice->patient_amount, 3, '.', '')));
        $root->appendChild($legalMonetary);

        return $doc->saveXML();
    }
}
