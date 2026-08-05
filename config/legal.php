<?php

return [
    // Version of the documents currently in force. Bump a version after
    // editing the corresponding page so users are prompted to re-consent.
    'terms_version' => '1.0',
    'privacy_version' => '1.0',

    // Retention period in days before fully-anonymised accounts are purged.
    'retention_days' => env('GDPR_RETENTION_DAYS', 90),

    // Validity of an invitation link sent to a new account, in days.
    'invite_days' => env('INVITE_DAYS', 7),

    // Legal entity information displayed on the mentions légales page.
    'company_name' => env('APP_NAME', 'Medix eSanté'),
    'company_email' => env('MAIL_FROM_ADDRESS', 'contact@medix-esante.com'),
    'company_country' => 'Tunisie',
];
