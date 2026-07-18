<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;

class NotDisposableEmail implements ValidationRule
{
    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        if (! is_string($value) || ! str_contains($value, '@')) {
            return;
        }

        $domain = strtolower((string) substr(strrchr($value, '@'), 1));
        if ($domain === '') {
            return;
        }

        $blocked = config('disposable_email_domains', []);
        if (in_array($domain, $blocked, true)) {
            $fail('Disposable or temporary email addresses are not allowed. Use a real inbox you can access.');

            return;
        }

        // Obvious typo-domains sometimes used to bypass checks
        if (preg_match('/^(example|test|invalid)\.(com|org|net|test)$/i', $domain)) {
            $fail('Please enter a real email address.');

            return;
        }
    }
}
