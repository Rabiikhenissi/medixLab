<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Messages\MailMessage;

#[Fillable(['first_name', 'last_name', 'email', 'password', 'phone', 'group_id', 'address', 'is_archive', 'last_login_at'])]
#[Hidden(['password', 'remember_token'])]
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

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function doctor()
    {
        return $this->hasOne(Doctor::class);
    }

    public function patient()
    {
        return $this->hasOne(Patient::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function hasPermission(string $actionCode): bool
    {
        if (!$this->group) {
            return false;
        }

        $actionCodes = cache()->remember("group_permissions_{$this->group_id}", 3600, function () {
            return $this->group->actions()->pluck('code')->toArray();
        });

        return in_array($actionCode, $actionCodes, true);
    }
}
