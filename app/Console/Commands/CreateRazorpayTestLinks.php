<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Razorpay\Api\Api;

/**
 * Create ₹1 Razorpay Payment Links with a callback URL baked in,
 * for testing the post-payment redirect + auto-unlock flow.
 *
 * Run: php artisan razorpay:create-test-links --callback="http://127.0.0.1:8000/client/packs/thanks"
 *
 * Outputs 8 short.io links you can paste into .env to override the test URLs.
 * The Razorpay dashboard UI doesn't expose the callback_url field for Payment Links,
 * but the API does — that's the trick.
 */
class CreateRazorpayTestLinks extends Command
{
    protected $signature = 'razorpay:create-test-links
                            {--callback= : Full callback URL the customer is sent to after payment}
                            {--amount= : Specific fixed amount in rupees for all links (e.g. 1)}
                            {--actual : Use actual plan amounts from config (default if amount is not set)}
                            {--only=* : Specific plan keys to generate (e.g., marriage_inv, matrimonial_500, matrimonial_200)}';

    protected $description = 'Create Razorpay Payment Links per plan with callback URL for testing';

    /** Plan key → human label → reference id (used to identify the link in callbacks). */
    private array $plans = [
        'celebration'      => ['label' => 'Celebration test',       'ref' => 'test_celebration'],
        'ledger_duo'       => ['label' => 'Host Plus Plan test',          'ref' => 'test_ledger_duo'],
        'family'           => ['label' => 'Family test',            'ref' => 'test_family'],
        'premium_bundle'   => ['label' => 'Premium Host Plan test',     'ref' => 'test_premium_bundle'],
        'guest_pay_single' => ['label' => 'Guest Contribution test',         'ref' => 'test_guest_pay_single'],
        'marriage_inv'     => ['label' => 'Marriage invitation test','ref' => 'test_marriage_inv'],
        'matrimonial_500'  => ['label' => 'Matrimonial 500 test',   'ref' => 'test_matrimonial_500'],
        'matrimonial_200'  => ['label' => 'Matrimonial 200 test',   'ref' => 'test_matrimonial_200'],
    ];

    /** Mapping from plan key → which .env line should hold the resulting URL. */
    private array $envKeyMap = [
        'celebration'      => 'PACK_CELEBRATION_RAZORPAY_URL',
        'ledger_duo'       => 'PACK_LEDGER_DUO_RAZORPAY_URL',
        'family'           => 'PACK_FAMILY_RAZORPAY_URL',
        'premium_bundle'   => 'PACK_PREMIUM_BUNDLE_RAZORPAY_URL',
        'guest_pay_single' => 'PACK_GUEST_PAY_SINGLE_RAZORPAY_URL',
        'marriage_inv'     => 'MARRIAGE_INVITATION_RAZORPAY_LINK',
        'matrimonial_500'  => 'MATRIMONIAL_RAZORPAY_LINK_500',
        'matrimonial_200'  => 'MATRIMONIAL_RAZORPAY_LINK_200',
    ];

    private function getActualAmount(string $kind): float
    {
        return match ($kind) {
            'celebration'      => (float) config('packs.celebration.amount_inr', 300),
            'ledger_duo'       => (float) config('packs.ledger_duo.amount_inr', 500),
            'family'           => (float) config('packs.family.amount_inr', 600),
            'premium_bundle'   => (float) config('packs.premium_bundle.amount_inr', 700),
            'guest_pay_single' => (float) config('packs.guest_pay_single.amount_inr', 400),
            'marriage_inv'     => (float) config('marriage_invitations.amount', 300),
            'matrimonial_500'  => (float) config('matrimonial.plans.500.price_inr', 500),
            'matrimonial_200'  => (float) config('matrimonial.plans.200.price_inr', 200),
            default            => 1.0,
        };
    }

