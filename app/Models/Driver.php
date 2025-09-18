<?php

namespace App\Models;

use App\Models\GrayCard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

        public function grayCard()
    {
        return $this->hasOne(GrayCard::class);
    }

    /**
     * Get the user that owns the driver.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function Insurancess()
    {
        return $this->hasMany(Insurances::class);
    }

    /**
     * Get the company that owns the driver.
     */
    public function company()
    {
        return $this->belongsTo(Companies::class);
    }

    public function lessons()
{
    return $this->hasMany(DriverLesson::class);
}

    /**
     * Accessor for full name.
     */
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Accessor for decrypted phone number (formatted).
     */
    public function getFormattedPhoneAttribute()
    {
        $phone = $this->phone; // Automatically decrypted by Laravel
        // Simple phone formatting
        if (strlen($phone) === 10) {
            return '(' . substr($phone, 0, 3) . ') ' . substr($phone, 3, 3) . '-' . substr($phone, 6, 4);
        }
        return $phone;
    }

    /**
     * Accessor for decrypted address.
     */
    public function getDecryptedAddressAttribute()
    {
        return $this->address; // Automatically decrypted by Laravel
    }

    /**
     * Scope a query to only include drivers from a specific company.
     */
    public function scopeByCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include drivers created by typing.
     */
    public function scopeCreatedByTyping($query)
    {
        return $query->where('is_created_by_typing', true);
    }

    /**
     * Scope a query to only include drivers not created by typing.
     */
    public function scopeNotCreatedByTyping($query)
    {
        return $query->where('is_created_by_typing', false);
    }
}
