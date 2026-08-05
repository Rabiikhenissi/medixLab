<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TwoFactorService
{
    /** How many minutes a verification code stays valid. */
    public const CODE_VALIDITY_MINUTES = 10;

    /** Generate a fresh 6-digit one-time password. */
    public function generateCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    /** Whether the given code matches the user's current pending code and is not expired. */
    public function isCodeValid(User $user, string $code): bool
    {
        if (! $user->two_factor_code || ! $user->two_factor_code_expires_at) {
            return false;
        }

        return $user->two_factor_code_expires_at->isFuture()
            && Hash::check(trim($code), (string) $user->two_factor_code);
    }

    /** Generate a code, store its hash (with expiry) on the user and email the plain code. */
    public function sendCode(User $user): void
    {
        $code = $this->generateCode();

        $user->update([
            'two_factor_code' => Hash::make($code),
            'two_factor_code_expires_at' => now()->addMinutes(self::CODE_VALIDITY_MINUTES),
        ]);

        Mail::to($user->email)
            ->send(new TwoFactorCodeMail($code, self::CODE_VALIDITY_MINUTES));
    }

    /** Invalidate the user's pending code. */
    public function clearCode(User $user): void
    {
        if (! $user->two_factor_code && ! $user->two_factor_code_expires_at) {
            return;
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_code_expires_at' => null,
        ]);
    }
}
