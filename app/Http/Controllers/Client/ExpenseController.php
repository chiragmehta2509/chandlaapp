<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    private function allowedUserIds(): array
    {
        return Auth::user()->allowedUserIds();
    }

    private function userEvents()
    {
        return Event::whereIn('user_id', $this->allowedUserIds())
            ->where('is_archived', false)
            ->orderBy('event_date', 'desc')
            ->get();
    }

    public function index(Request $request)
    {
        $query = Expense::whereIn('user_id', $this->allowedUserIds())->with('event');

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
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('payee_name', 'like', "%{$s}%");
            });
        }

        $expenses   = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->get();
        $events     = $this->userEvents();
        $categories = Expense::categories();

        // Stats
        $totalAmount   = $expenses->sum('amount');
        $categoryTotals = $expenses->groupBy('category')->map(fn($g) => $g->sum('amount'));

        return view('client.expenses.index', compact(
            'expenses', 'events', 'categories', 'totalAmount', 'categoryTotals'
        ));
    }

    public function create(Request $request)
    {
        $events     = $this->userEvents();
        $categories = Expense::categories();
        $selectedEventId = $request->event_id;

        return view('client.expenses.create', compact('events', 'categories', 'selectedEventId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id'       => 'required|integer|exists:events,id',
            'title'          => 'required|string|max:255',
            'category'       => 'required|string|max:100',
            'amount'         => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'payee_name'     => 'nullable|string|max:255',
            'payee_phone'    => 'nullable|string|max:30',
            'payee_upi'      => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,gpay,bank_transfer,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'description'    => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        // Ensure event belongs to user
        Event::whereIn('user_id', $this->allowedUserIds())->findOrFail($validated['event_id']);

        $validated['user_id'] = Auth::user()->dataOwnerId();

        if ($request->hasFile('receipt_image')) {
            $validated['receipt_image'] = $request->file('receipt_image')
                ->store('expenses/receipts', 'public');
        }

        Expense::create($validated);

        return redirect()->route('client.expenses.index')
            ->with('success', 'Expense added successfully.');
    }

    public function show($id)
    {
        $expense = Expense::whereIn('user_id', $this->allowedUserIds())->with('event')->findOrFail($id);
        return view('client.expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense    = Expense::whereIn('user_id', $this->allowedUserIds())->findOrFail($id);
        $events     = $this->userEvents();
        $categories = Expense::categories();

        return view('client.expenses.edit', compact('expense', 'events', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::whereIn('user_id', $this->allowedUserIds())->findOrFail($id);

        $validated = $request->validate([
            'event_id'       => 'required|integer|exists:events,id',
            'title'          => 'required|string|max:255',
            'category'       => 'required|string|max:100',
            'amount'         => 'required|numeric|min:0',
            'expense_date'   => 'required|date',
            'payee_name'     => 'nullable|string|max:255',
            'payee_phone'    => 'nullable|string|max:30',
            'payee_upi'      => 'nullable|string|max:255',
            'payment_method' => 'required|in:cash,gpay,bank_transfer,cheque,other',
            'transaction_id' => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'receipt_image'  => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            'description'    => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        if ($request->hasFile('receipt_image')) {
            if ($expense->receipt_image && Storage::disk('public')->exists($expense->receipt_image)) {
                Storage::disk('public')->delete($expense->receipt_image);
            }
            $validated['receipt_image'] = $request->file('receipt_image')
                ->store('expenses/receipts', 'public');
        }

        $expense->update($validated);

        return redirect()->route('client.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function pdf(Request $request)
    {
        $query = Expense::whereIn('user_id', $this->allowedUserIds())->with('event');

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

        // Build a human-readable label for the cover page
        $parts = [];
        if ($request->filled('event_id')) {
            $ev = Event::find($request->event_id);
            $parts[] = 'Event: ' . ($ev?->title ?? 'ID ' . $request->event_id);
        }
        if ($request->filled('category'))       { $parts[] = 'Category: ' . ucfirst($request->category); }
        if ($request->filled('payment_method')) { $parts[] = 'Payment: ' . str_replace('_', ' ', $request->payment_method); }
        if ($request->filled('from_date'))      { $parts[] = 'From: ' . $request->from_date; }
        if ($request->filled('to_date'))        { $parts[] = 'To: '   . $request->to_date; }
        $filterLabel = $parts ? implode(' · ', $parts) : 'All expenses';

        $pdf = Pdf::loadView('client.expenses.pdf', compact('expenses', 'filterLabel'));

        // Running footer — page numbers
        try {
            $dompdf      = $pdf->getDomPDF();
            $canvas      = $dompdf->getCanvas();
            $fontMetrics = $dompdf->getFontMetrics();
            $font        = $fontMetrics->get_font('DejaVu Sans', 'normal');
            if ($font) {
                $muted = [0.38, 0.41, 0.45];
                $canvas->page_text(34,  806, 'Chandla Book · ' . config('app.name'), $font, 7.5, $muted);
                $canvas->page_text(238, 806, 'Page {PAGE_NUM} of {PAGE_COUNT}', $font, 9, [0.18, 0.23, 0.29]);
            }
        } catch (\Throwable) {}

        $filename = 'expense-register-' . now()->format('Y-m-d') . '.pdf';
        return response($pdf->output())
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function destroy($id)
    {
        $expense = Expense::whereIn('user_id', $this->allowedUserIds())->findOrFail($id);

        if ($expense->receipt_image && Storage::disk('public')->exists($expense->receipt_image)) {
            Storage::disk('public')->delete($expense->receipt_image);
        }

        $expense->delete();

        return redirect()->route('client.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }
}
