<?php

namespace App\Http\Controllers\Api\Event;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{
    private function userEvents(Request $request)
    {
        $authUser = $request->user();
        $ownerId  = $authUser->dataOwnerId();
        $selfId   = $authUser->id;

        if ($authUser->isFamilyMember() && $selfId !== $ownerId) {
            return Event::where(function ($q) use ($ownerId, $selfId) {
                $q->where('user_id', $ownerId)->orWhere('user_id', $selfId);
            });
        }

        return Event::where('user_id', $ownerId);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $query = $this->userEvents($request)->with(['entries', 'invitations']);

        if ($request->has('type')) {
            $query->where('event_type', $request->type);
        }

        if ($request->has('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $events = $query->orderBy('event_date', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function upcoming(Request $request)
    {
        $events = $this->userEvents($request)
            ->active()
            ->upcoming()
            ->orderBy('event_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function past(Request $request)
    {
        $events = $this->userEvents($request)
            ->active()
            ->past()
            ->orderBy('event_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function archived(Request $request)
    {
        $events = $this->userEvents($request)
            ->archived()
            ->orderBy('archived_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function show(Request $request, $id)
    {
        $event = $this->userEvents($request)
            ->with(['entries.contact', 'invitations', 'collaborators'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'event_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'event_type' => 'nullable|string|in:wedding,birthday,anniversary,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'title', 'description', 'event_date', 'event_time', 
            'venue', 'event_type'
        ]);

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $data['user_id'] = $request->user()->dataOwnerId();
        $event = Event::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'new_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event created successfully',
            'data' => $event
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'event_time' => 'nullable|date_format:H:i',
            'venue' => 'nullable|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'event_type' => 'nullable|string|in:wedding,birthday,anniversary,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldValues = $event->toArray();
        $data = $request->only([
            'title', 'description', 'event_date', 'event_time', 
            'venue', 'event_type'
        ]);

        if ($request->hasFile('cover_image')) {
            if ($event->cover_image) {
                Storage::disk('public')->delete($event->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('events', 'public');
        }

        $event->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'old_values' => $oldValues,
            'new_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        if ($event->cover_image) {
            Storage::disk('public')->delete($event->cover_image);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_event',
            'model_type' => Event::class,
            'model_id' => $event->id,
            'old_values' => $event->toArray(),
            'ip_address' => $request->ip(),
        ]);

        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    public function archive(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $event->update([
            'is_archived' => true,
            'archived_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event archived successfully',
            'data' => $event
        ]);
    }

    public function unarchive(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $event->update([
            'is_archived' => false,
            'archived_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Event unarchived successfully',
            'data' => $event
        ]);
    }

    public function duplicate(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Copy)';
        $newEvent->is_archived = false;
        $newEvent->archived_at = null;
        $newEvent->save();

        return response()->json([
            'success' => true,
            'message' => 'Event duplicated successfully',
            'data' => $newEvent
        ], 201);
    }

    public function getStats(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $stats = [
            'total_entries' => $event->entries()->count(),
            'confirmed_entries' => $event->entries()->confirmed()->count(),
            'pending_entries' => $event->entries()->pending()->count(),
            'declined_entries' => $event->entries()->declined()->count(),
            'total_invitations' => $event->invitations()->count(),
            'sent_invitations' => $event->invitations()->sent()->count(),
            'opened_invitations' => $event->invitations()->opened()->count(),
            'accepted_invitations' => $event->invitations()->accepted()->count(),
            'total_guests' => $event->entries()->sum('adults_count') + $event->entries()->sum('children_count'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function getCollaborators(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);
        $collaborators = $event->collaborators;

        return response()->json([
            'success' => true,
            'data' => $collaborators
        ]);
    }

    public function addCollaborator(Request $request, $id)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|in:owner,editor,viewer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->collaborators()->syncWithoutDetaching([
            $request->user_id => ['role' => $request->role]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collaborator added successfully'
        ]);
    }

    public function updateCollaborator(Request $request, $id, $userId)
    {
        $event = $this->userEvents($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|in:owner,editor,viewer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event->collaborators()->updateExistingPivot($userId, [
            'role' => $request->role
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collaborator updated successfully'
        ]);
    }

    public function removeCollaborator(Request $request, $id, $userId)
    {
        $event = $this->userEvents($request)->findOrFail($id);
        $event->collaborators()->detach($userId);

        return response()->json([
            'success' => true,
            'message' => 'Collaborator removed successfully'
        ]);
    }

    public function sync(Request $request)
    {
        // Handle offline sync
        $validator = Validator::make($request->all(), [
            'events' => 'required|array',
            'events.*.id' => 'nullable|integer',
            'events.*.title' => 'required|string',
            'events.*.event_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $synced = [];
        foreach ($request->events as $eventData) {
            if (isset($eventData['id'])) {
                $event = Event::where('user_id', $userId)->find($eventData['id']);
                if ($event) {
                    $event->update($eventData);
                    $synced[] = $event;
                }
            } else {
                $eventData['user_id'] = $userId;
                $synced[] = Event::create($eventData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Events synced successfully',
            'data' => $synced
        ]);
    }
}
