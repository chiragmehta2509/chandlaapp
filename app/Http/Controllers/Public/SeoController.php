<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class SeoController extends Controller
{
    /**
     * Plain-text robots.txt — keeps crawl rules aligned with APP_URL when deployed.
     */
    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /client/login',
            'Disallow: /client/register',
            'Disallow: /client/forgot-password',
            'Disallow: /client/reset-password/',
            'Disallow: /client/dashboard',
            'Disallow: /client/*',
            'Disallow: /admin/',
            'Disallow: /admin/*',
            'Disallow: /payment/*',
            'Disallow: /e/*/gpay/',
            'Disallow: /webhooks/',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    /**
     * Minimal XML sitemap for public marketing/legal URLs.
     */
    public function sitemap()
    {
        $urls = [
            url('/'),
            route('public.contact'),
            route('public.privacy'),
            route('public.terms'),
            url('/packages/celebration-pack'),
            url('/packages/guest-pay'),
            url('/packages/host-duo'),
            url('/packages/family-pack'),
            url('/packages/complete-host'),
        ];

        return response()
            ->view('public.sitemap-xml', compact('urls'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    /**
     * Public marketing contact (no auth).
     */
    public function contact()
    {
        return view('public.contact');
    }

    /**
     * SEO landing pages for specific pricing packages.
     */
    public function package(string $slug)
    {
        $packages = [
            'celebration-pack' => [
                'title' => 'Celebration Plan — Invitation Card Layouts & Video Invitation Maker',
                'description' => 'Unlock 10 beautiful digital wedding invitation card layouts, story-ready video invitation templates, and milestone card maker with Chandla Book Celebration Plan.',
                'keywords' => 'wedding invitation templates, wedding video invitation maker, milestone cards, save the date card generator, invitation templates india',
            ],
            'guest-pay' => [
                'title' => 'Guest Contribution — Direct UPI & Google Pay Event Payments',
                'description' => 'Collect gift payments from guests directly to your personal UPI ID or GPay with zero commission. Unlocks unlimited ledger entries and PDF downloads for one event.',
                'keywords' => 'direct Guest Contributionment, wedding upi scanner, zero commission wedding gift, digital envelope collection, smart gift ledger',
            ],
            'host-duo' => [
                'title' => 'Host Plus Plan — Smart Chandla Ledger for Two Events',
                'description' => 'Manage digital cash collection records, envelope covers, and balances across two events with unlimited entries and full PDF download.',
                'keywords' => 'digital chandla book, cash collection tracker, cover balance envelope, multiple events ledger, digital ledger book',
            ],
            'family-pack' => [
                'title' => 'Family Plan — Collaborative Event Ledger for Joint Indian Families',
                'description' => 'Add up to 3 family members as co-managers to collaboratively edit and update the digital gift ledger across two events with unlimited entries.',
                'keywords' => 'family ledger share, wedding organizer sub accounts, joint family cash manager, collaborative wedding planning',
            ],
            'complete-host' => [
                'title' => 'Premium Host Plan — All-in-One Invitation and Event Ledger Bundle',
                'description' => 'The ultimate bundle: Unlocks all 10 digital invitation card layouts, story video invitation maker, milestone card studio, and 3 unlimited event ledgers.',
                'keywords' => 'wedding planning bundle, event invitation and ledger pack, indian wedding organizer app, custom invitation card maker',
            ],
        ];

        if (!array_key_exists($slug, $packages)) {
            return redirect()->route('public.home');
        }

        $pack = $packages[$slug];
        $seoTitle = $pack['title'];
        $seoDesc = $pack['description'];
        $seoKeywords = $pack['keywords'];

        return view('public.home', compact('seoTitle', 'seoDesc', 'seoKeywords'));
    }
}
