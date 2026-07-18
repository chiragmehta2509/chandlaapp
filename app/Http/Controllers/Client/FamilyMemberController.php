<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class FamilyMemberController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $members = $user->familyMembers()->orderByDesc('created_at')->get();
        $maxAllowed = $user->maxFamilyEditorsAllowed();
        $canAddEditors = $user->canAddFamilyEditors();

        return view('client.family-members.index', [
            'members' => $members,
            'maxAllowed' => $maxAllowed,
            'remainingSlots' => max(0, $maxAllowed - $members->count()),
            'canAddEditors' => $canAddEditors,
            'familyPackAmount' => (int) config('packs.family.amount_inr', 600),
            'completePackAmount' => (int) config('packs.premium_bundle.amount_inr', 700),
        ]);
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $maxAllowed = $user->maxFamilyEditorsAllowed();
        if ($user->familyMembers()->count() >= $maxAllowed) {
            return back()->withErrors(['limit' => "You have reached the maximum of {$maxAllowed} family members for your current plan."])->withInput();
        }

        $request->merge([
            'email' => $request->filled('email') ? strtolower(trim((string) $request->input('email'))) : null,
            'phone' => $this->normalizeIndianMobile($request->input('phone')),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email:rfc|max:255|unique:users,email',
            'phone' => 'required|string|size:10|regex:/^[6-9][0-9]{9}$/|unique:users,phone',
            'role' => 'nullable|in:viewer,editor',
        ], [
            'phone.regex' => 'Enter a valid 10-digit Indian mobile number (starts with 6, 7, 8, or 9).',
            'phone.size' => 'Mobile number must be exactly 10 digits.',
            'email.email' => 'Enter a valid email address.',
        ]);

        // Resolve role: requested role, capped by what the parent's plan allows.
        $requestedRole = $validated['role'] ?? User::FAMILY_ROLE_VIEWER;
        $role = ($requestedRole === User::FAMILY_ROLE_EDITOR && $user->canAddFamilyEditors())
            ? User::FAMILY_ROLE_EDITOR
            : User::FAMILY_ROLE_VIEWER;

        $hasEmail = !empty($validated['email']);
        $tempPassword = $hasEmail ? $this->generateTempPassword() : config('family.default_password', 'Chandla@123');

        $member = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($tempPassword),
            'parent_user_id' => $user->id,
            'family_role' => $role,
            'must_change_password' => true,
            'auth_provider' => $hasEmail ? 'email' : 'phone',
            'is_active' => true,
        ]);

        if ($hasEmail) {
            try {
                Mail::send('emails.family-invite', [
                    'member' => $member,
                    'parent' => $user,
                    'tempPassword' => $tempPassword,
                ], function ($message) use ($member) {
                    $message->to($member->email, $member->name)
                        ->subject('You\'ve been added to a Chandla Book account');
                });
            } catch (\Throwable $e) {
                report($e);
            }

            return redirect()->route('client.family-members.index')
                ->with('success', "Added {$member->name}. Login details sent to {$member->email}.");
        }

        return redirect()->route('client.family-members.index')
            ->with('success', "Added {$member->name}.")
            ->with('temp_password_for', $member->name)
            ->with('temp_password', $tempPassword)
            ->with('temp_password_login', $member->phone);
    }

    public function destroy($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $member = User::where('parent_user_id', $user->id)->where('id', $id)->firstOrFail();
        $name = $member->name;
        $member->delete();

        return redirect()->route('client.family-members.index')
            ->with('success', "Removed {$name}.");
    }

    public function resetPassword($id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $member = User::where('parent_user_id', $user->id)->where('id', $id)->firstOrFail();

        $hasEmail = !empty($member->email);
        $tempPassword = $hasEmail ? $this->generateTempPassword() : config('family.default_password', 'Chandla@123');

        $member->password = Hash::make($tempPassword);
        $member->must_change_password = true;
        $member->save();

        if ($hasEmail) {
            try {
                Mail::send('emails.family-invite', [
                    'member' => $member,
                    'parent' => $user,
                    'tempPassword' => $tempPassword,
                ], function ($message) use ($member) {
                    $message->to($member->email, $member->name)
                        ->subject('Your Chandla Book password has been reset');
                });
            } catch (\Throwable $e) {
                report($e);
            }

            return redirect()->route('client.family-members.index')
                ->with('success', "Password reset. New login details sent to {$member->email}.");
        }

        return redirect()->route('client.family-members.index')
            ->with('success', "Password reset for {$member->name}.")
            ->with('temp_password_for', $member->name)
            ->with('temp_password', $tempPassword)
            ->with('temp_password_login', $member->phone);
    }

    public function updateRole(Request $request, $id)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $member = User::where('parent_user_id', $user->id)->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'role' => 'required|in:viewer,editor',
        ]);

        $requestedRole = $validated['role'];
        if ($requestedRole === User::FAMILY_ROLE_EDITOR && !$user->canAddFamilyEditors()) {
            return back()->withErrors(['role' => 'Your current plan does not allow family editors. Upgrade to Family Plan (₹' . (int) config('packs.family.amount_inr', 600) . ') or Premium Host Plan to unlock.']);
        }

        $member->family_role = $requestedRole;
        $member->save();

        $label = $requestedRole === User::FAMILY_ROLE_EDITOR ? 'Editor (full access)' : 'Viewer (read-only)';
        return redirect()->route('client.family-members.index')
            ->with('success', "{$member->name} is now: {$label}.");
    }

    private function generateTempPassword(): string
    {
        // Pronounceable-ish random: letters + 4 digits, e.g., "Mango4729"
        $words = ['Mango', 'River', 'Sunny', 'Tiger', 'Lotus', 'Cloud', 'Petal', 'Aroma', 'Drift', 'Spark'];
        return $words[array_rand($words)] . str_pad((string) random_int(1000, 9999), 4, '0', STR_PAD_LEFT);
    }

    private function normalizeIndianMobile(mixed $raw): string
    {
        $digits = preg_replace('/\D/', '', (string) $raw);
        if (str_starts_with($digits, '91') && strlen($digits) === 12) {
            $digits = substr($digits, 2);
        }
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            $digits = substr($digits, 1);
        }
        return $digits;
    }
}
