<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class Hl7ResponseParser
{
    public function parseOru(string $hl7Message): array
    {
        $segments = preg_split('/\r/', $hl7Message, -1, PREG_SPLIT_NO_EMPTY);

        $result = [
            'message_id' => '',
            'message_type' => '',
            'patient_id' => '',
            'patient_name' => '',
            'order_id' => '',
            'exam_code' => '',
            'exam_name' => '',
            'results' => [],
            'ack_code' => '',
            'ack_text' => '',
        ];

        foreach ($segments as $segment) {
            $fields = explode('|', $segment);
            $segType = trim($fields[0] ?? '');

            switch ($segType) {
                case 'MSH':
                    $this->parseMsh($fields, $result);
                    break;
                case 'MSA':
                    $this->parseMsa($fields, $result);
                    break;
                case 'PID':
                    $this->parsePid($fields, $result);
                    break;
                case 'ORC':
                    $this->parseOrc($fields, $result);
                    break;
                case 'OBR':
                    $this->parseObr($fields, $result);
                    break;
                case 'OBX':
                    $this->parseObx($fields, $result);
                    break;
            }
        }

        Log::info('HL7 ORU parsed', [
            'order_id' => $result['order_id'],
            'exam_code' => $result['exam_code'],
            'results_count' => count($result['results']),
        ]);

        return $result;
    }

    public function parseAck(string $hl7Message): array
    {
        $segments = preg_split('/\r/', $hl7Message, -1, PREG_SPLIT_NO_EMPTY);
        $result = ['ack_code' => '', 'ack_text' => '', 'message_id' => ''];

        foreach ($segments as $segment) {
            $fields = explode('|', $segment);
            $segType = trim($fields[0] ?? '');

            if ($segType === 'MSH') {
                $result['message_id'] = $fields[9] ?? '';
            } elseif ($segType === 'MSA') {
                $result['ack_code'] = $fields[1] ?? '';
                $result['ack_text'] = $fields[3] ?? '';
            }
        }

        return $result;
    }

    private function parseMsh(array $fields, array &$result): void
    {
        $result['message_type'] = $fields[8] ?? '';
        $result['message_id'] = $fields[9] ?? '';
    }

    private function parseMsa(array $fields, array &$result): void
    {
        $result['ack_code'] = $fields[1] ?? '';
        $result['ack_text'] = $fields[3] ?? '';
    }

    private function parsePid(array $fields, array &$result): void
    {
        $pid3 = $fields[3] ?? '';
        $parts = explode('^', $pid3);
        $result['patient_id'] = $parts[0] ?? $pid3;

        $pid5 = $fields[5] ?? '';
        $nameParts = explode('^', $pid5);
        if (count($nameParts) >= 2) {
            $result['patient_name'] = trim($nameParts[0]) . ' ' . trim($nameParts[1]);
        } else {
            $result['patient_name'] = $pid5;
        }
    }

    private function parseOrc(array $fields, array &$result): void
    {
        $result['order_id'] = $fields[2] ?? '';
    }

    private function parseObr(array $fields, array &$result): void
    {
        $obr4 = $fields[4] ?? '';
        $parts = explode('^', $obr4);
        $result['exam_code'] = $parts[0] ?? $obr4;
        $result['exam_name'] = $parts[1] ?? '';
    }

    private function parseObx(array $fields, array &$result): void
    {
        $obx3 = $fields[3] ?? '';
        $paramParts = explode('^', $obx3);
        $parameterName = $paramParts[1] ?? ($paramParts[0] ?? '');

        $value = $fields[5] ?? '';
        $unit = $fields[6] ?? '';
        $refRange = $fields[7] ?? '';
        $flag = $fields[10] ?? '';

        $status = 'normal';
        if ($flag === 'H' || $flag === 'HH') {
            $status = 'high';
        } elseif ($flag === 'L' || $flag === 'LL') {
            $status = 'low';
        } elseif ($flag === 'A') {
            $status = 'abnormal';
        }

        $result['results'][] = [
            'parameter' => $parameterName,
            'value' => $value,
            'unit' => $unit,
            'reference_range' => $refRange,
            'status' => $status,
        ];
    }
}
