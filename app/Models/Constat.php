<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Constat extends Model
{
    protected $fillable = [
        'pdf_url',
        'pdf_hash',
        'latitude',
        'longitude',
        'driver_a_id',
        'driver_b_id',
        'company_a_id',
        'company_b_id',
        'attachments_urls', // new field for attachments
    ];

    protected $casts = [
        'attachments_urls' => 'array', // automatically cast JSON to array
    ];

    // Relationships
    public function driverA()
    {
        return $this->belongsTo(Driver::class, 'driver_a_id');
    }

    public function driverB()
    {
        return $this->belongsTo(Driver::class, 'driver_b_id');
    }

    public function companyA()
    {
        return $this->belongsTo(Companies::class, 'company_a_id');
    }

    public function companyB()
    {
        return $this->belongsTo(Companies::class, 'company_b_id');
    }

    // Scope to get constats for a specific driver
    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_a_id', $driverId)
                     ->orWhere('driver_b_id', $driverId);
    }
}
