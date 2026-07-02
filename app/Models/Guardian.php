<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasFactory;

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
        ];
    }

    /**
     * Get the notification logs for this parent.
     */
    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class, 'parent_id');
    }
}
