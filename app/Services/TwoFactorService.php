<?php

namespace App\Services;

use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorService
{
    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /** Generate a fresh base32 TOTP secret. */
    public function generateSecret(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /** Build an otpauth:// URL used by authenticator apps. */
    public function otpauthUrl(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl('MedixLab', $email, $secret);
    }

    /** Return an inline SVG data-URI QR code for the provisioning URL. */
    public function qrCodeSvg(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeInline('MedixLab', $email, $secret);
    }

    /** Validate a 6-digit TOTP code against a secret, tolerating clock drift. */
    public function verify(string $secret, string $code): bool
    {
        return $this->google2fa->verifyKey($secret, trim($code), 1);
    }
}
