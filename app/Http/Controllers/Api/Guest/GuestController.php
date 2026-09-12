<?php

namespace App\Http\Controllers\Api\Guest;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestController extends Controller
{
    /**
     * Scope guests to the data owner (supports family member accounts).
     */
    private function userGuests(Request $request)
    {
        $userId = $request->user()->dataOwnerId();
        return Guest::where('user_id', $userId);
    }

    /**
     * GET /api/v1/guests
     * List all guests with optional search & pagination.
     */
    public function index(Request $request)
    {
        $perPage = min((int) $request->get('per_page', 15), 100);
        $query   = $this->userGuests($request);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('relationship', 'like', "%{$s}%");
            });
        }

        if ($request->boolean('favorites')) {
            $query->where('is_favorite', true);
        }

        $guests = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $guests,
        ]);
    }

    /**
     * GET /api/v1/guests/favorites
     * List favorite guests.
     */
    public function favorites(Request $request)
    {
        $guests = $this->userGuests($request)
            ->where('is_favorite', true)
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $guests,
        ]);
    }

    /**
     * GET /api/v1/guests/search?q=...
     * Search guests by name, phone, email, city, relationship.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $s      = $request->q;
        $guests = $this->userGuests($request)
            ->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('city', 'like', "%{$s}%")
                  ->orWhere('relationship', 'like', "%{$s}%");
            })
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => $guests,
        ]);
    }

    /**
     * GET /api/v1/guests/{id}
     * Show a single guest.
     */
    public function show(Request $request, $id)
    {
        $guest = $this->userGuests($request)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $guest,
        ]);
    }

    /**
     * POST /api/v1/guests
     * Create a new guest.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:60',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'city'         => 'required|string|max:100',
            'relationship' => 'required|string|max:100',
            'notes'        => 'nullable|string',
            'is_favorite'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $data            = $request->only(['name', 'phone', 'email', 'address', 'city', 'relationship', 'notes', 'is_favorite']);
        $data['user_id'] = $request->user()->dataOwnerId();

        $guest = Guest::create($data);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'create_guest',
            'model_type' => Guest::class,
            'model_id'   => $guest->id,
            'new_values' => $guest->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest created successfully',
            'data'    => $guest,
        ], 201);
    }

    /**
     * PUT /api/v1/guests/{id}
     * Update an existing guest.
     */
    public function update(Request $request, $id)
    {
        $guest = $this->userGuests($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'         => 'nullable|string|max:255',
            'phone'        => 'nullable|string|max:60',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string|max:100',
            'relationship' => 'nullable|string|max:100',
            'notes'        => 'nullable|string',
            'is_favorite'  => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldValues = $guest->toArray();
        $data      = $request->only(['name', 'phone', 'email', 'address', 'city', 'relationship', 'notes', 'is_favorite']);

        $guest->update($data);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'update_guest',
            'model_type' => Guest::class,
            'model_id'   => $guest->id,
            'old_values' => $oldValues,
            'new_values' => $guest->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Guest updated successfully',
            'data'    => $guest,
        ]);
    }

    /**
     * DELETE /api/v1/guests/{id}
     * Delete a guest.
     */
    public function destroy(Request $request, $id)
    {
        $guest = $this->userGuests($request)->findOrFail($id);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'delete_guest',
            'model_type' => Guest::class,
            'model_id'   => $guest->id,
            'old_values' => $guest->toArray(),
            'ip_address' => $request->ip(),
        ]);

        $guest->delete();

        return response()->json([
            'success' => true,
            'message' => 'Guest deleted successfully',
        ]);
    }

    /**
     * POST /api/v1/guests/{id}/favorite
     * Toggle favorite status.
     */
    public function toggleFavorite(Request $request, $id)
    {
        $guest = $this->userGuests($request)->findOrFail($id);
        $guest->update(['is_favorite' => !$guest->is_favorite]);

        return response()->json([
            'success' => true,
            'message' => $guest->is_favorite ? 'Marked as favorite' : 'Removed from favorites',
            'data'    => $guest,
        ]);
    }
}
