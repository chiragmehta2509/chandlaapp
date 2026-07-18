<?php

namespace App\Http\Controllers\Api\Invitation;

use App\Http\Controllers\Controller;
use App\Models\MarriageInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class MarriageInvitationController extends Controller
{
    private function buildValidationRules(): array
    {
        $rules = [];
        $fields = config('marriage_invitations.shared_fields', []);
        foreach ($fields as $key => $field) {
            $isReq = !empty($field['required']);
            $type = $field['type'] ?? 'string';
            if ($type === 'date') {
                $rules[$key] = $isReq ? 'required|date' : 'nullable|date';
            } elseif ($type === 'time') {
                $rules[$key] = $isReq ? 'required|date_format:H:i' : 'nullable|date_format:H:i';
            } elseif ($type === 'textarea') {
                $rules[$key] = $isReq ? 'required|string|max:2000' : 'nullable|string|max:2000';
            } elseif ($type === 'image') {
                $rules[$key] = $isReq ? 'required|image|mimes:jpeg,png,jpg,webp|max:5120' : 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120';
            } elseif ($type === 'schedule') {
                $rules[$key] = $isReq ? 'required|array|max:12' : 'nullable|array|max:12';
                $rules[$key.'.*.title'] = 'nullable|string|max:120';
                $rules[$key.'.*.date'] = 'nullable|date';
                $rules[$key.'.*.time'] = 'nullable|string|max:40';
            } else {
                $rules[$key] = $isReq ? 'required|string|max:500' : 'nullable|string|max:500';
            }
        }
        return $rules;
    }

    private function mergeInvitationImageUploads(Request $request, array $validated, ?array $existingData, int $ownerId): array
    {
        $existingData = $existingData ?? [];
        $disk = Storage::disk('public');
        $fields = config('marriage_invitations.shared_fields', []);

        foreach ($fields as $key => $field) {
            if (($field['type'] ?? '') !== 'image') {
                continue;
            }
            unset($validated[$key]);
            if ($request->hasFile($key)) {
                $oldPath = $this->normalizePublicDiskPath(
                    is_string($existingData[$key] ?? null) ? $existingData[$key] : null
                );
                if ($oldPath !== null && $disk->exists($oldPath)) {
                    $disk->delete($oldPath);
                }
                $validated[$key] = $request->file($key)->store('marriage_invitations/' . $ownerId, 'public');
            } elseif (array_key_exists($key, $existingData) && $existingData[$key] !== '') {
                $kept = $this->normalizePublicDiskPath(
                    is_string($existingData[$key]) ? $existingData[$key] : null
                );
                $validated[$key] = $kept !== null ? $kept : $existingData[$key];
            }
        }

        return $validated;
    }

    private function normalizePublicDiskPath(?string $path): ?string
    {
        if ($path === null || $path === '') {
            return null;
        }
        $path = ltrim(str_replace('\\', '/', $path), '/');
        if (str_starts_with($path, 'storage/')) {
            $path = substr($path, strlen('storage/'));
        }
        return $path;
    }

    private function normalizeInvitationTimes(array $data): array
    {
        if (! array_key_exists('wedding_time', $data)) {
            return $data;
        }

        $v = trim((string) ($data['wedding_time'] ?? ''));
        if ($v === '') {
            $data['wedding_time'] = '';
            return $data;
        }

        try {
            $data['wedding_time'] = Carbon::parse($v)->format('H:i');
        } catch (\Throwable $e) {
            $data['wedding_time'] = '';
        }

        return $data;
    }

    private function normalizeScheduleEvents(array $data): array
    {
        if (empty($data['schedule_events']) || !is_array($data['schedule_events'])) {
            unset($data['schedule_events']);
            return $data;
        }

        $rows = [];
        foreach ($data['schedule_events'] as $row) {
            if (!is_array($row)) {
                continue;
            }
            $title = trim((string) ($row['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $dateVal = $row['date'] ?? null;
            $rows[] = [
                'title' => $title,
                'date' => $dateVal !== '' && $dateVal !== null ? $dateVal : null,
                'time' => trim((string) ($row['time'] ?? '')),
            ];
        }

        if ($rows === []) {
            unset($data['schedule_events']);
        } else {
            $data['schedule_events'] = array_values($rows);
        }

        return $data;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $userId = $request->user()->dataOwnerId();

        $invitations = MarriageInvitation::where('user_id', $userId)
            ->orderByDesc('updated_at')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $invitations
        ]);
    }

    public function show(Request $request, $id)
    {
        $userId = $request->user()->dataOwnerId();
        $invitation = MarriageInvitation::where('user_id', $userId)->with('upiTransaction')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $invitation
        ]);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->dataOwnerId();

        if (MarriageInvitation::where('user_id', $userId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have an invitation. Update the existing one instead.'
            ], 400);
        }

        $rules = $this->buildValidationRules();
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data = $this->normalizeInvitationTimes($data);
        $data = $this->mergeInvitationImageUploads($request, $data, null, $userId);
        $data = $this->normalizeScheduleEvents($data);

        $amount = (float) config('marriage_invitations.amount', 300);
        $templateKey = $request->input('template_key', 'heritage');

        $invitation = MarriageInvitation::create([
            'user_id' => $userId,
            'template_key' => $templateKey,
            'data' => $data,
            'amount' => $amount,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marriage invitation created successfully',
            'data' => $invitation
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $userId = $request->user()->dataOwnerId();
        $invitation = MarriageInvitation::where('user_id', $userId)->findOrFail($id);

        if ($invitation->isUnlocked()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot edit after payment.'
            ], 400);
        }

        $rules = $this->buildValidationRules();
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data = $this->normalizeInvitationTimes($data);
        $data = $this->mergeInvitationImageUploads($request, $data, $invitation->data ?? [], $userId);
        $data = $this->normalizeScheduleEvents($data);

        $templateKey = $request->input('template_key', $invitation->template_key);

        $invitation->update([
            'template_key' => $templateKey,
            'data' => $data
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Marriage invitation updated successfully',
            'data' => $invitation
        ]);
    }
}
