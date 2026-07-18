<?php

namespace App\Http\Controllers\Api\Invitation;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationShare;
use App\Models\Entry;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    /**
     * Helper: get the data owner user ID (supports family member accounts).
     */
    private function ownerId(Request $request): int
    {
        return $request->user()->dataOwnerId();
    }

    /**
     * Helper: query invitations scoped to the data owner's events.
     */
    private function ownerInvitations(Request $request)
    {
        $userId = $this->ownerId($request);
        return Invitation::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    /**
     * Helper: query events scoped to the data owner.
     */
    private function ownerEvents(Request $request)
    {
        $userId = $this->ownerId($request);
        return Event::where('user_id', $userId);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $query = $this->ownerInvitations($request)->with(['event', 'entry']);

        $invitations = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invitations
        ]);
    }

    public function getByEvent(Request $request, $eventId)
    {
        $event = $this->ownerEvents($request)->findOrFail($eventId);
        $invitations = $event->invitations()->with('entry')->get();

        return response()->json([
            'success' => true,
            'data' => $invitations
        ]);
    }

    public function show(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)
            ->with(['event', 'entry', 'shares'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $invitation
        ]);
    }

    public function getByCode(Request $request, $code)
    {
        $invitation = Invitation::where('invitation_code', $code)
            ->with(['event', 'entry'])
            ->firstOrFail();

        // Track open
        $invitation->increment('open_count');
        if (!$invitation->opened_at) {
            $invitation->update([
                'status' => 'opened',
                'opened_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $invitation
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'entry_id' => 'nullable|exists:entries,id',
            'type' => 'nullable|string|in:digital,pdf,image',
            'template_id' => 'nullable|string',
            'custom_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = $this->ownerEvents($request)->findOrFail($request->event_id);

        $invitation = Invitation::create([
            'event_id' => $request->event_id,
            'entry_id' => $request->entry_id,
            'invitation_code' => Str::random(32),
            'type' => $request->type ?? 'digital',
            'template_id' => $request->template_id,
            'custom_message' => $request->custom_message,
            'status' => 'draft',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invitation created successfully',
            'data' => $invitation->load(['event', 'entry'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|string|in:digital,pdf,image',
            'template_id' => 'nullable|string',
            'custom_message' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $invitation->update($request->only(['type', 'template_id', 'custom_message']));

        return response()->json([
            'success' => true,
            'message' => 'Invitation updated successfully',
            'data' => $invitation
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $invitation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Invitation deleted successfully'
        ]);
    }

    public function send(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:whatsapp,sms,email,link',
            'recipient' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Send invitation via platform
        // Integration with WhatsApp/SMS/Email would go here

        $invitation->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        InvitationShare::create([
            'invitation_id' => $invitation->id,
            'platform' => $request->platform,
            'recipient' => $request->recipient,
            'shared_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invitation sent successfully',
            'data' => $invitation
        ]);
    }

    public function sendBulk(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:whatsapp,sms,email',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        foreach ($request->recipients as $recipient) {
            InvitationShare::create([
                'invitation_id' => $invitation->id,
                'platform' => $request->platform,
                'recipient' => $recipient,
                'shared_at' => now(),
            ]);
        }

        $invitation->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invitations sent to ' . count($request->recipients) . ' recipients'
        ]);
    }

    public function share(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'platform' => 'required|string|in:whatsapp,sms,email,link',
            'recipient' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $share = InvitationShare::create([
            'invitation_id' => $invitation->id,
            'platform' => $request->platform,
            'recipient' => $request->recipient,
            'shared_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Invitation shared successfully',
            'data' => $share
        ]);
    }

    public function getShares(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $shares = $invitation->shares;

        return response()->json([
            'success' => true,
            'data' => $shares
        ]);
    }

    public function generatePDF(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        // PDF generation would go here using DomPDF
        // For now, return success

        return response()->json([
            'success' => true,
            'message' => 'PDF generation initiated',
            'data' => [
                'invitation_id' => $invitation->id,
                'pdf_url' => null, // Would be generated URL
            ]
        ]);
    }

    public function generateImage(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        // Image generation would go here
        // For now, return success

        return response()->json([
            'success' => true,
            'message' => 'Image generation initiated',
            'data' => [
                'invitation_id' => $invitation->id,
                'image_url' => null, // Would be generated URL
            ]
        ]);
    }

    public function respond(Request $request, $code)
    {
        $invitation = Invitation::where('invitation_code', $code)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:accepted,declined,maybe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $invitation->update([
            'status' => $request->status,
            'responded_at' => now(),
        ]);

        // Update entry status if exists
        if ($invitation->entry) {
            $invitation->entry->update([
                'status' => $request->status === 'accepted' ? 'confirmed' : 'declined',
                'confirmed_at' => $request->status === 'accepted' ? now() : null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Response recorded successfully',
            'data' => $invitation
        ]);
    }

    public function getAnalytics(Request $request, $id)
    {
        $invitation = $this->ownerInvitations($request)->findOrFail($id);

        $analytics = [
            'open_count' => $invitation->open_count,
            'sent_at' => $invitation->sent_at,
            'opened_at' => $invitation->opened_at,
            'responded_at' => $invitation->responded_at,
            'status' => $invitation->status,
            'shares_count' => $invitation->shares()->count(),
            'shares_by_platform' => $invitation->shares()
                ->selectRaw('platform, count(*) as count')
                ->groupBy('platform')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $analytics
        ]);
    }
}
