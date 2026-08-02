<?php

return [
    'url' => env('MACHINE_URL', 'http://127.0.0.1:5000'),
    'mllp_port' => env('MACHINE_MLLP_PORT', 5001),
    'timeout' => env('MACHINE_TIMEOUT', 15),
    'enabled' => env('MACHINE_ENABLED', true),

    // default transport protocol: hl7_mllp (TCP), serial_hl7 (RS-232/USB) or http_json
    'protocol' => env('MACHINE_PROTOCOL', 'hl7_mllp'),

    // RS-232 / USB serial defaults (used when protocol is serial_hl7)
    'serial' => [
        'port' => env('MACHINE_SERIAL_PORT', PHP_OS_FAMILY === 'Windows' ? 'COM3' : '/dev/ttyUSB0'),
        'baud_rate' => env('MACHINE_BAUD_RATE', 9600),
        'data_bits' => env('MACHINE_DATA_BITS', 8),
        'stop_bits' => env('MACHINE_STOP_BITS', 1),
        'parity' => env('MACHINE_PARITY', 'N'),
    ],
];
