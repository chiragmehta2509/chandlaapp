<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    /**
     * GET /webhooks/whatsapp
     *
     * Meta calls this URL to verify the webhook when you register it
     * in the Developer Console. It sends three query params:
     *   hub.mode        = "subscribe"
     *   hub.verify_token = the token you set in the console
     *   hub.challenge   = a random string you must echo back
     */
    public function verify(Request $request)
    {
        $mode        = $request->query('hub_mode');        // underscore in Laravel
        $token       = $request->query('hub_verify_token');
        $challenge   = $request->query('hub_challenge');

        // Also try dot notation (Meta sends hub.mode, hub.verify_token)
        if (!$mode) {
            $mode      = $request->query('hub.mode');
            $token     = $request->query('hub.verify_token');
            $challenge = $request->query('hub.challenge');
        }

        $configuredToken = config('services.whatsapp.verify_token', 'chandlabook_whatsapp_verify');

        if ($mode === 'subscribe' && $token === $configuredToken) {
            Log::info('WhatsApp webhook verified successfully.');
            return response($challenge, 200)->header('Content-Type', 'text/plain');
        }

        Log::warning('WhatsApp webhook verification failed.', [
            'mode'  => $mode,
            'token' => $token,
        ]);

        return response('Forbidden', 403);
    }

    /**
     * POST /webhooks/whatsapp
     *
     * Receives incoming WhatsApp messages, status updates, etc.
     * For now we just log the payload — extend as needed.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        Log::info('WhatsApp webhook received', ['payload' => $payload]);

        // Example: Handle incoming messages
        $entry = $payload['entry'][0] ?? null;
        if ($entry) {
            $changes = $entry['changes'][0] ?? null;
            $value   = $changes['value'] ?? null;

            // Incoming message
            if (!empty($value['messages'])) {
                foreach ($value['messages'] as $message) {
                    $from = $message['from'] ?? 'unknown';
                    $text = $message['text']['body'] ?? '[non-text message]';
                    Log::info("WhatsApp message from {$from}: {$text}");
                }
            }

            // Message status update (sent, delivered, read, failed)
            if (!empty($value['statuses'])) {
                foreach ($value['statuses'] as $status) {
                    Log::info('WhatsApp status update', [
                        'id'        => $status['id'],
                        'status'    => $status['status'],
                        'recipient' => $status['recipient_id'] ?? null,
                    ]);
                }
            }
        }

        // Always return 200 OK to acknowledge receipt
        return response()->json(['status' => 'ok'], 200);
    }
}
