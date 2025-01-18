<?php

namespace App\Services;

use App\Models\Otp;

use Illuminate\Support\Str;

class OtpService
{
    public function generate(): string
    {
        return Str::random(6);
    }

    public function getLatestOtp($userId): ?Otp
    {
        return Otp::where('user_id', $userId)
            ->latest()
            ->first();
    }

    public function invalidateOtp(Otp $otp)
    {
        return $otp->update(['valid' => false]);
    }

    public function isValidOtp(?Otp $otp, string $value): bool
    {
        return $otp && $otp->valid && $otp->value === $value;
    }

    public function verifyOtp($otpValue, $userId): array
    {
        $otp = $this->getLatestOtp($userId);
        return ['valid' => $this->isValidOtp($otp, $otpValue), 'otp' => $otp];
    }
}
