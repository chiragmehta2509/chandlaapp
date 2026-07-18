<?php

namespace App\Http\Controllers\Api\Report;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Entry;
use App\Models\Invitation;
use App\Models\UPITransaction;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Helper: get the data owner user ID (supports family member accounts).
     */
    private function ownerId(Request $request): int
    {
        return $request->user()->dataOwnerId();
    }

    public function eventsReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'event_type' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $query = Event::where('user_id', $userId);

        if ($request->start_date) {
            $query->where('event_date', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('event_date', '<=', $request->end_date);
        }

        if ($request->event_type) {
            $query->where('event_type', $request->event_type);
        }

        $events = $query->withCount(['entries', 'invitations'])->get();

        $report = [
            'total_events' => $events->count(),
            'upcoming_events' => $events->where('event_date', '>=', now()->toDateString())->count(),
            'past_events' => $events->where('event_date', '<', now()->toDateString())->count(),
            'archived_events' => $events->where('is_archived', true)->count(),
            'events_by_type' => $events->groupBy('event_type')->map->count(),
            'events' => $events,
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function entriesReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|exists:events,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $query = Entry::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->start_date) {
            $query->whereHas('event', function($q) use ($request) {
                $q->where('event_date', '>=', $request->start_date);
            });
        }

        if ($request->end_date) {
            $query->whereHas('event', function($q) use ($request) {
                $q->where('event_date', '<=', $request->end_date);
            });
        }

        $entries = $query->with(['event', 'contact'])->get();

        $report = [
            'total_entries' => $entries->count(),
            'confirmed_entries' => $entries->where('status', 'confirmed')->count(),
            'pending_entries' => $entries->where('status', 'pending')->count(),
            'declined_entries' => $entries->where('status', 'declined')->count(),
            'total_guests' => $entries->sum('adults_count') + $entries->sum('children_count'),
            'entries_by_status' => $entries->groupBy('status')->map->count(),
            'entries' => $entries,
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function invitationsReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'nullable|exists:events,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $query = Invitation::whereHas('event', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        $invitations = $query->with(['event', 'entry'])->get();

        $report = [
            'total_invitations' => $invitations->count(),
            'sent_invitations' => $invitations->where('status', 'sent')->count(),
            'opened_invitations' => $invitations->where('status', 'opened')->count(),
            'accepted_invitations' => $invitations->where('status', 'accepted')->count(),
            'total_opens' => $invitations->sum('open_count'),
            'invitations_by_status' => $invitations->groupBy('status')->map->count(),
            'invitations' => $invitations,
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function paymentsReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $query = UPITransaction::where('user_id', $userId);

        if ($request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->end_date) {
            $query->where('created_at', '<=', $request->end_date);
        }

        $transactions = $query->with('event')->get();

        $report = [
            'total_transactions' => $transactions->count(),
            'completed_transactions' => $transactions->where('status', 'completed')->count(),
            'pending_transactions' => $transactions->where('status', 'pending')->count(),
            'failed_transactions' => $transactions->where('status', 'failed')->count(),
            'total_amount' => $transactions->where('status', 'completed')->sum('amount'),
            'transactions_by_status' => $transactions->groupBy('status')->map->count(),
            'transactions' => $transactions,
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function contactsReport(Request $request)
    {
        $userId = $this->ownerId($request);
        $contacts = Contact::where('user_id', $userId)->get();

        $report = [
            'total_contacts' => $contacts->count(),
            'favorite_contacts' => $contacts->where('is_favorite', true)->count(),
            'contacts_with_phone' => $contacts->whereNotNull('phone')->count(),
            'contacts_with_email' => $contacts->whereNotNull('email')->count(),
            'contacts_by_relationship' => $contacts->groupBy('relationship')->map->count(),
            'contacts' => $contacts,
        ];

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    public function dashboard(Request $request)
    {
        $userId = $this->ownerId($request);

        $dashboard = [
            'events' => [
                'total' => Event::where('user_id', $userId)->count(),
                'active' => Event::where('user_id', $userId)->active()->count(),
                'upcoming' => Event::where('user_id', $userId)->active()->upcoming()->count(),
                'archived' => Event::where('user_id', $userId)->archived()->count(),
            ],
            'contacts' => [
                'total' => Contact::where('user_id', $userId)->count(),
                'favorites' => Contact::where('user_id', $userId)->favorite()->count(),
            ],
            'entries' => [
                'total' => Entry::whereHas('event', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->count(),
                'confirmed' => Entry::whereHas('event', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->confirmed()->count(),
            ],
            'invitations' => [
                'total' => Invitation::whereHas('event', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->count(),
                'sent' => Invitation::whereHas('event', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->sent()->count(),
            ],
            'payments' => [
                'total' => UPITransaction::where('user_id', $userId)->count(),
                'completed' => UPITransaction::where('user_id', $userId)->completed()->count(),
                'total_amount' => UPITransaction::where('user_id', $userId)->completed()->sum('amount'),
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $dashboard
        ]);
    }

    public function exportReport(Request $request, $type)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $this->ownerId($request);
        $filename = "{$type}_report_" . now()->format('Y-m-d') . '.xlsx';

        switch ($type) {
            case 'events':
                $data = Event::where('user_id', $userId)->get();
                break;
            case 'contacts':
                $data = Contact::where('user_id', $userId)->get();
                break;
            case 'entries':
                $data = Entry::whereHas('event', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })->with('event')->get();
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid report type'
                ], 400);
        }

        return Excel::download(new class($data) implements \Maatwebsite\Excel\Concerns\FromCollection {
            protected $data;

            public function __construct($data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }
        }, $filename);
    }
}
