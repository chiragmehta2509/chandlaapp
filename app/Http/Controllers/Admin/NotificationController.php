<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $notifications = \App\Models\Notification::with('creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        // Get user IDs that have at least one active device token
        $usersWithTokens = \App\Models\DeviceToken::where('is_active', true)
            ->whereNotNull('device_token')
            ->where('device_token', '!=', '')
            ->pluck('user_id')
            ->toArray();

        // Get all active users
        $users = User::select('id', 'name', 'phone')
            ->active()
            ->orderBy('name')
            ->get()
            ->map(function ($user) use ($usersWithTokens) {
                $user->has_token = in_array($user->id, $usersWithTokens);
                return $user;
            });

        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,plan_wise,specific_users',
            'plan_level' => 'required_if:target_audience,plan_wise|integer|min:0|max:7',
            'specific_user_ids' => 'required_if:target_audience,specific_users|array',
            'specific_user_ids.*' => 'integer|exists:users,id',
        ]);

        $data = [
            'title' => $request->title,
            'message' => $request->message,
            'action_type' => 'none',
            'target_audience' => $request->target_audience,
        ];

        if ($request->target_audience === 'all') {
            $data['send_to'] = 'all';
            $data['target_data'] = null;
        } elseif ($request->target_audience === 'specific_users') {
            $data['send_to'] = 'selected_users';
            $data['user_ids'] = $request->specific_user_ids;
            $data['target_data'] = ['user_ids' => $request->specific_user_ids];
        } else {
            $data['send_to'] = 'selected_users';
            
            // Find users matching the specified plan level
            $planLevel = (int) $request->plan_level;
            
            // Using chunks to be memory efficient when evaluating the PHP-based planLevel() method
            $userIds = collect();
            
            User::active()->chunk(1000, function ($users) use ($planLevel, $userIds) {
                foreach ($users as $user) {
                    if ($user->planLevel() === $planLevel) {
                        $userIds->push($user->id);
                    }
                }
            });

            if ($userIds->isEmpty()) {
                return back()->with('error', 'No users found for the selected plan.')->withInput();
            }

            $data['user_ids'] = $userIds->toArray();
            $data['target_data'] = ['plan_level' => $planLevel];
        }

        try {
            $this->notificationService->send($data, Auth::guard('web')->id());
            return back()->with('success', 'Push notifications sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send notifications: ' . $e->getMessage())->withInput();
        }
    }
}
