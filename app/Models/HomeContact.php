<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomeContact extends Model
{
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'leader_id',
        'date',
        'phone',
        'notes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Get the leader assigned to this home contact shift.
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(Leader::class);
    }
}
