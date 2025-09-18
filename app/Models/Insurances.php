<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insurances extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'insurances';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'policy_number',
        'company_name',
        'valid_from',
        'valid_until',
        'driver_id',
        'company_id',
        'agency_name',
        'agency_address',
        'agency_phone',
        'is_created_by_typing',
    ];

    /**
     * Cast attributes to appropriate types.
     */
    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_created_by_typing' => 'boolean',
    ];

    /**
     * Insurance belongs to a Driver.
     */
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Insurance belongs to a Company.
     */
    public function company()
    {
        return $this->belongsTo(Companies::class);
    }
}
