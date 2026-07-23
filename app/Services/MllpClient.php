<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class MllpClient
{
    private string $host;
    private int $port;
    private int $timeout;

    private const MLLP_START = "\x0b";
    private const MLLP_END = "\x1c\x0d";

    public function __construct(string $host, int $port, int $timeout = 10)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    public function send(string $hl7Message): ?string
    {
        $socket = @stream_socket_client(
            "tcp://{$this->host}:{$this->port}",
            $errno,
            $errstr,
            $this->timeout
        );

        if (!$socket) {
            Log::error("MLLP connection failed", [
                'host' => $this->host,
                'port' => $this->port,
                'error' => $errstr,
            ]);
            return null;
        }

        stream_set_timeout($socket, $this->timeout);

        $frame = self::MLLP_START . $hl7Message . self::MLLP_END;
        $written = @fwrite($socket, $frame);

        if ($written === false) {
            fclose($socket);
            Log::error("MLLP send failed");
            return null;
        }

        $response = $this->readMllpResponse($socket);
        fclose($socket);

        return $response;
    }

    private function readMllpResponse($socket): ?string
    {
        $buffer = '';
        $started = false;

        while (!feof($socket)) {
            $chunk = fread($socket, 4096);
            if ($chunk === false || $chunk === '') {
                break;
            }

            $buffer .= $chunk;

            if (!$started) {
                $startPos = strpos($buffer, self::MLLP_START);
                if ($startPos !== false) {
                    $started = true;
                    $buffer = substr($buffer, $startPos + 1);
                }
            }

            if ($started) {
                $endPos = strpos($buffer, self::MLLP_END);
                if ($endPos !== false) {
                    return substr($buffer, 0, $endPos);
                }
            }
        }

        return $started ? $buffer : null;
    }
}
