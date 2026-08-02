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

    /**
     * Create an MLLP client for the given host and port.
     *
     * @param  string  $host  MLLP server host
     * @param  int  $port  MLLP server port
     * @param  int  $timeout  connection and read timeout in seconds
     */
    public function __construct(string $host, int $port, int $timeout = 10)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * Wrap an HL7 message in MLLP framing, send it, and read the reply.
     *
     * @param  string  $hl7Message  HL7 message to send
     * @return string|null the unframed HL7 reply, or null on failure
     */
    public function send(string $hl7Message): ?string
    {
        // Open a TCP socket to the MLLP server
        $socket = @stream_socket_client(
            "tcp://{$this->host}:{$this->port}",
            $errno,
            $errstr,
            $this->timeout
        );

        // Log the failure and bail out when the connection could not be opened
        if (! $socket) {
            Log::error('MLLP connection failed', [
                'host' => $this->host,
                'port' => $this->port,
                'error' => $errstr,
            ]);

            return null;
        }

        // Apply the read timeout to the socket
        stream_set_timeout($socket, $this->timeout);

        // Wrap the HL7 message in MLLP framing bytes
        $frame = self::MLLP_START.$hl7Message.self::MLLP_END;
        $written = @fwrite($socket, $frame);

        if ($written === false) {
            fclose($socket);
            Log::error('MLLP send failed');

            return null;
        }

        // Read the framed response and close the socket
        $response = $this->readMllpResponse($socket);
        fclose($socket);

        return $response;
    }

    /**
     * Read chunks from the socket until the MLLP frame is complete.
     *
     * @param  resource  $socket  connected TCP socket
     * @return string|null the HL7 message, or null when no frame arrives
     */
    private function readMllpResponse($socket): ?string
    {
        $buffer = '';
        $started = false;

        // Read chunks until the frame is complete or the connection closes
        while (! feof($socket)) {
            $chunk = fread($socket, 4096);
            if ($chunk === false || $chunk === '') {
                break;
            }

            $buffer .= $chunk;

            // Skip everything before the frame start byte
            if (! $started) {
                $startPos = strpos($buffer, self::MLLP_START);
                if ($startPos !== false) {
                    $started = true;
                    $buffer = substr($buffer, $startPos + 1);
                }
            }

            // Return the message once the end-of-frame bytes are found
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