    public function handle(): int
    {
        $callback = (string) $this->option('callback');
        if ($callback === '') {
            $this->error('Provide --callback="<url>" (e.g. http://127.0.0.1:8000/client/packs/thanks)');
            return self::INVALID;
        }

        $key = (string) config('services.razorpay.key_id');
        $secret = (string) config('services.razorpay.key_secret');
        if ($key === '' || $secret === '') {
            $this->error('Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET in .env first.');
            return self::FAILURE;
        }

        $fixedAmount = $this->option('amount');
        $useActual = $this->option('actual') || ($fixedAmount === null);

        if ($fixedAmount !== null) {
            $amountRs = (float) $fixedAmount;
            $amountPaise = (int) round($amountRs * 100);
            if ($amountPaise < 100) {
                $this->error('Amount must be at least ₹1 (100 paise).');
                return self::INVALID;
            }
        }

        $only = $this->option('only');
        $plansToGenerate = $this->plans;
        if (!empty($only)) {
            $plansToGenerate = array_intersect_key($this->plans, array_flip($only));
        }

        $this->info('Creating ' . count($plansToGenerate) . ' Razorpay Payment Links…');
        $this->line('Callback URL: ' . $callback);
        $this->line('Using key: ' . substr($key, 0, 12) . '…');
        $this->newLine();

        $api = new Api($key, $secret);
        $results = [];
        $errors = [];

        foreach ($plansToGenerate as $kind => $meta) {
            try {
                // Sleep to prevent hitting Razorpay rate limits
                sleep(5);

                $amountRs = $useActual ? $this->getActualAmount($kind) : (float) $fixedAmount;
                $amountPaise = (int) round($amountRs * 100);

                $link = $api->paymentLink->create([
                    'amount' => $amountPaise,
                    'currency' => 'INR',
                    'accept_partial' => false,
                    'reference_id' => $meta['ref'] . '_' . substr(bin2hex(random_bytes(3)), 0, 6),
                    'description' => $meta['label'] . ' — ₹' . $amountRs . ' test payment',
                    'callback_url' => $callback,
                    'callback_method' => 'get',
                    'reminder_enable' => false,
                    'notes' => [
                        'plan_kind' => $kind,
                        'test_link' => 'true',
                    ],
                ]);

                $shortUrl = $link['short_url'] ?? null;
                if (!$shortUrl) {
                    throw new \RuntimeException('No short_url returned from Razorpay.');
                }

                $results[$kind] = [
                    'env' => $this->envKeyMap[$kind],
                    'url' => $shortUrl,
                    'id' => $link['id'] ?? '?',
                    'label' => $meta['label'],
                ];

                $this->line(sprintf('  ✔ %s (₹%d) → %s', str_pad($meta['label'], 25), $amountRs, $shortUrl));
            } catch (\Throwable $e) {
                $errors[$kind] = $e->getMessage();
                $this->line(sprintf('  ✗ %s → FAILED: %s', str_pad($meta['label'], 25), $e->getMessage()));
            }
        }

        $this->newLine();

        if (!empty($results)) {
            $this->info('=== Paste these into your .env ===');
            $this->newLine();
            foreach ($results as $kind => $r) {
                $this->line($r['env'] . '=' . $r['url']);
            }
            $this->newLine();
            // Also set the RAZORPAY_TEST_PAYMENT_LINK (used for event plan unlock) to the Host Plus Plan link.
            if (isset($results['ledger_duo'])) {
                $this->line('# Event plan unlock (programmatic) — reuses Host Plus Plan URL');
                $this->line('RAZORPAY_TEST_PAYMENT_LINK=' . $results['ledger_duo']['url']);
            }
            $this->newLine();
            $this->info('After updating .env, run: php artisan config:clear');
        }

        if (!empty($errors)) {
            $this->newLine();
            $this->warn('Some links failed:');
            foreach ($errors as $k => $err) {
                $this->line("  - {$k}: {$err}");
            }
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
