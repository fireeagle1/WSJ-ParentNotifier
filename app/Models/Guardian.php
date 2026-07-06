<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class Guardian extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    /**
     * The table associated with the model.
     */
    protected $table = 'parents';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'email',
        'young_person_name',
        'access_token',
        'is_revoked',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'access_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_revoked' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the notification logs for this parent.
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'parent_id');
    }

    /**
     * Determine if the parent has set a password.
     */
    public function hasPassword(): bool
    {
        return !empty($this->password);
    }
}
