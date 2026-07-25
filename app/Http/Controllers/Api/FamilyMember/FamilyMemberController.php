<?php

namespace App\Http\Controllers\Api\FamilyMember;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class FamilyMemberController extends Controller
{
    /**
     * GET /api/v1/family-members
     * List all family members added by the authenticated user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Family members must belong to a root (non-sub) account
        if ($user->isFamilyMember()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the main account holder can manage family members.',
            ], 403);
        }

        $members = User::where('parent_user_id', $user->id)
            ->select(['id', 'name', 'email', 'phone', 'avatar', 'family_role', 'is_active', 'created_at'])
            ->get()
            ->map(fn($m) => $this->formatMember($m));

        return response()->json([
            'success' => true,
            'data'    => $members,
            'count'   => $members->count(),
            'max_editors_allowed' => $user->maxFamilyEditorsAllowed(),
            'can_add_editors'     => $user->canAddFamilyEditors(),
        ]);
    }

    /**
     * POST /api/v1/family-members
     * Add a new family member sub-account.
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->isFamilyMember()) {
            return response()->json([
                'success' => false,
                'message' => 'Only the main account holder can add family members.',
            ], 403);
        }

        $maxRole = $user->maxFamilyRole();

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|unique:users,email',
            'password'    => 'nullable|string|min:6',
            'family_role' => 'nullable|in:viewer,editor',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Enforce editor cap
        $requestedRole = $request->input('family_role', User::FAMILY_ROLE_VIEWER);
        if ($requestedRole === User::FAMILY_ROLE_EDITOR && !$user->canAddFamilyEditors()) {
            return response()->json([
                'success' => false,
                'message' => 'Your current plan does not allow adding editor family members. Please upgrade to Family Plan or above.',
            ], 403);
        }

        // Check max member count
        $currentCount = User::where('parent_user_id', $user->id)->count();
        if ($currentCount >= $user->maxFamilyEditorsAllowed()) {
            return response()->json([
                'success' => false,
                'message' => "You have reached the maximum number of family members allowed ({$user->maxFamilyEditorsAllowed()}) for your plan.",
            ], 403);
        }

        $password = $request->input('password') ?: Str::random(10);

        $member = User::create([
            'name'             => $request->name,
            'phone'            => $request->phone,
            'email'            => $request->email,
            'password'         => Hash::make($password),
            'parent_user_id'   => $user->id,
            'family_role'      => $requestedRole,
            'is_active'        => true,
            'must_change_password' => !$request->has('password'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Family member added successfully.',
            'data'    => $this->formatMember($member),
            // Return the auto-generated password only if we generated it
            'temp_password' => !$request->has('password') ? $password : null,
        ], 201);
    }

    /**
     * GET /api/v1/family-members/{id}
     * Show a single family member.
     */
    public function show(Request $request, $id)
    {
        $member = $this->findMember($request->user(), $id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Family member not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatMember($member),
        ]);
    }

    /**
     * PUT /api/v1/family-members/{id}
     * Update a family member (name, phone, role, active status).
     */
    public function update(Request $request, $id)
    {
        $user   = $request->user();
        $member = $this->findMember($user, $id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Family member not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'        => 'nullable|string|max:255',
            'phone'       => 'nullable|string|max:20',
            'email'       => 'nullable|email|unique:users,email,' . $member->id,
            'password'    => 'nullable|string|min:6',
            'family_role' => 'nullable|in:viewer,editor',
            'is_active'   => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Enforce editor cap if role is being upgraded
        if (
            $request->input('family_role') === User::FAMILY_ROLE_EDITOR
            && $member->family_role !== User::FAMILY_ROLE_EDITOR
            && !$user->canAddFamilyEditors()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Your current plan does not allow editor family members.',
            ], 403);
        }

        $updateData = array_filter([
            'name'        => $request->name,
            'phone'       => $request->phone,
            'email'       => $request->email,
            'family_role' => $request->family_role,
            'is_active'   => $request->is_active,
        ], fn($v) => $v !== null);

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
            $updateData['must_change_password'] = false;
        }

        $member->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Family member updated successfully.',
            'data'    => $this->formatMember($member->fresh()),
        ]);
    }

    /**
     * DELETE /api/v1/family-members/{id}
     * Remove a family member sub-account.
     */
    public function destroy(Request $request, $id)
    {
        $member = $this->findMember($request->user(), $id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Family member not found.',
            ], 404);
        }

        // Revoke all tokens before deleting
        $member->tokens()->delete();
        $member->delete();

        return response()->json([
            'success' => true,
            'message' => 'Family member removed successfully.',
        ]);
    }

    /**
     * POST /api/v1/family-members/{id}/toggle-active
     * Toggle active / inactive status.
     */
    public function toggleActive(Request $request, $id)
    {
        $member = $this->findMember($request->user(), $id);

        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Family member not found.',
            ], 404);
        }

        $member->update(['is_active' => !$member->is_active]);

        $status = $member->fresh()->is_active ? 'activated' : 'deactivated';

        return response()->json([
            'success' => true,
            'message' => "Family member {$status} successfully.",
            'data'    => $this->formatMember($member->fresh()),
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function findMember(User $user, $id): ?User
    {
        if ($user->isFamilyMember()) {
            return null;
        }

        return User::where('parent_user_id', $user->id)
            ->where('id', $id)
            ->first();
    }

    private function formatMember(User $member): array
    {
        return [
            'id'          => $member->id,
            'name'        => $member->name,
            'email'       => $member->email,
            'phone'       => $member->phone,
            'avatar'      => $member->avatar,
            'family_role' => $member->family_role ?? User::FAMILY_ROLE_VIEWER,
            'is_active'   => (bool) $member->is_active,
            'must_change_password' => (bool) $member->must_change_password,
            'created_at'  => $member->created_at,
        ];
    }
}
