<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;


class Otp extends Model
{

    protected $fillable = ['value', 'valid'];

    // Define the valid duration (5 minutes in this case)
    const OTP_VALIDITY_DURATION = 5; // in minutes

    /**
     * Scope a query to only include valid OTPs.
     */
    public function scopeValid($query)
    {
        return $query->where('valid', true)
            ->where('created_at', '>=', Carbon::now()->subMinutes(self::OTP_VALIDITY_DURATION));
    }

    /**
     * Update validity based on the expiration time.
     */
    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($otp) {
            // Check if the OTP is expired based on its creation time
            if ($otp->created_at->diffInMinutes(Carbon::now()) > self::OTP_VALIDITY_DURATION) {
                // Update the OTP to invalid if expired
                $otp->valid = false;
                $otp->save(); // Save the invalid status before returning the record
            }
        });
    }
}
