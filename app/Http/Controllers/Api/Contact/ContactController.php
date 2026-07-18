<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContactController extends Controller
{
    /**
     * Helper: query contacts scoped to the data owner (supports family member accounts).
     */
    private function userContacts(Request $request)
    {
        $userId = $request->user()->dataOwnerId();
        return Contact::where('user_id', $userId);
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $query = $this->userContacts($request);

        if ($request->has('search')) {
            $query->search($request->search);
        }

        $contacts = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    public function favorites(Request $request)
    {
        $contacts = $this->userContacts($request)
            ->favorite()
            ->orderBy('name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'q' => 'required|string|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $contacts = $this->userContacts($request)
            ->search($request->q)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $contacts
        ]);
    }

    public function show(Request $request, $id)
    {
        $contact = $this->userContacts($request)
            ->with('entries')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_favorite' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $request->only([
            'name', 'phone', 'email', 'address', 
            'relationship', 'notes', 'is_favorite'
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('contacts', 'public');
        }

        $data['user_id'] = $request->user()->dataOwnerId();
        $contact = Contact::create($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'create_contact',
            'model_type' => Contact::class,
            'model_id' => $contact->id,
            'new_values' => $contact->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact created successfully',
            'data' => $contact
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $contact = $this->userContacts($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'relationship' => 'nullable|string',
            'notes' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'is_favorite' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldValues = $contact->toArray();
        $data = $request->only([
            'name', 'phone', 'email', 'address', 
            'relationship', 'notes', 'is_favorite'
        ]);

        if ($request->hasFile('avatar')) {
            if ($contact->avatar) {
                Storage::disk('public')->delete($contact->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('contacts', 'public');
        }

        $contact->update($data);

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'update_contact',
            'model_type' => Contact::class,
            'model_id' => $contact->id,
            'old_values' => $oldValues,
            'new_values' => $contact->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contact updated successfully',
            'data' => $contact
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $contact = $this->userContacts($request)->findOrFail($id);

        if ($contact->avatar) {
            Storage::disk('public')->delete($contact->avatar);
        }

        ActivityLog::create([
            'user_id' => $request->user()->id,
            'action' => 'delete_contact',
            'model_type' => Contact::class,
            'model_id' => $contact->id,
            'old_values' => $contact->toArray(),
            'ip_address' => $request->ip(),
        ]);

        $contact->delete();

        return response()->json([
            'success' => true,
            'message' => 'Contact deleted successfully'
        ]);
    }

    public function toggleFavorite(Request $request, $id)
    {
        $contact = $this->userContacts($request)->findOrFail($id);
        $contact->update(['is_favorite' => !$contact->is_favorite]);

        return response()->json([
            'success' => true,
            'message' => 'Favorite status updated',
            'data' => $contact
        ]);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = Excel::toArray(new class implements ToArray, WithHeadingRow {
            public function array(array $array)
            {
                return $array;
            }
        }, $request->file('file'));

        $userId = $request->user()->dataOwnerId();
        $imported = 0;
        $errors = [];

        foreach ($data[0] as $row) {
            try {
                Contact::create([
                    'user_id' => $userId,
                    'name' => $row['name'] ?? 'Unknown',
                    'phone' => $row['phone'] ?? null,
                    'email' => $row['email'] ?? null,
                    'address' => $row['address'] ?? null,
                    'relationship' => $row['relationship'] ?? null,
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Imported {$imported} contacts",
            'data' => [
                'imported' => $imported,
                'errors' => $errors
            ]
        ]);
    }

    public function export(Request $request)
    {
        $contacts = $this->userContacts($request)->get();

        $filename = 'contacts_' . now()->format('Y-m-d') . '.xlsx';
        
        return Excel::download(new class($contacts) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $contacts;

            public function __construct($contacts)
            {
                $this->contacts = $contacts;
            }

            public function collection()
            {
                return $this->contacts;
            }
        }, $filename);
    }

    public function downloadTemplate(Request $request)
    {
        $template = [
            ['name', 'phone', 'email', 'address', 'relationship']
        ];

        $filename = 'contacts_template.xlsx';
        
        return Excel::download(new class($template) implements \Maatwebsite\Excel\Concerns\FromArray {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function array(): array
            {
                return $this->data;
            }
        }, $filename);
    }

    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contacts' => 'required|array',
            'contacts.*.id' => 'nullable|integer',
            'contacts.*.name' => 'required|string',
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
        foreach ($request->contacts as $contactData) {
            if (isset($contactData['id'])) {
                $contact = Contact::where('user_id', $userId)->find($contactData['id']);
                if ($contact) {
                    $contact->update($contactData);
                    $synced[] = $contact;
                }
            } else {
                $contactData['user_id'] = $userId;
                $synced[] = Contact::create($contactData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Contacts synced successfully',
            'data' => $synced
        ]);
    }
}
