<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'address',
        'postal_code',
        'city',
        'country',
        'phone',
        'is_created_by_typing',
        'user_id',
        'company_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_created_by_typing' => 'boolean',
        'date_of_birth' => 'date',
        'phone' => 'encrypted',
        'address' => 'encrypted',

    ];

}
