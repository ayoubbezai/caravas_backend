<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverLesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'driver_id',
        'last_name',
        'first_name',
        'date_of_birth',
        'address',
        'postal_code',
        'city',
        'country',
        'license_number',
        'license_category',
        'license_valid_until',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'date_of_birth' => 'date',
        'license_valid_until' => 'date',
        'address' => 'encrypted',
        'postal_code' => 'encrypted',
        'city' => 'encrypted',
    ];

    /**
     * Get the driver that owns the lesson.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
