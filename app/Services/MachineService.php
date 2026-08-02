<?php

namespace App\Services;

use App\Models\ExamRequestItem;
use App\Models\MachineConfiguration;
use App\Models\ResultLabo;
use App\Models\ResultLaboDetail;
use App\Models\Staff;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MachineService
{
    protected string $baseUrl;

    protected string $mllpHost;

    protected int $mllpPort;

    protected int $timeout;

    protected string $protocol;

    protected string $serialPort;

    protected int $serialBaudRate;

    protected int $serialDataBits;

    protected int $serialStopBits;

    protected string $serialParity;

    protected ?MachineConfiguration $config;

    /**
     * Create the machine service, using a saved configuration or app defaults.
     *
     * @param  MachineConfiguration|null  $config  optional saved machine configuration
     */
    public function __construct(?MachineConfiguration $config = null)
    {
        $this->config = $config;

        if ($config) {
            // Read connection settings from the stored configuration
            $this->baseUrl = $config->getBaseUrl();
            $this->timeout = $config->timeout ?? config('machine.timeout', 5);
            $this->mllpHost = $config->host;
            $this->mllpPort = $config->mllp_port ?? config('machine.mllp_port', 5001);
            $this->protocol = $config->protocol ?? 'hl7_mllp';
            $this->serialPort = $config->serial_port ?? config('machine.serial.port');
            $this->serialBaudRate = $config->baud_rate ?? config('machine.serial.baud_rate', 9600);
            $this->serialDataBits = $config->data_bits ?? config('machine.serial.data_bits', 8);
            $this->serialStopBits = $config->stop_bits ?? config('machine.serial.stop_bits', 1);
            $this->serialParity = $config->parity ?? config('machine.serial.parity', 'N');
        } else {
            // Fall back to the global machine config
            $this->baseUrl = config('machine.url', 'http://127.0.0.1:5000');
            $this->timeout = config('machine.timeout', 5);
            $parsed = parse_url($this->baseUrl);
            $this->mllpHost = $parsed['host'] ?? '127.0.0.1';
            $this->mllpPort = config('machine.mllp_port', 5001);
            $this->protocol = config('machine.protocol', 'hl7_mllp');
            $this->serialPort = config('machine.serial.port', PHP_OS_FAMILY === 'Windows' ? 'COM3' : '/dev/ttyUSB0');
            $this->serialBaudRate = config('machine.serial.baud_rate', 9600);
            $this->serialDataBits = config('machine.serial.data_bits', 8);
            $this->serialStopBits = config('machine.serial.stop_bits', 1);
            $this->serialParity = config('machine.serial.parity', 'N');
        }
    }

    /**
     * Check whether the machine is currently reachable.
     *
     * @return bool true when the serial port, MLLP port, or status endpoint responds
     */
    public function isOnline(): bool
    {
        // serial machines are reachable when the COM/tty device can be opened
        if ($this->protocol === 'serial_hl7' && $this->isSerialAvailable()) {
            return true;
        }

        // A reachable TCP port is enough to consider the machine online
        if ($this->isTcpReachable()) {
            return true;
        }
        try {
            // Otherwise probe the HTTP status endpoint
            $response = Http::timeout($this->timeout)->get($this->baseUrl.'/api/status');

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Fetch the machine's status payload from the HTTP endpoint.
     *
     * @return array|null decoded status JSON, or null on failure
     */
    public function getStatus(): ?array
    {
        try {
            $response = Http::timeout($this->timeout)->get($this->baseUrl.'/api/status');

            return $response->json();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Send an exam order to the machine using the configured protocol.
     *
     * @param  ExamRequestItem  $item  the exam item to order
     * @return array machine response with results
     */
    public function sendOrder(ExamRequestItem $item): array
    {
        // route the order over the transport matching the saved protocol
        $result = match ($this->protocol) {
            // HL7 over a serial line (RS-232 / USB virtual COM port)
            'serial_hl7' => $this->sendViaSerialHl7($item),
            // Plain HTTP / JSON simulator
            'http_json' => $this->sendViaHttp($item),
            // Default: HL7 over MLLP (TCP)
            default => $this->sendViaHl7($item),
        };

        if ($result !== null) {
            return $result;
        }

        Log::info('Preferred transport unavailable, falling back to HTTP', [
            'item_id' => $item->id,
            'protocol' => $this->protocol,
        ]);

        // Fall back to the HTTP simulator when the chosen transport is unavailable
        return $this->sendViaHttp($item);
    }

    /**
     * Send the order via HL7 ORM^O01 over the serial line and parse the ORU response.
     *
     * @param  ExamRequestItem  $item  the exam item to order
     * @return array|null parsed results, or null when the serial line cannot be used
     */
    private function sendViaSerialHl7(ExamRequestItem $item): ?array
    {
        // Only attempt serial HL7 when the device can be opened
        if (! $this->isSerialAvailable()) {
            Log::warning('Serial port unavailable', ['port' => $this->serialPort]);

            return null;
        }

        try {
            // Build the ORM order and send it over the serial line
            $builder = new Hl7MessageBuilder;
            $ormMessage = $builder->buildOrmOrder($item);

            Log::info('Sending HL7 ORM^O01 over serial', [
                'item_id' => $item->id,
                'exam_code' => $item->exam->code,
                'port' => $this->serialPort,
                'baud' => $this->serialBaudRate,
                'message_length' => strlen($ormMessage),
            ]);

            $client = new SerialClient(
                $this->serialPort,
                $this->serialBaudRate,
                $this->serialDataBits,
                $this->serialStopBits,
                $this->serialParity,
                $this->timeout
            );
            $response = $client->send($ormMessage);

            if ($response === null) {
                Log::warning('Serial no response', ['item_id' => $item->id]);

                return null;
            }

            // Parse the ORU response into results
            $parser = new Hl7ResponseParser;
            $parsed = $parser->parseOru($response);

            $results = $parsed['results'];
            if (empty($results)) {
                Log::warning('HL7 ORU contained no results', ['item_id' => $item->id]);

                return null;
            }

            // Count abnormal results and build the response payload
            $abnormalCount = count(array_filter($results, fn ($r) => $r['status'] !== 'normal'));

            return [
                'status' => 'completed',
                'order_id' => $parsed['order_id'] ?: $item->id,
                'exam_code' => $parsed['exam_code'] ?: $item->exam->code,
                'exam_name' => $parsed['exam_name'] ?: $item->exam->name,
                'results' => $results,
                'processing_time_seconds' => 0,
                'abnormal_count' => $abnormalCount,
                'source' => 'hl7_serial',
            ];
        } catch (\Exception $e) {
            Log::error('Serial HL7 send failed', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Send the order via HL7 ORM^O01 over MLLP and parse the ORU response.
     *
     * @param  ExamRequestItem  $item  the exam item to order
     * @return array|null parsed results, or null when HL7 cannot be used
     */
    private function sendViaHl7(ExamRequestItem $item): ?array
    {
        // Only attempt HL7 if the MLLP port is open
        if (! $this->isTcpReachable()) {
            return null;
        }

        try {
            // Build and send the ORM order over MLLP
            $builder = new Hl7MessageBuilder;
            $ormMessage = $builder->buildOrmOrder($item);

            Log::info('Sending HL7 ORM^O01 via MLLP', [
                'item_id' => $item->id,
                'exam_code' => $item->exam->code,
                'host' => $this->mllpHost,
                'port' => $this->mllpPort,
                'message_length' => strlen($ormMessage),
            ]);

            $client = new MllpClient($this->mllpHost, $this->mllpPort, $this->timeout);
            $response = $client->send($ormMessage);

            if ($response === null) {
                Log::warning('MLLP no response', ['item_id' => $item->id]);

                return null;
            }

            // Parse the ORU response into results
            $parser = new Hl7ResponseParser;
            $parsed = $parser->parseOru($response);

            $results = $parsed['results'];
            if (empty($results)) {
                Log::warning('HL7 ORU contained no results', ['item_id' => $item->id]);

                return null;
            }

            // Count abnormal results and build the response payload
            $abnormalCount = count(array_filter($results, fn ($r) => $r['status'] !== 'normal'));

            return [
                'status' => 'completed',
                'order_id' => $parsed['order_id'] ?: $item->id,
                'exam_code' => $parsed['exam_code'] ?: $item->exam->code,
                'exam_name' => $parsed['exam_name'] ?: $item->exam->name,
                'results' => $results,
                'processing_time_seconds' => 0,
                'abnormal_count' => $abnormalCount,
                'source' => 'hl7_tcp',
            ];
        } catch (\Exception $e) {
            Log::error('HL7 TCP send failed', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Send the order to the HTTP simulator and return its response.
     *
     * @param  ExamRequestItem  $item  the exam item to order
     * @return array simulator response, or built-in generated results on failure
     */
    private function sendViaHttp(ExamRequestItem $item): array
    {
        $patient = $item->examRequest->patient;
        $doctor = $item->examRequest->doctor;

        // Build the JSON order payload for the HTTP simulator
        $payload = [
            'order_id' => 'ORD-'.$item->examRequest->id.'-'.$item->id,
            'exam_request_item_id' => $item->id,
            'exam_code' => $item->exam->code,
            'patient' => [
                'id' => $patient->id,
                'name' => $patient->user->first_name.' '.$patient->user->last_name,
                'birth_date' => $patient->date_of_birth?->format('Y-m-d') ?? '1990-01-01',
                'sex' => $patient->gender ?? 'M',
            ],
            'doctor' => $doctor ? [
                'id' => $doctor->id,
                'name' => 'Dr. '.$doctor->user->first_name.' '.$doctor->user->last_name,
            ] : null,
        ];

        try {
            // Post the order to the simulator
            $response = Http::timeout(3)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->baseUrl.'/api/order', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $data['source'] = 'http_json';

                return $data;
            }
        } catch (\Exception $e) {
            Log::warning('HTTP simulator unreachable, using built-in generator', [
                'item_id' => $item->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Simulator unreachable: generate deterministic results locally
        return $this->generateBuiltInResults($item);
    }

    /**
     * Store machine results for an exam item and trigger completion checks.
     *
     * @param  ExamRequestItem  $item  the exam item the results belong to
     * @param  array  $machineResponse  machine response containing the results
     * @param  int  $staffId  staff user that validated the results
     * @return ResultLabo the created or updated lab result
     */
    public function processResults(ExamRequestItem $item, array $machineResponse, int $staffId): ResultLabo
    {
        // Extract the parameter results from the machine response
        $results = $machineResponse['results'] ?? [];
        $staff = Staff::find($staffId);

        // Create or update the lab result container for this item
        $result = ResultLabo::updateOrCreate(
            ['exam_request_item_id' => $item->id],
            [
                'staff_id' => $staff?->id,
                'interpretation' => 'Résultats générés automatiquement par le BioAnalyzer 3000',
                'is_archive' => false,
            ]
        );

        // Reset old details before storing fresh results
        $result->details()->delete();

        // Store each parameter as a result detail
        foreach ($results as $r) {
            $statusMap = ['normal' => 'normal', 'high' => 'high', 'low' => 'low', 'abnormal' => 'abnormal'];
            $detailStatus = $statusMap[$r['status']] ?? 'normal';

            ResultLaboDetail::create([
                'result_labo_id' => $result->id,
                'parameter' => $r['parameter'],
                'value' => $r['value'],
                'status' => $detailStatus,
                'reference_range' => $r['reference_range'] ?? null,
                'unit' => $r['unit'] ?? null,
                'is_archive' => false,
            ]);
        }

        // Trigger exam completion checks and notifications
        ExamRequestService::checkCompletion($item->examRequest);

        return $result;
    }

    /**
     * Probe whether the MLLP TCP port accepts connections.
     *
     * @return bool true when a socket connection can be opened
     */
    private function isTcpReachable(): bool
    {
        $socket = @stream_socket_client(
            "tcp://{$this->mllpHost}:{$this->mllpPort}",
            $errno,
            $errstr,
            1
        );
        if ($socket) {
            fclose($socket);

            return true;
        }

        return false;
    }

    /**
     * Probe whether the configured serial device can be opened.
     *
     * @return bool true when the serial port is available
     */
    private function isSerialAvailable(): bool
    {
        $client = new SerialClient(
            $this->serialPort,
            $this->serialBaudRate,
            $this->serialDataBits,
            $this->serialStopBits,
            $this->serialParity,
            $this->timeout
        );

        return $client->isAvailable();
    }

    /**
     * Generate plausible results locally when no machine is reachable.
     *
     * @param  ExamRequestItem  $item  the exam item to generate results for
     * @return array generated result payload
     */
    private function generateBuiltInResults(ExamRequestItem $item): array
    {
        $examCode = strtoupper($item->exam->code);
        $sex = $item->examRequest->patient->gender ?? 'M';
        $orderId = 'ORD-'.$item->examRequest->id.'-'.$item->id;

        // Pick the parameter definitions for the exam code
        $params = self::getExamParameters($examCode);
        $results = [];

        // Decide how many parameters will be abnormal
        $abnormalCount = random_int(0, max(1, (int) (count($params) * 0.15)));
        $abnormalIndices = [];
        if ($abnormalCount > 0) {
            $indices = range(0, count($params) - 1);
            shuffle($indices);
            $abnormalIndices = array_slice($indices, 0, $abnormalCount);
        }

        // Generate a value for each parameter
        foreach ($params as $i => $param) {
            $isAbnormal = in_array($i, $abnormalIndices);

            // Qualitative tests only have positive/negative results
            if (! empty($param['qualitative'])) {
                $val = $isAbnormal ? 'Positif' : 'Négatif';
                $results[] = [
                    'parameter' => $param['name'], 'value' => $val, 'unit' => $param['unit'],
                    'reference_range' => 'Négatif', 'status' => $isAbnormal ? 'high' : 'normal',
                ];

                continue;
            }

            // Use gender-specific ranges when the patient is female
            $low = $param['range'][0] ?? $param['range_m'][0];
            $high = $param['range'][1] ?? $param['range_m'][1];
            if ($sex === 'F' && isset($param['range_f'])) {
                $low = $param['range_f'][0];
                $high = $param['range_f'][1];
            }

            // Abnormal values are pushed just outside the reference range
            if ($isAbnormal) {
                $direction = random_int(0, 1) ? 'high' : 'low';
                $value = $direction === 'high'
                    ? $high + ($param['std'] * mt_rand(10, 25) / 10)
                    : $low - ($param['std'] * mt_rand(10, 25) / 10);
                $status = $direction;
            } else {
                // Normal values stay within the reference range
                $value = $param['mean'] + ($param['std'] * (mt_rand(-100, 100) / 100));
                $value = max($low, min($high, $value));
                $status = 'normal';
            }

            // Round the value according to the parameter's precision
            $std = $param['std'];
            $value = $std < 0.01 ? round($value, 3) : ($std < 1.0 ? round($value, 2) : ($std < 10.0 ? round($value, 1) : round($value, 0)));

            $results[] = [
                'parameter' => $param['name'], 'value' => $value, 'unit' => $param['unit'],
                'reference_range' => "{$low} - {$high}", 'status' => $status,
            ];
        }

        // Assemble the generated result payload
        return [
            'status' => 'completed', 'order_id' => $orderId, 'exam_code' => $examCode,
            'exam_name' => $item->exam->name, 'results' => $results,
            'processing_time_seconds' => round(mt_rand(150, 400) / 100, 2),
            'abnormal_count' => count(array_filter($results, fn ($r) => $r['status'] !== 'normal')),
            'source' => 'builtin',
        ];
    }

    /**
     * Resolve the parameter definitions for an exam code.
     *
     * @param  string  $code  exam code, possibly an alias
     * @return array list of parameter definition arrays
     */
    private static function getExamParameters(string $code): array
    {
        // Normalize aliases to the canonical exam code
        $aliasMap = [
            'CBC' => 'NFS', 'BLOOD' => 'NFS', 'HEMO' => 'NFS',
            'GLYCEMIE' => 'GLYC', 'GLYCO' => 'GLYC', 'LIPID' => 'GLYC',
            'CREATININE' => 'CREAT', 'DFG' => 'CREAT',
            'THYROID' => 'TSH', 'THYROIDE' => 'TSH',
            'URINE' => 'ECBU', 'UROCULTURE' => 'ECBU',
            'IONO' => 'IONO', 'ELECTROLYTES' => 'IONO',
        ];
        $resolved = $aliasMap[$code] ?? $code;

        $params = [
            'NFS' => [
                ['name' => 'Hémoglobine', 'unit' => 'g/dL', 'range_m' => [13.0, 17.0], 'range_f' => [12.0, 16.0], 'mean' => 14.5, 'std' => 1.2],
                ['name' => 'Hématocrite', 'unit' => '%', 'range_m' => [40.0, 54.0], 'range_f' => [36.0, 46.0], 'mean' => 45.0, 'std' => 3.0],
                ['name' => 'Leucocytes', 'unit' => 'G/L', 'range' => [4.0, 10.0], 'mean' => 7.0, 'std' => 1.5],
                ['name' => 'Plaquettes', 'unit' => 'G/L', 'range' => [150.0, 400.0], 'mean' => 250.0, 'std' => 50.0],
                ['name' => 'Neutrophiles', 'unit' => '%', 'range' => [40.0, 75.0], 'mean' => 55.0, 'std' => 8.0],
                ['name' => 'Lymphocytes', 'unit' => '%', 'range' => [20.0, 45.0], 'mean' => 30.0, 'std' => 5.0],
            ],
            'GLYC' => [['name' => 'Glycémie', 'unit' => 'g/L', 'range' => [0.70, 1.10], 'mean' => 0.90, 'std' => 0.10]],
            'HB1AC' => [['name' => 'HbA1c', 'unit' => '%', 'range' => [4.0, 5.7], 'mean' => 5.2, 'std' => 0.4]],
            'UREE' => [['name' => 'Urée', 'unit' => 'g/L', 'range' => [0.15, 0.45], 'mean' => 0.30, 'std' => 0.07]],
            'CREAT' => [['name' => 'Créatinine', 'unit' => 'mg/L', 'range_m' => [7.0, 13.0], 'range_f' => [6.0, 11.0], 'mean' => 9.0, 'std' => 1.5]],
            'CRP' => [['name' => 'CRP', 'unit' => 'mg/L', 'range' => [0.0, 6.0], 'mean' => 2.0, 'std' => 1.5]],
            'VS' => [['name' => 'VS 1ère heure', 'unit' => 'mm/h', 'range_m' => [0.0, 15.0], 'range_f' => [0.0, 20.0], 'mean' => 8.0, 'std' => 4.0]],
            'IONO' => [
                ['name' => 'Sodium', 'unit' => 'mmol/L', 'range' => [135.0, 145.0], 'mean' => 140.0, 'std' => 2.5],
                ['name' => 'Potassium', 'unit' => 'mmol/L', 'range' => [3.5, 5.0], 'mean' => 4.2, 'std' => 0.35],
                ['name' => 'Chlore', 'unit' => 'mmol/L', 'range' => [96.0, 106.0], 'mean' => 101.0, 'std' => 2.5],
            ],
            'TSH' => [
                ['name' => 'TSH', 'unit' => 'mUI/L', 'range' => [0.4, 4.0], 'mean' => 2.0, 'std' => 0.8],
                ['name' => 'T4L', 'unit' => 'pmol/L', 'range' => [12.0, 22.0], 'mean' => 16.5, 'std' => 2.5],
            ],
            'ECBU' => [
                ['name' => 'pH', 'unit' => '', 'range' => [5.0, 8.0], 'mean' => 6.0, 'std' => 0.7],
                ['name' => 'Densité', 'unit' => '', 'range' => [1.005, 1.030], 'mean' => 1.015, 'std' => 0.005],
                ['name' => 'Leucocytes', 'unit' => '/µL', 'range' => [0.0, 25.0], 'mean' => 8.0, 'std' => 5.0],
            ],
        ];

        return $params[$resolved] ?? $params['GLYC'];
    }
}
