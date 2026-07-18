<?php

namespace App\Support;

use App\Models\User;

final class RazorpayPayerUser
{
    public static function findFromPayment(array $payment): ?User
    {
        $email = isset($payment['email']) ? trim((string) $payment['email']) : '';
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $u = User::query()->where('email', $email)->first();
            if ($u) {
                return $u;
            }
        }

        $contact = $payment['contact'] ?? null;
        if ($contact === null || $contact === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', (string) $contact);
        if (strlen($digits) < 10) {
            return null;
        }

        $last10 = substr($digits, -10);

        return User::query()
            ->where(function ($q) use ($last10, $digits) {
                $q->where('phone', 'like', '%'.$last10)
                    ->orWhere('phone', 'like', '%'.$digits);
            })
            ->first();
    }
}
