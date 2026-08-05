<?php

namespace App\Models;

use App\Mail\VerificationMail;
use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;

#[Fillable(['first_name', 'last_name', 'email', 'email_verified_at', 'password', 'phone', 'group_id', 'address', 'is_archive', 'last_login_at', 'two_factor_secret', 'two_factor_confirmed_at', 'two_factor_code', 'two_factor_code_expires_at'])]
#[Hidden(['password', 'remember_token', 'two_factor_secret'])]
/**
 * Authenticatable user account shared by every role (admin, doctor, patient, center staff).
 */
class User extends Authenticatable implements MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * Send the branded email-verification link.
     */
    public function sendEmailVerificationNotification(): void
    {
        if ($this->hasVerifiedEmail()) {
            return;
        }

        Mail::to($this->email)->send(new VerificationMail($this));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_archive' => 'boolean',
            'two_factor_secret' => 'encrypted',
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_code_expires_at' => 'datetime',
        ];
    }

    /** Whether two-factor authentication is configured for this account. */
    public function twoFactorEnabled(): bool
    {
        return filled($this->two_factor_confirmed_at);
    }

    /** The permission group this user belongs to. */
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    /** In-app notifications sent to this user. */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /** The admin profile attached to this account, if any. */
    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    /** The doctor profile attached to this account, if any. */
    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    /** The patient profile attached to this account, if any. */
    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    /** The center staff profile attached to this account, if any. */
    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    /** Whether the user's group is granted the given action code (cached per group). */
    public function hasPermission(string $actionCode): bool
    {
        if (! $this->group) {
            return false;
        }

        $actionCodes = cache()->remember("group_permissions_{$this->group_id}", 3600, function () {
            return $this->group->actions()->pluck('code')->toArray();
        });

        return in_array($actionCode, $actionCodes, true);
    }
}
