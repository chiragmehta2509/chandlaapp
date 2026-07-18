<?php

namespace App\Http\Controllers\Api\Entry;

use App\Http\Controllers\Controller;
use App\Models\Entry;
use App\Models\Event;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EntryController extends Controller
{
    /**
     * Helper: get the data owner user ID (supports family member accounts).
     */
    private function ownerId(Request $request): int
    {
        return $request->user()->dataOwnerId();
    }

    /**
     * Helper: query entries scoped to the data owner's events.
     */
    private function ownerEntries(Request $request)
    {
        $userId = $this->ownerId($request);
        return Entry::whereHas('event', function($q) use ($userId) {
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
        $query = $this->ownerEntries($request)->with(['event', 'contact']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $entries
        ]);
    }

    public function getByEvent(Request $request, $eventId)
    {
        $event = $this->ownerEvents($request)->findOrFail($eventId);
        $entries = $event->entries()->with('contact')->get();

        return response()->json([
            'success' => true,
            'data' => $entries
        ]);
    }

    public function show(Request $request, $id)
    {
        $entry = $this->ownerEntries($request)
            ->with(['event', 'contact', 'invitations'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $entry
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'contact_id' => 'nullable|exists:contacts,id',
            'guest_name' => 'required|string|max:255',
            'guest_phone' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'adults_count' => 'nullable|integer|min:1',
            'children_count' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:pending,confirmed,declined,maybe',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = $this->ownerEvents($request)->findOrFail($request->event_id);

        $data = $request->only([
            'event_id', 'contact_id', 'guest_name', 'guest_phone',
            'guest_email', 'adults_count', 'children_count', 'status', 'notes'
        ]);

        $entry = Entry::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_entry',
            'model_type' => Entry::class,
            'model_id' => $entry->id,
            'new_values' => $entry->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry created successfully',
            'data' => $entry->load(['event', 'contact'])
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $entry = $this->ownerEntries($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'contact_id' => 'nullable|exists:contacts,id',
            'guest_name' => 'nullable|string|max:255',
            'guest_phone' => 'nullable|string',
            'guest_email' => 'nullable|email',
            'adults_count' => 'nullable|integer|min:1',
            'children_count' => 'nullable|integer|min:0',
            'status' => 'nullable|string|in:pending,confirmed,declined,maybe',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldValues = $entry->toArray();
        $entry->update($request->only([
            'contact_id', 'guest_name', 'guest_phone', 'guest_email',
            'adults_count', 'children_count', 'status', 'notes'
        ]));

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_entry',
            'model_type' => Entry::class,
            'model_id' => $entry->id,
            'old_values' => $oldValues,
            'new_values' => $entry->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Entry updated successfully',
            'data' => $entry->load(['event', 'contact'])
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $entry = $this->ownerEntries($request)->findOrFail($id);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_entry',
            'model_type' => Entry::class,
            'model_id' => $entry->id,
            'old_values' => $entry->toArray(),
            'ip_address' => $request->ip(),
        ]);

        $entry->delete();

        return response()->json([
            'success' => true,
            'message' => 'Entry deleted successfully'
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $entry = $this->ownerEntries($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,confirmed,declined,maybe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $entry->update([
            'status' => $request->status,
            'confirmed_at' => $request->status === 'confirmed' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated successfully',
            'data' => $entry
        ]);
    }

    public function bulkCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|exists:events,id',
            'entries' => 'required|array|min:1',
            'entries.*.guest_name' => 'required|string',
            'entries.*.guest_phone' => 'nullable|string',
            'entries.*.adults_count' => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $event = $this->ownerEvents($request)->findOrFail($request->event_id);
        $created = [];

        foreach ($request->entries as $entryData) {
            $entryData['event_id'] = $request->event_id;
            $created[] = Entry::create($entryData);
        }

        return response()->json([
            'success' => true,
            'message' => count($created) . ' entries created successfully',
            'data' => $created
        ], 201);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entry_ids' => 'required|array|min:1',
            'status' => 'required|string|in:pending,confirmed,declined,maybe',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $entries = Entry::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereIn('id', $request->entry_ids);

        $entries->update([
            'status' => $request->status,
            'confirmed_at' => $request->status === 'confirmed' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status updated for ' . $entries->count() . ' entries'
        ]);
    }

    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'entries' => 'required|array',
            'entries.*.id' => 'nullable|integer',
            'entries.*.event_id' => 'required|exists:events,id',
            'entries.*.guest_name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $synced = [];
        foreach ($request->entries as $entryData) {
            // Verify event belongs to data owner
            $event = Event::where('user_id', $userId)->find($entryData['event_id']);
            if (!$event) continue;

            if (isset($entryData['id'])) {
                $entry = Entry::find($entryData['id']);
                if ($entry && $entry->event->user_id === $userId) {
                    $entry->update($entryData);
                    $synced[] = $entry;
                }
            } else {
                $synced[] = Entry::create($entryData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Entries synced successfully',
            'data' => $synced
        ]);
    }
}
