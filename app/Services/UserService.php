<?php

namespace App\Services;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function formatUser($user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'verified' => $user->hasVerifiedEmail(),
            'admin' => $user->is_admin,
            'deleted' => $user->deleted,

        ];
    }

    public function getUsers($deleted = false)
    {
        return User::where('deleted', $deleted)->get()->map(fn($user) => $this->formatUser($user));
    }

    public function updateUser(User $user, $userAttributes)
    {
        return $user->update($userAttributes);
    }
    public function deleteUser(User $user)
    {
        return $user->update(['deleted' => true]);
    }

    public function restoreUser(User $user)
    {
        return $user->update(['deleted' => false]);
    }

    public function verifyUserEmail(User $user)
    {
        return $user->update(['email_verified_at' => now()]);
    }

    public function createUserOtp(User $user, string $otp)
    {
        return $user->otps()->create([
            'value' => $otp,
        ]);
    }

    public function validateChangePassword(User $user, $currentPassword, $newPassword)
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return [
                'success' => false,
                'message' => 'Please provide correct current password.'
            ];
        }

        if ($currentPassword == $newPassword) {
            return [
                'success' => false,
                'message' => 'You cannot use past passwords.'
            ];
        }

        return ['success' => true];
    }

    public function changeUserPassword(User $user, $currentPassword, $newPassword)
    {
        $validation = $this->validateChangePassword($user, $currentPassword, $newPassword);

        if (!$validation['success']) {
            return $validation;
        }

        $updated = $user->update([
            'password' => $newPassword
        ]);

        return [
            'success' => $updated,
            'message' => $updated ? 'Password updated successfully.' : 'Failed to update password.'
        ];
    }

    public function getUserByEmail($email): ?User
    {
        return User::where('email', $email)->get()->first();
    }
}
