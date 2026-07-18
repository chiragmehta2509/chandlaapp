<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Contact;
use App\Models\Entry;
use App\Models\Invitation;
use App\Models\UPITransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::where('is_deleted', false)->count(),
            'active_users' => User::where('is_active', true)->where('is_deleted', false)->count(),
            'premium_users' => User::where('subscription_status', 'premium')
                ->where('subscription_expires_at', '>', now())
                ->count(),
            'total_events' => Event::count(),
            'active_events' => Event::where('is_archived', false)->count(),
            'total_contacts' => Contact::count(),
            'total_entries' => Entry::count(),
            'total_invitations' => Invitation::count(),
            'total_payments' => UPITransaction::where('status', 'completed')->count(),
            'revenue' => UPITransaction::where('status', 'completed')->sum('amount'),
        ];

        $recent_users = User::where('is_deleted', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recent_events = Event::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recent_payments = UPITransaction::with('user')
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_users', 'recent_events', 'recent_payments'));
    }
}
