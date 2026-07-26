<?php

namespace App\Http\Controllers\Api\PushNotification;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterDeviceTokenRequest;
use App\Http\Resources\DeviceTokenResource;
use App\Models\DeviceToken;

class DeviceTokenController extends Controller
{
    /**
     * Register or update a device token.
     *
     * @param RegisterDeviceTokenRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterDeviceTokenRequest $request)
    {
        $userId = $request->user()->id;

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'device_token' => $request->device_token,
            ],
            [
                'user_id' => $userId,
                'platform' => $request->platform,
                'device_name' => $request->device_name,
                'app_version' => $request->app_version,
                'is_active' => true,
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Device token registered successfully.',
            'data' => new DeviceTokenResource($deviceToken)
        ], 200);
    }
}
