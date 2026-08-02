<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['first_name', 'last_name', 'email', 'password', 'phone', 'group_id', 'address', 'is_archive', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
/**
 * Authenticatable user account shared by every role (admin, doctor, patient, center staff).
 */
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
        ];
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
