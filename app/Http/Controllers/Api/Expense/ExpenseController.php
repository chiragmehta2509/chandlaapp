<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    /**
     * Helper: return query scoped to the authenticated user's events.
     */
    private function userExpenses(Request $request)
    {
        $userId = $request->user()->dataOwnerId();
        return Expense::where('user_id', $userId);
    }

    /**
     * Ensure the event belongs to the authenticated user.
     */
    private function userEvent(Request $request, $eventId): Event
    {
        $userId = $request->user()->dataOwnerId();
        return Event::where('user_id', $userId)->findOrFail($eventId);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/expenses/categories
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Return all available expense categories.
     */
    public function categories()
    {
        return response()->json([
            'success' => true,
            'data'    => Expense::categories(),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/expenses
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * List all expenses across all events (with optional filters).
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $query   = $this->userExpenses($request)->with('event');

        // Filter by event
        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Date range filters
        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }

        // Search by title / payee name
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('payee_name', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        $expenses = $query->orderBy('expense_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $expenses,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/expenses/event/{eventId}
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * List all expenses for a specific event with category-wise summary.
     */
    public function byEvent(Request $request, $eventId)
    {
        $event    = $this->userEvent($request, $eventId);
        $expenses = Expense::where('event_id', $event->id)
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        // Category-wise summary
        $categoryTotals = $expenses->groupBy('category')->map(function ($items, $cat) {
            return [
                'category' => $cat,
                'count'    => $items->count(),
                'total'    => (float) $items->sum('amount'),
            ];
        })->values();

        // Payment method summary
        $paymentTotals = $expenses->groupBy('payment_method')->map(function ($items, $method) {
            return [
                'payment_method' => $method,
                'count'          => $items->count(),
                'total'          => (float) $items->sum('amount'),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data'    => [
                'event'          => $event,
                'expenses'       => $expenses,
                'summary'        => [
                    'total_amount'   => (float) $expenses->sum('amount'),
                    'total_entries'  => $expenses->count(),
                    'by_category'    => $categoryTotals,
                    'by_payment'     => $paymentTotals,
                ],
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/expenses/{id}
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Show a single expense entry.
     */
    public function show(Request $request, $id)
    {
        $expense = $this->userExpenses($request)->with('event')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $expense,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/expenses
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Create a new expense entry.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id'       => 'required|integer|exists:events,id',
            'title'          => 'required|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'required|string|max:100',
            'amount'         => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'payee_name'     => 'nullable|string|max:255',
            'payee_phone'    => 'nullable|string|max:30',
            'payee_upi'      => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,gpay,bank_transfer,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_image'  => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Ensure the event belongs to this user
        $this->userEvent($request, $request->event_id);

        $userId = $request->user()->dataOwnerId();

        $data = $request->only([
            'event_id', 'title', 'description', 'category', 'amount',
            'expense_date', 'payee_name', 'payee_phone', 'payee_upi',
            'payment_method', 'transaction_id', 'receipt_number', 'notes',
        ]);

        $data['user_id'] = $userId;

        // Upload receipt image if provided
        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')
                ->store('expenses/receipts', 'public');
        }

        $expense = Expense::create($data);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'create_expense',
            'model_type' => Expense::class,
            'model_id'   => $expense->id,
            'new_values' => $expense->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense added successfully',
            'data'    => $expense->load('event'),
        ], 201);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PUT /api/v1/expenses/{id}
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Update an existing expense entry.
     */
    public function update(Request $request, $id)
    {
        $expense = $this->userExpenses($request)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title'          => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'category'       => 'nullable|string|max:100',
            'amount'         => 'nullable|numeric|min:0',
            'expense_date'   => 'nullable|date',
            'payee_name'     => 'nullable|string|max:255',
            'payee_phone'    => 'nullable|string|max:30',
            'payee_upi'      => 'nullable|string|max:255',
            'payment_method' => 'nullable|in:cash,gpay,bank_transfer,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_image'  => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:5120',
            'notes'          => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $oldValues  = $expense->toArray();
        $updateData = $request->only([
            'title', 'description', 'category', 'amount', 'expense_date',
            'payee_name', 'payee_phone', 'payee_upi', 'payment_method',
            'transaction_id', 'receipt_number', 'notes',
        ]);

        // Handle receipt image upload (replace old one)
        if ($request->hasFile('receipt_image')) {
            if ($expense->receipt_image && Storage::disk('public')->exists($expense->receipt_image)) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $updateData['receipt_image'] = $request->file('receipt_image')
                ->store('expenses/receipts', 'public');
        }

        $expense->update($updateData);

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'update_expense',
            'model_type' => Expense::class,
            'model_id'   => $expense->id,
            'old_values' => $oldValues,
            'new_values' => $expense->toArray(),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data'    => $expense->load('event'),
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // DELETE /api/v1/expenses/{id}
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Delete an expense entry and its receipt image.
     */
    public function destroy(Request $request, $id)
    {
        $expense   = $this->userExpenses($request)->findOrFail($id);
        $oldValues = $expense->toArray();

        // Delete receipt image from storage
        if ($expense->receipt_image && Storage::disk('public')->exists($expense->receipt_image)) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        ActivityLog::create([
            'user_id'    => $request->user()->id,
            'action'     => 'delete_expense',
            'model_type' => Expense::class,
            'model_id'   => $id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/expenses/stats
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Overall expense stats (optionally filtered by event_id).
     */
    public function stats(Request $request)
    {
        $userId  = $request->user()->dataOwnerId();
        $eventId = $request->input('event_id');

        $query = Expense::where('user_id', $userId);

        if ($eventId) {
            // Verify event ownership
            $this->userEvent($request, $eventId);
            $query->where('event_id', $eventId);
        }

        $expenses = $query->get();

        // Overall totals
        $overall = [
            'total_amount'  => (float) $expenses->sum('amount'),
            'total_entries' => $expenses->count(),
        ];

        // By category
        $byCategory = $expenses->groupBy('category')->map(function ($items, $cat) {
            return [
                'category' => $cat,
                'count'    => $items->count(),
                'total'    => (float) $items->sum('amount'),
            ];
        })->values();

        // By payment method
        $byPayment = $expenses->groupBy('payment_method')->map(function ($items, $method) {
            return [
                'payment_method' => $method,
                'count'          => $items->count(),
                'total'          => (float) $items->sum('amount'),
            ];
        })->values();

        // Per-event breakdown (only when not filtered by event)
        $perEvent = [];
        if (!$eventId) {
            $perEvent = Expense::where('expenses.user_id', $userId)
                ->join('events', 'events.id', '=', 'expenses.event_id')
                ->selectRaw('expenses.event_id, events.title as event_title, SUM(expenses.amount) as total_amount, COUNT(*) as total_entries')
                ->groupBy('expenses.event_id', 'events.title')
                ->orderByDesc('total_amount')
                ->get()
                ->map(fn($row) => [
                    'event_id'     => (int)   $row->event_id,
                    'event_title'  =>          $row->event_title,
                    'total_amount' => (float)  $row->total_amount,
                    'total_entries'=> (int)    $row->total_entries,
                ]);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'overall'     => $overall,
                'by_category' => $byCategory,
                'by_payment'  => $byPayment,
                'per_event'   => $perEvent,
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // POST /api/v1/sync/expenses
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Sync offline expenses from mobile application database.
     */
    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'expenses' => 'required|array',
            'expenses.*.id'             => 'nullable|integer',
            'expenses.*.event_id'       => 'required|exists:events,id',
            'expenses.*.title'          => 'required|string|max:255',
            'expenses.*.category'       => 'required|string|max:100',
            'expenses.*.amount'         => 'required|numeric|min:0',
            'expenses.*.expense_date'   => 'required|date',
            'expenses.*.payment_method' => 'required|in:cash,gpay,bank_transfer,cheque,other',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $userId = $request->user()->dataOwnerId();
        $synced = [];

        foreach ($request->expenses as $expenseData) {
            // Verify event belongs to data owner
            $event = Event::where('user_id', $userId)->find($expenseData['event_id']);
            if (!$event) {
                continue;
            }

            $expenseData['user_id'] = $userId;

            if (isset($expenseData['id'])) {
                $expense = Expense::find($expenseData['id']);
                if ($expense && $expense->user_id === $userId) {
                    $expense->update($expenseData);
                    $synced[] = $expense;
                }
            } else {
                $synced[] = Expense::create($expenseData);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Expenses synced successfully',
            'data'    => $synced,
        ]);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // GET /api/v1/expenses/cash-ledger
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Cash Ledger — Cash In (chandla cash receipts) vs Cash Out (cash expenses).
     *
     * Query params (all optional):
     *   event_id   – filter both sides to a specific event
     *   from_date  – ISO date, inclusive
     *   to_date    – ISO date, inclusive
     *
     * Response shape:
     * {
     *   success: true,
     *   data: {
     *     summary: {
     *       cash_in:       <float>,
     *       cash_out:      <float>,
     *       net_balance:   <float>,   // cash_in - cash_out
     *       status:        "surplus" | "deficit" | "balanced"
     *     },
     *     cash_in_entries:  [ { id, date, event, giver_name, giver_phone, amount } … ],
     *     cash_out_entries: [ { id, date, event, title, payee_name, amount } … ]
     *   }
     * }
     */
    public function cashLedger(Request $request)
    {
        $userId = $request->user()->dataOwnerId();

        // ── Cash In (chandla entries paid in cash) ─────────────────────────
        $cashInQuery = Chandla::where('user_id', $userId)
            ->where('payment_method', 'cash')
            ->with('event');

        if ($request->filled('event_id')) {
            // Verify event belongs to user
            $this->userEvent($request, $request->event_id);
            $cashInQuery->where('event_id', $request->event_id);
        }
        if ($request->filled('from_date')) {
            $cashInQuery->where('received_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $cashInQuery->where('received_date', '<=', $request->to_date);
        }

        $cashInEntries = $cashInQuery
            ->orderBy('received_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($c) => [
                'id'          => $c->id,
                'date'        => $c->received_date?->toDateString(),
                'event_id'    => $c->event_id,
                'event_title' => $c->event?->title,
                'giver_name'  => $c->giver_name,
                'giver_phone' => $c->giver_phone,
                'category'    => $c->category,
                'amount'      => (float) $c->amount,
            ]);

        // ── Cash Out (expense entries paid in cash) ────────────────────────
        $cashOutQuery = Expense::where('user_id', $userId)
            ->where('payment_method', 'cash')
            ->with('event');

        if ($request->filled('event_id')) {
            $cashOutQuery->where('event_id', $request->event_id);
        }
        if ($request->filled('from_date')) {
            $cashOutQuery->where('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $cashOutQuery->where('expense_date', '<=', $request->to_date);
        }

        $cashOutEntries = $cashOutQuery
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->map(fn($e) => [
                'id'          => $e->id,
                'date'        => $e->expense_date?->toDateString(),
                'event_id'    => $e->event_id,
                'event_title' => $e->event?->title,
                'title'       => $e->title,
                'category'    => $e->category,
                'payee_name'  => $e->payee_name,
                'payee_phone' => $e->payee_phone,
                'amount'      => (float) $e->amount,
            ]);

        // ── Summary ────────────────────────────────────────────────────────
        $cashIn      = $cashInEntries->sum('amount');
        $cashOut     = $cashOutEntries->sum('amount');
        $netBalance  = round($cashIn - $cashOut, 2);
        $status      = $netBalance > 0 ? 'surplus' : ($netBalance < 0 ? 'deficit' : 'balanced');

        return response()->json([
            'success' => true,
            'data'    => [
                'summary' => [
                    'cash_in'     => round($cashIn,  2),
                    'cash_out'    => round($cashOut, 2),
                    'net_balance' => $netBalance,
                    'status'      => $status,
                ],
                'cash_in_entries'  => $cashInEntries,
                'cash_out_entries' => $cashOutEntries,
            ],
        ]);
    }

    /**
     * Download or stream PDF report for Expenses.
     * GET /api/v1/expenses/pdf
     */
    public function pdf(Request $request)
    {
        $query = $this->userExpenses($request)->with('event');

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        if ($request->filled('from_date')) {
            $query->where('expense_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->get();

        $selectedEvent = null;
        $parts = [];
        if ($request->filled('event_id')) {
            $selectedEvent = Event::find($request->event_id);
            $parts[] = 'Event: ' . ($selectedEvent?->title ?? 'ID ' . $request->event_id);
        }
        if ($request->filled('category'))       { $parts[] = 'Category: ' . ucfirst($request->category); }
        if ($request->filled('payment_method')) { $parts[] = 'Payment: ' . str_replace('_', ' ', $request->payment_method); }
        if ($request->filled('from_date'))      { $parts[] = 'From: ' . $request->from_date; }
        if ($request->filled('to_date'))        { $parts[] = 'To: '   . $request->to_date; }
        $filterLabel = $parts ? implode(' · ', $parts) : 'All expenses';

        $pdf = Pdf::loadView('client.expenses.pdf', compact('expenses', 'filterLabel', 'selectedEvent'))
            ->setPaper('a4', 'portrait');

        try {
            $dompdf      = $pdf->getDomPDF();
            $canvas      = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font        = $fontMetrics->get_font('DejaVu Sans', 'normal');
            if ($font) {
                $muted = [0.38, 0.41, 0.45];
                $canvas->page_text(34,  806, 'Chandla Book · Expense Register', $font, 7.5, $muted);
                $canvas->page_text(238, 806, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 9, [0.18, 0.23, 0.29]);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Expense PDF footer skipped: ' . $e->getMessage());
        }

        $filename = 'expense_report_' . date('Y-m-d') . '.pdf';

        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}


