<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{

    public $timestamps = false;
    protected $fillable = [
        'email',
        'token',
        'created_at',
    ];
    protected $casts = [
        'created_at' => 'datetime',
    ];
    // Define the valid duration (5 minutes in this case)
    const VALIDITY_DURATION = 5; // in minutes

    /**
     * Scope a query to only include valid PASSWORD_RESET_TOKENs.
     */
    public function scopeValid($query)
    {
        return $query->where('valid', true)
            ->where('created_at', '>=', Carbon::now()->subMinutes(self::VALIDITY_DURATION));
    }

    /**
     * Update validity based on the expiration time.
     */
    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($password_reset_token) {
            // Check if the PASSWORD_RESET_TOKEN is expired based on its creation time
            if ($password_reset_token->created_at->diffInMinutes(Carbon::now()) > self::VALIDITY_DURATION) {
                // Update the PASSWORD_RESET_TOKEN to invalid if expired
                $password_reset_token->valid = false;
                $password_reset_token->save(); // Save the invalid status before returning the record
            }
        });
    }
}
