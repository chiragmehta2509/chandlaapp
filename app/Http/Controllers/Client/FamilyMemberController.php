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
        $currentCount = $user->familyMembers()->count();
        $remainingSlots = max(0, $maxAllowed - $currentCount);

        if ($remainingSlots <= 0) {
            return back()->withErrors(['limit' => "You have reached the maximum of {$maxAllowed} family members for your current plan."])->withInput();
        }

        $rawMembers = $request->input('members');
        if (!is_array($rawMembers)) {
            $rawMembers = [
                [
                    'name'  => $request->input('name'),
                    'phone' => $request->input('phone'),
                    'email' => $request->input('email'),
                    'role'  => $request->input('role'),
                ]
            ];
        }

        $membersToProcess = [];
        foreach ($rawMembers as $m) {
            $name  = trim((string) ($m['name'] ?? ''));
            $phone = trim((string) ($m['phone'] ?? ''));
            if ($name !== '' || $phone !== '') {
                $membersToProcess[] = $m;
            }
        }

        if (empty($membersToProcess)) {
            return back()->withErrors(['members' => 'Please fill in details for at least one family member.'])->withInput();
        }

        if (count($membersToProcess) > $remainingSlots) {
            return back()->withErrors(['limit' => "You have {$remainingSlots} slot(s) remaining, but submitted " . count($membersToProcess) . " member(s)."])->withInput();
        }

        $createdMembers = [];
        $errors = [];

        foreach ($membersToProcess as $idx => $m) {
            $rowNum = $idx + 1;
            $name     = trim((string) ($m['name'] ?? ''));
            $rawPhone = (string) ($m['phone'] ?? '');
            $phone    = $this->normalizeIndianMobile($rawPhone);
            $email    = !empty($m['email']) ? strtolower(trim((string) $m['email'])) : null;

            if ($name === '') {
                $errors[] = "Member #{$rowNum}: Name is required.";
                continue;
            }

            if (strlen($phone) !== 10 || !preg_match('/^[6-9][0-9]{9}$/', $phone)) {
                $errors[] = "Member #{$rowNum} ({$name}): Enter a valid 10-digit Indian mobile number.";
                continue;
            }

            if (User::where('phone', $phone)->exists()) {
                $errors[] = "Member #{$rowNum} ({$name}): Mobile number {$phone} is already registered.";
                continue;
            }

            if ($email) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Member #{$rowNum} ({$name}): Enter a valid email address.";
                    continue;
                }
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Member #{$rowNum} ({$name}): Email {$email} is already registered.";
                    continue;
                }
            }

            $requestedRole = $m['role'] ?? User::FAMILY_ROLE_VIEWER;
            $role = ($requestedRole === User::FAMILY_ROLE_EDITOR && $user->canAddFamilyEditors())
                ? User::FAMILY_ROLE_EDITOR
                : User::FAMILY_ROLE_VIEWER;

            $hasEmail     = !empty($email);
            $tempPassword = $hasEmail ? $this->generateTempPassword() : config('family.default_password', 'Chandla@123');

            $member = User::create([
                'name'                 => $name,
                'email'                => $email,
                'phone'                => $phone,
                'password'             => Hash::make($tempPassword),
                'parent_user_id'       => $user->id,
                'family_role'          => $role,
                'must_change_password' => true,
                'auth_provider'        => $hasEmail ? 'email' : 'phone',
                'is_active'            => true,
            ]);

            if ($hasEmail) {
                try {
                    Mail::send('emails.family-invite', [
                        'member'       => $member,
                        'parent'       => $user,
                        'tempPassword' => $tempPassword,
                    ], function ($message) use ($member) {
                        $message->to($member->email, $member->name)
                            ->subject('You\'ve been added to a Chandla Book account');
                    });
                } catch (\Throwable $e) {
                    report($e);
                }
            }

            $createdMembers[] = [
                'name'          => $member->name,
                'phone'         => $member->phone,
                'email'         => $member->email,
                'temp_password' => $tempPassword,
                'has_email'     => $hasEmail,
            ];
        }

        if (!empty($errors) && empty($createdMembers)) {
            return back()->withErrors($errors)->withInput();
        }

        $countAdded = count($createdMembers);
        $namesAdded = implode(', ', array_column($createdMembers, 'name'));

        $redirect = redirect()->route('client.family-members.index');

        if (!empty($errors)) {
            $redirect->withErrors($errors);
        }

        if ($countAdded === 1) {
            $m = $createdMembers[0];
            $redirect->with('success', "Added {$m['name']}" . ($m['has_email'] ? ". Login details sent to {$m['email']}." : "."));
            if (!$m['has_email']) {
                $redirect->with('temp_password_for', $m['name'])
                    ->with('temp_password', $m['temp_password'])
                    ->with('temp_password_login', $m['phone']);
            }
            return $redirect;
        }

        return $redirect
            ->with('success', "Successfully added {$countAdded} family members: {$namesAdded}.")
            ->with('bulk_added_members', $createdMembers);
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
