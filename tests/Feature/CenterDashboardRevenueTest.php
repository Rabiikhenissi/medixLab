<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Labo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Str;
use Tests\CreatesUsers;
use Tests\TestCase;

class CenterDashboardRevenueTest extends TestCase
{
    use CreatesUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ThrottleRequests::class);
    }

    protected function makeInvoice(Labo $labo, array $overrides = []): Invoice
    {
        $patient = $this->makePatient();

        return Invoice::create(array_merge([
            'invoice_number' => 'INV-TEST-'.Str::upper(Str::random(8)),
            'patient_id' => $patient['patient']->id,
            'labo_id' => $labo->id,
            'status' => 'pending',
            'total_amount' => 0,
            'cnam_amount' => 0,
            'patient_amount' => 0,
            'paid_amount' => 0,
        ], $overrides));
    }

    public function test_dashboard_revenue_is_computed_from_invoices(): void
    {
        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);

        $this->makeInvoice($labo, ['status' => 'pending', 'total_amount' => 100, 'patient_amount' => 100, 'paid_amount' => 0]);
        $this->makeInvoice($labo, ['status' => 'partially_paid', 'total_amount' => 100, 'patient_amount' => 100, 'paid_amount' => 60]);
        $this->makeInvoice($labo, ['status' => 'paid', 'total_amount' => 100, 'patient_amount' => 100, 'paid_amount' => 100]);
        $this->makeInvoice($labo, ['status' => 'cancelled', 'total_amount' => 500, 'patient_amount' => 500, 'paid_amount' => 0]);

        $otherLabo = $this->makeLabo('Autre laboratoire');
        $this->makeInvoice($otherLabo, ['status' => 'paid', 'total_amount' => 999, 'patient_amount' => 999, 'paid_amount' => 999]);

        $response = $this->actingAs($staff['user'])->get(route('center.dashboard'));

        $response->assertOk();
        $response->assertSee('Revenus (Facturation)');
        $response->assertSee('160.00');   // encaissé = 60 + 100
        $response->assertSee('300.00');   // facturé = 100 + 100 + 100 (cancelled + other lab excluded)
        $response->assertSee('140.00');   // en attente = 100 + 40
        $response->assertSee('3 facture(s)');
        $response->assertDontSee('999.00');
        $response->assertDontSee('Revenus Estimés');
    }

    public function test_dashboard_revenue_is_zero_without_invoices(): void
    {
        $labo = $this->makeLabo();
        $staff = $this->makeStaff($labo);

        $this->actingAs($staff['user'])->get(route('center.dashboard'))
            ->assertOk()
            ->assertSee('0.00');
    }
}
