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

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_audience' => 'required|in:all,plan_wise',
            'plan_level' => 'required_if:target_audience,plan_wise|integer|min:0|max:7',
        ]);

        $data = [
            'title' => $request->title,
            'message' => $request->message,
            'action_type' => 'none',
        ];

        if ($request->target_audience === 'all') {
            $data['send_to'] = 'all';
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
        }

        try {
            $this->notificationService->send($data, Auth::guard('web')->id());
            return back()->with('success', 'Push notifications sent successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send notifications: ' . $e->getMessage())->withInput();
        }
    }
}
