<?php

namespace App\Services;

use App\Mail\TwoFactorCodeMail;
use App\Models\TwoFactorDevice;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TwoFactorService
{
    /** How many minutes a verification code stays valid. */
    public const CODE_VALIDITY_MINUTES = 10;

    /** How many days a device stays trusted before the challenge is required again. */
    public const TRUST_DEVICE_DAYS = 30;

    /** Cookie storing the trusted-device token for the current browser. */
    public const TRUST_COOKIE_NAME = 'two_factor_trusted_device';

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

    /**
     * Whether the token held by this browser belongs to a non-expired,
     * remembered device of the user.
     */
    public function isTrustedDevice(User $user, ?string $token): bool
    {
        if (! $token) {
            return false;
        }

        return TwoFactorDevice::query()
            ->where('user_id', $user->id)
            ->where('device_token', $token)
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Remember the current device for the user and return its token.
     * Any previous device token for this browser is replaced.
     */
    public function trustDevice(User $user, ?string $previousToken = null): string
    {
        $token = Str::random(64);

        if ($previousToken) {
            TwoFactorDevice::query()
                ->where('user_id', $user->id)
                ->where('device_token', $previousToken)
                ->delete();
        }

        TwoFactorDevice::create([
            'user_id' => $user->id,
            'device_token' => $token,
            'expires_at' => now()->addDays(self::TRUST_DEVICE_DAYS),
        ]);

        return $token;
    }

    /** Forget every remembered device of the user (e.g. when 2FA is disabled). */
    public function revokeAllDevices(User $user): void
    {
        TwoFactorDevice::query()
            ->where('user_id', $user->id)
            ->delete();
    }
}
