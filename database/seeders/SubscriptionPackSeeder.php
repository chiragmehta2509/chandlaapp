<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPack;

class SubscriptionPackSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            [
                'slug'        => 'starter',
                'name'        => 'Starter Plan',
                'amount_inr'  => 0,
                'min_level'   => 0,
                'description' => 'Perfect for small, intimate family events and basic ledger management.',
                'badge'       => 'Starter',
                'is_popular'  => false,
                'features'    => [
                    '1 Event Limit',
                    'Up to 50 Gift/Chandla Entries',
                    'Basic Ledger Management',
                    'Standard PDF Export',
                    'Cash & Cover Tracking',
                    '3 Family Viewers (Read Only)'
                ],
                'limits'      => [
                    'events' => 1,
                    'entries' => 50,
                    'qrCollection' => false,
                    'editors' => 0
                ]
            ],
            [
                'slug'        => 'celebration',
                'name'        => 'Celebration Pack',
                'amount_inr'  => 300,
                'min_level'   => 1,
                'description' => 'Enhance your celebration with printable invitations and graphic studio assets.',
                'badge'       => 'Best Value',
                'is_popular'  => false,
                'features'    => [
                    '10 Invitation Templates',
                    'Printable Invitation Designs',
                    'Event Story / Reel Video Creator',
                    'Countdown Studio Access',
                    'Event Graphics & Social Posts',
                    'Everything in Starter Plan'
                ],
                'limits'      => [
                    'events' => 1,
                    'entries' => 50,
                    'qrCollection' => false,
                    'editors' => 0
                ]
            ],
            [
                'slug'        => 'guest_pay_single',
                'name'        => 'Guest Contribution',
                'amount_inr'  => 400,
                'min_level'   => 2,
                'description' => 'Direct payment collections and unlimited ledger entries for your single event.',
                'badge'       => 'Recommended',
                'is_popular'  => false,
                'features'    => [
                    'Personal UPI / QR Collection',
                    'Guest Payment Tracking',
                    'Unlimited Entries (1 Event)',
                    'Collection Status Reports',
                    'Full Event PDF',
                    'Everything in Celebration Pack'
                ],
                'limits'      => [
                    'events' => 1,
                    'entries' => 999999,
                    'qrCollection' => true,
                    'editors' => 0
                ]
            ],
            [
                'slug'        => 'ledger_duo',
                'name'        => 'Host Plus Plan',
                'amount_inr'  => 500,
                'min_level'   => 3,
                'description' => 'Manage multiple events with unlimited ledger logs and hosting tools.',
                'badge'       => 'Great Value',
                'is_popular'  => false,
                'features'    => [
                    'Up to 2 Events',
                    'Unlimited Entries (All Events)',
                    'Advanced Financial Reports',
                    'Full Event PDF Downloads',
                    'Additional Hosting & Seating Tools',
                    'Everything in Guest Contribution'
                ],
                'limits'      => [
                    'events' => 2,
                    'entries' => 999999,
                    'qrCollection' => true,
                    'editors' => 0
                ]
            ],
            [
                'slug'        => 'family',
                'name'        => 'Family Plan',
                'amount_inr'  => 600,
                'min_level'   => 4,
                'description' => 'Coordinate family functions together with multi-editor read/write accounts.',
                'badge'       => 'Family Pick',
                'is_popular'  => false,
                'features'    => [
                    '3 Family Editors (Write Access)',
                    'Shared Event Management Space',
                    'Joint Family Hosting Support',
                    'Role-Based Team Permissions',
                    'Everything in Host Plus Plan'
                ],
                'limits'      => [
                    'events' => 2,
                    'entries' => 999999,
                    'qrCollection' => true,
                    'editors' => 3
                ]
            ],
            [
                'slug'        => 'premium_bundle',
                'name'        => 'Premium Host',
                'amount_inr'  => 700,
                'min_level'   => 5,
                'description' => 'Our flagship plan. Elevates everything with premium custom templates and reports.',
                'badge'       => 'Most Popular',
                'is_popular'  => true,
                'features'    => [
                    'Up to 3 Events',
                    'Premium Invitation Templates',
                    'Premium Video / Reels Templates',
                    'Priority Email & Chat Support',
                    'Full Data Export & Email Reports',
                    'Everything in Family Plan'
                ],
                'limits'      => [
                    'events' => 3,
                    'entries' => 999999,
                    'qrCollection' => true,
                    'editors' => 3
                ]
            ],
            [
                'slug'        => 'professional',
                'name'        => 'Professional',
                'amount_inr'  => 999,
                'min_level'   => 6,
                'description' => 'For power users and professional coordinators running multiple large events.',
                'badge'       => 'Professional',
                'is_popular'  => false,
                'features'    => [
                    'Up to 10 Events',
                    'Unlimited Family Editors',
                    'Advanced Analytics Dashboard',
                    'Custom Branding (Remove logo)',
                    'Event Backup & Restore Utilities',
                    'Premium Support Channel',
                    'Everything in Premium Host'
                ],
                'limits'      => [
                    'events' => 10,
                    'entries' => 999999,
                    'qrCollection' => true,
                    'editors' => 999
                ]
            ],
            [
                'slug'        => 'enterprise',
                'name'        => 'Enterprise',
                'amount_inr'  => 9999,
                'min_level'   => 7,
                'description' => 'Bespoke integration, white labeling, and dedicated hosting for large organizations.',
                'badge'       => 'Enterprise',
                'is_popular'  => false,
                'features'    => [
                    'Unlimited Events & Editors',
                    'Organization Dashboard & Team Management',
                    'White Label & Custom Domain Solution',
                    'Full REST API Integrations',
                    'Dedicated Account Manager',
                    'Everything in Professional'
                ],
                'limits'      => [
                    'events' => 9999,
                    'entries' => 999999,
                    'qrCollection' => true,
                    'editors' => 999
                ]
            ],
        ];

        foreach ($packs as $packData) {
            SubscriptionPack::updateOrCreate(
                ['slug' => $packData['slug']],
                $packData
            );
        }
    }
}
