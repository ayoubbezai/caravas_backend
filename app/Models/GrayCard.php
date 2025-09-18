<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrayCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'card_number',
        'car_name',
        'car_type'
    ];

    /**
     * Get the driver that owns the gray card.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
