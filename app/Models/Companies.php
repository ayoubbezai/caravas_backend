<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number', // encrypted
        'name_of_company',
        'user_id',
    ];


    public function Insurances()
    {
        return $this->hasMany(Insurances::class);
    }

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'phone_number' => 'encrypted', // Only encrypt phone number
    ];

    /**
     * Get the user that owns the company.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for full name
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Accessor for formatted phone number (decrypted automatically)
     */
    public function getFormattedPhoneNumberAttribute()
    {
        $phone = $this->phone_number; // Automatically decrypted by Laravel
        // Simple phone formatting example
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
        }
        return $phone;
    }
}
