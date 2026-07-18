<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\Contact;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (empty($user->referral_code)) {
            do {
                $code = strtoupper(Str::random(8));
            } while (User::where('referral_code', $code)->exists());
            $user->referral_code = $code;
            $user->save();
        }
        
        $stats = [
            'total_events' => Event::whereIn('user_id', $user->allowedUserIds())->count(),
            'upcoming_events' => Event::whereIn('user_id', $user->allowedUserIds())
                ->where('event_date', '>=', now()->toDateString())
                ->where('is_archived', false)
                ->count(),
            'total_contacts' => Contact::whereIn('user_id', $user->allowedUserIds())->count(),
            'favorite_contacts' => Contact::whereIn('user_id', $user->allowedUserIds())->where('is_favorite', true)->count(),
            'total_entries' => Chandla::whereIn('user_id', $user->allowedUserIds())->count(),
            'total_invitations' => Invitation::whereHas('event', function($q) use ($user) {
                $q->whereIn('user_id', $user->allowedUserIds());
            })->count(),
            'free_event_credits' => $user->free_event_credits ?? 0,
        ];

        $globalFreeLimit = 50;
        $hasPaidPlan = Event::whereIn('user_id', $user->allowedUserIds())
            ->where('pricing_plan', '!=', 'free')
            ->exists()
            || ($user->planLevel() > 0)
            || (($user->free_event_credits ?? 0) > 0)
            || \App\Models\UPITransaction::where('user_id', $user->id)->where('status', 'completed')->exists()
            || \App\Models\PaymentTransaction::where('user_id', $user->id)->where('status', \App\Models\PaymentTransaction::STATUS_SUCCESS)->exists()
            || \App\Models\MarriageInvitation::whereIn('user_id', $user->allowedUserIds())->whereNotNull('paid_at')->exists();
        $freePlanUsedEntries = Chandla::whereHas('event', function ($q) use ($user) {
            $q->whereIn('user_id', $user->allowedUserIds())
                ->where('pricing_plan', 'free');
        })->count();

        $stats['global_free_limit_total'] = $globalFreeLimit;
        $stats['global_free_limit_used'] = $freePlanUsedEntries;
        $stats['global_free_limit_remaining'] = max(0, $globalFreeLimit - $freePlanUsedEntries);
        $stats['show_global_free_limit'] = !$hasPaidPlan;
        $stats['show_free_limit_download'] = !$hasPaidPlan && $freePlanUsedEntries > 0;

        $upgradeEvent = Event::whereIn('user_id', $user->allowedUserIds())
            ->where('is_archived', false)
            ->orderBy('event_date', 'desc')
            ->first();

        $dashboardQuickEvents = Event::whereIn('user_id', $user->allowedUserIds())
            ->where('is_archived', false)
            ->orderBy('event_date', 'desc')
            ->limit(24)
            ->get();

        $upcoming_events = Event::whereIn('user_id', $user->allowedUserIds())
            ->where('event_date', '>=', now()->toDateString())
            ->where('is_archived', false)
            ->orderBy('event_date', 'asc')
            ->limit(5)
            ->get();

        $recent_contacts = Contact::whereIn('user_id', $user->allowedUserIds())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $chartEvents = Event::whereIn('user_id', $user->allowedUserIds())
            ->orderBy('event_date', 'desc')
            ->limit(8)
            ->get();

        $eventIds = $chartEvents->pluck('id')->all();
        $totals = Chandla::select('event_id', 'category', DB::raw('SUM(amount) as total'))
            ->whereIn('event_id', $eventIds)
            ->groupBy('event_id', 'category')
            ->get()
            ->groupBy('event_id');

        $labels = [];
        $cashTotals = [];
        $coverTotals = [];
        $giftTotals = [];
        $cashCounts = [];
        $coverCounts = [];
        $giftCounts = [];

        // Also fetch counts per category per event
        $counts = Chandla::select('event_id', 'category', DB::raw('COUNT(*) as cnt'))
            ->whereIn('event_id', $eventIds)
            ->groupBy('event_id', 'category')
            ->get()
            ->groupBy('event_id');

        foreach ($chartEvents as $event) {
            $labels[] = $event->title;
            $group  = $totals->get($event->id, collect());
            $cgroup = $counts->get($event->id, collect());

            $cashTotals[]  = (float) ($group->firstWhere('category', 'chandla')->total ?? 0);
            $coverTotals[] = (float) ($group->firstWhere('category', 'cover')->total  ?? 0);
            $giftTotals[]  = (float) ($group->firstWhere('category', 'gift')->total   ?? 0);

            $cashCounts[]  = (int) ($cgroup->firstWhere('category', 'chandla')->cnt ?? 0);
            $coverCounts[] = (int) ($cgroup->firstWhere('category', 'cover')->cnt  ?? 0);
            $giftCounts[]  = (int) ($cgroup->firstWhere('category', 'gift')->cnt   ?? 0);
        }

        $chartData = [
            'labels' => $labels,
            'cash' => $cashTotals,
            'cover' => $coverTotals,
            'gift' => $giftTotals,
            'cash_count' => $cashCounts,
            'cover_count' => $coverCounts,
            'gift_count' => $giftCounts,
        ];

        $eventBreakdown = [];
        foreach ($chartEvents as $index => $event) {
            $eventBreakdown[] = [
                'id' => $event->id,
                'title' => $event->title,
                'cash' => $cashTotals[$index] ?? 0,
                'cover' => $coverTotals[$index] ?? 0,
                'gift' => $giftTotals[$index] ?? 0,
                'cash_count' => $cashCounts[$index] ?? 0,
                'cover_count' => $coverCounts[$index] ?? 0,
                'gift_count' => $giftCounts[$index] ?? 0,
            ];
        }

        $allTotals = [
            'cash'        => array_sum($cashTotals),
            'cover'       => array_sum($coverTotals),
            'gift'        => array_sum($giftTotals),
            'cash_count'  => array_sum($cashCounts),
            'cover_count' => array_sum($coverCounts),
            'gift_count'  => array_sum($giftCounts),
        ];

        return view('client.dashboard', compact(
            'stats',
            'dashboardQuickEvents',
            'upcoming_events',
            'recent_contacts',
            'user',
            'chartData',
            'eventBreakdown',
            'allTotals',
            'upgradeEvent'
        ));
    }
}
