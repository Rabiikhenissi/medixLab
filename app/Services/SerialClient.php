<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SerialClient
{
    private const MLLP_START = "\x0b";

    private const MLLP_END = "\x1c\x0d";

    private string $port;

    private int $baudRate;

    private int $dataBits;

    private int $stopBits;

    private string $parity;

    private int $timeout;

    /**
     * Create a serial (RS-232 / USB virtual COM) client for HL7 over MLLP.
     *
     * @param  string  $port  serial device, e.g. "COM5" on Windows or "/dev/ttyUSB0" on Linux
     * @param  int  $baudRate  line speed in bits per second (9600 is the lab default)
     * @param  int  $dataBits  data bits, usually 8
     * @param  int  $stopBits  stop bits, usually 1
     * @param  string  $parity  parity, one of "N", "E" or "O"
     * @param  int  $timeout  read timeout in seconds
     */
    public function __construct(
        string $port,
        int $baudRate = 9600,
        int $dataBits = 8,
        int $stopBits = 1,
        string $parity = 'N',
        int $timeout = 10
    ) {
        $this->port = $port;
        $this->baudRate = $baudRate;
        $this->dataBits = $dataBits;
        $this->stopBits = $stopBits;
        $this->parity = strtoupper($parity);
        $this->timeout = $timeout;
    }

    /**
     * Check whether the serial device exists and can be opened.
     *
     * @return bool true when the port is available
     */
    public function isAvailable(): bool
    {
        $fp = @fopen($this->port, 'w+b');
        if ($fp) {
            fclose($fp);

            return true;
        }

        return false;
    }

    /**
     * Wrap an HL7 message in MLLP framing, send it over the serial line, and read the reply.
     *
     * @param  string  $hl7Message  HL7 message to send
     * @return string|null the unframed HL7 reply, or null on failure
     */
    public function send(string $hl7Message): ?string
    {
        // configure the line parameters before opening the port
        $this->configureLine();

        $fp = @fopen($this->port, 'w+b');
        if (! $fp) {
            Log::error('Serial open failed', [
                'port' => $this->port,
                'error' => error_get_last()['message'] ?? 'unknown',
            ]);

            return null;
        }

        // make the read non-blocking so the timeout applies
        stream_set_blocking($fp, false);
        stream_set_timeout($fp, $this->timeout);

        // wrap the HL7 message in MLLP framing bytes and send it
        $frame = self::MLLP_START.$hl7Message.self::MLLP_END;
        $written = @fwrite($fp, $frame);

        if ($written === false) {
            fclose($fp);
            Log::error('Serial send failed', ['port' => $this->port]);

            return null;
        }

        // flush the write buffer so bytes actually reach the device
        fflush($fp);

        // read the framed response with a timeout
        $response = $this->readMllpResponse($fp);
        fclose($fp);

        return $response;
    }

    /**
     * Configure the serial line speed and framing via the OS tool.
     *
     * Windows uses the built-in "mode" command; Linux uses "stty".
     * A failure here is not fatal: the port may already be configured.
     */
    private function configureLine(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            @exec("mode {$this->port} BAUD={$this->baudRate} PARITY={$this->parity} DATA={$this->dataBits} STOP={$this->stopBits}");
        } else {
            $parityFlags = match ($this->parity) {
                'E' => 'parenb -parodd',
                'O' => 'parenb parodd',
                default => '-parenb -parodd',
            };
            $stopFlags = $this->stopBits >= 2 ? 'cstopb' : '-cstopb';
            @exec("stty -F {$this->port} {$this->baudRate} cs{$this->dataBits} {$parityFlags} {$stopFlags}");
        }
    }

    /**
     * Read chunks from the serial stream until the MLLP frame is complete or the timeout elapses.
     *
     * @param  resource  $fp  opened serial stream
     * @return string|null the HL7 message, or null when no frame arrives
     */
    private function readMllpResponse($fp): ?string
    {
        $buffer = '';
        $started = false;
        $deadline = microtime(true) + $this->timeout;

        // keep reading until the timeout deadline
        while (microtime(true) < $deadline) {
            // wait for readable data, then read a chunk
            $read = [$fp];
            $write = null;
            $except = null;

            // small select windows so we can enforce the total deadline
            $ready = @stream_select($read, $write, $except, 0, 200000);
            if ($ready === false) {
                break;
            }

            if ($ready > 0) {
                $chunk = fread($fp, 4096);
                if ($chunk === false || $chunk === '') {
                    break;
                }

                $buffer .= $chunk;

                // skip everything before the frame start byte
                if (! $started) {
                    $startPos = strpos($buffer, self::MLLP_START);
                    if ($startPos !== false) {
                        $started = true;
                        $buffer = substr($buffer, $startPos + 1);
                    }
                }

                // return the message once the end-of-frame bytes are found
                if ($started) {
                    $endPos = strpos($buffer, self::MLLP_END);
                    if ($endPos !== false) {
                        return substr($buffer, 0, $endPos);
                    }
                }
            }
        }

        return $started ? $buffer : null;
    }
}
