<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;
    protected string $baseUrl;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token', '');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id', '');
        $this->baseUrl = "https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages";
    }

    /**
     * Send a WhatsApp template message.
     *
     * @param string $to The recipient's phone number with country code (e.g. "919876543210")
     * @param string $templateName The name of the WhatsApp template
     * @param string $languageCode The language code (default: "en_US")
     * @param array $components The components array for the template variables (e.g., body parameters)
     * @return array|null Returns the API response as an array on success, or null on failure.
     */
    public function sendTemplateMessage(string $to, string $templateName, string $languageCode = 'en', array $components = []): ?array
    {
        if (empty($this->token) || empty($this->phoneNumberId)) {
            Log::error('WhatsApp credentials are not configured.');
            return null;
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode
                ]
            ]
        ];

        if (!empty($components)) {
            $payload['template']['components'] = $components;
        }

        try {
            $response = Http::withToken($this->token)
                ->post($this->baseUrl, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            $resJson = $response->json();
            $errorCode = $resJson['error']['code'] ?? null;

            // Handle Error 190 / 102 / 10: Expired or invalid access token
            // This means WHATSAPP_TOKEN in .env needs to be regenerated in Meta Business Manager.
            if (in_array($errorCode, [190, 102, 10], true)) {
                Log::critical('🚨 WhatsApp Access Token is EXPIRED or INVALID. '
                    . 'Go to Meta Business Manager → System Users → Generate New Token. '
                    . 'Then update WHATSAPP_TOKEN in .env and run: php artisan config:clear', [
                    'error_code'    => $errorCode,
                    'error_message' => $resJson['error']['message'] ?? 'unknown',
                    'phone_number_id' => $this->phoneNumberId,
                ]);
                return null;
            }

            // Handle Error 132001: Template name does not exist in the requested language
            // Automatically try alternate language codes (e.g., en_US <-> en <-> en_GB)
            if ($errorCode === 132001) {
                $fallbacks = match ($languageCode) {
                    'en'    => ['en_US', 'en_GB'],
                    'en_US' => ['en', 'en_GB'],
                    'en_GB' => ['en_US', 'en'],
                    default => ['en_US', 'en'],
                };

                foreach ($fallbacks as $fallbackCode) {
                    Log::info("WhatsApp template '{$templateName}' not found in '{$languageCode}'. Retrying with '{$fallbackCode}'...");
                    $payload['template']['language']['code'] = $fallbackCode;
                    $fallbackResp = Http::withToken($this->token)->post($this->baseUrl, $payload);
                    if ($fallbackResp->successful()) {
                        return $fallbackResp->json();
                    }
                }
            }

            Log::error('WhatsApp API Error', [
                'status'   => $response->status(),
                'response' => $resJson,
                'payload'  => $payload,
            ]);

        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception', [
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Helper to format a single body parameter.
     */
    public static function formatTextParameter(string $text): array
    {
        return [
            'type' => 'text',
            'text' => $text
        ];
    }
}
