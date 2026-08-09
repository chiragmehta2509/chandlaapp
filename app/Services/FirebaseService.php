<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\DeviceToken;

class FirebaseService
{
    /**
     * @var string|null
     */
    protected $projectId;

    /**
     * @var string
     */
    protected $credentialsPath;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials_path');

        // Resolve relative paths (e.g. 'storage/app/firebase-credentials.json')
        if (!str_starts_with($credentialsPath, '/') && !str_contains($credentialsPath, ':')) {
            $credentialsPath = base_path($credentialsPath);
        }

        $this->credentialsPath = $credentialsPath;
        $this->projectId = config('firebase.project_id');
    }

    /**
     * Get OAuth2 Access Token.
     *
     * @return string|null
     */
    protected function getAccessToken(): ?string
    {
        if (!file_exists($this->credentialsPath)) {
            Log::error("Firebase service account credentials file not found at: {$this->credentialsPath}");
            return null;
        }

        try {
            $client = new GoogleClient();
            $client->setAuthConfig($this->credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            
            $token = $client->getAccessToken();
            
            // Extract project_id from credentials file if not set in config
            if (empty($this->projectId)) {
                $credentials = json_decode(file_get_contents($this->credentialsPath), true);
                $this->projectId = $credentials['project_id'] ?? null;
            }

            return $token['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error("Error generating Firebase OAuth2 access token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send FCM message.
     *
     * @param string $deviceToken
     * @param array $payload
     * @return bool
     */
    public function sendNotification(string $deviceToken, array $payload): bool
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error("Failed to send FCM notification: Access token is empty.");
            return false;
        }

        if (empty($this->projectId)) {
            Log::error("Failed to send FCM notification: Firebase Project ID is not configured.");
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        // Build the HTTP v1 API payload
        $body = [
            'message' => [
                'token' => $deviceToken,
                'notification' => [
                    'title' => $payload['title'],
                    'body' => $payload['body'],
                ],
                'data' => [
                    'title' => $payload['title'],
                    'body' => $payload['body'],
                    'image' => $payload['image'] ?? '',
                    'action_type' => $payload['action_type'] ?? 'none',
                    'action_value' => $payload['action_value'] ?? '',
                ],
            ]
        ];

        // Include image if present (HTTP v1 supports direct image urls for android and apns)
        if (!empty($payload['image'])) {
            $body['message']['android'] = [
                'notification' => [
                    'image' => $payload['image']
                ]
            ];
            $body['message']['apns'] = [
                'payload' => [
                    'aps' => [
                        'mutable-content' => 1
                    ]
                ],
                'fcm_options' => [
                    'image' => $payload['image']
                ]
            ];
            $body['message']['webpush'] = [
                'headers' => [
                    'image' => $payload['image']
                ]
            ];
        }

        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, $body);

            if ($response->successful()) {
                return true;
            }

            Log::error("FCM HTTP v1 Error response: " . $response->body());
            
            // Check for invalid registration/token error to deactivate or delete token
            $error = $response->json('error');
            if (isset($error['status']) && ($error['status'] === 'UNREGISTERED' || $error['status'] === 'INVALID_ARGUMENT')) {
                // Token is no longer valid, deactivate it
                $tokenCol = DeviceToken::getTokenColumn();
                DeviceToken::where($tokenCol, $deviceToken)->update(['is_active' => false]);
                Log::info("Deactivated invalid device token: {$deviceToken}");
            }

            return false;
        } catch (\Exception $e) {
            Log::error("FCM HTTP v1 Exception: " . $e->getMessage());
            return false;
        }
    }
}
