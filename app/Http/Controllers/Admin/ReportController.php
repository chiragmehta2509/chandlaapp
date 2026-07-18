<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chandla;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function chandlaReport(Request $request)
    {
        $query = Chandla::with(['event', 'user']);

        // Filter by event
        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by date range
        if ($request->start_date && $request->end_date) {
            $query->whereBetween('received_date', [$request->start_date, $request->end_date]);
        } elseif ($request->date) {
            $query->whereDate('received_date', $request->date);
        }

        // Filter by category
        if ($request->category) {
            $query->where('category', $request->category);
        }

        // Filter by payment method
        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $chandlas = $query->orderBy('received_date', 'desc')->get();

        // Calculate statistics
        $stats = [
            'total_count' => $chandlas->count(),
            'total_amount' => $chandlas->sum('amount'),
            'by_category' => $chandlas->groupBy('category')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'amount' => $items->sum('amount'),
                ];
            }),
            'by_payment_method' => $chandlas->groupBy('payment_method')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'amount' => $items->sum('amount'),
                ];
            }),
            'by_event' => $chandlas->groupBy('event_id')->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'amount' => $items->sum('amount'),
                    'event' => $items->first()->event,
                ];
            }),
        ];

        $events = Event::orderBy('event_date', 'desc')->get();

        return view('admin.reports.chandla', compact('chandlas', 'stats', 'events'));
    }

    public function exportChandlaReport(Request $request)
    {
        $query = Chandla::with(['event', 'user']);

        if ($request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('received_date', [$request->start_date, $request->end_date]);
        } elseif ($request->date) {
            $query->whereDate('received_date', $request->date);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        $chandlas = $query->orderBy('received_date', 'desc')->get();

        // Generate CSV
        $filename = 'chandla_report_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($chandlas) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, [
                'Date',
                'Event',
                'Giver Name',
                'Phone',
                'Email',
                'Category',
                'Payment Method',
                'Amount',
                'Receipt Number',
                'Notes'
            ]);

            // Data rows
            foreach ($chandlas as $chandla) {
                fputcsv($file, [
                    $chandla->received_date->format('Y-m-d'),
                    $chandla->event->title,
                    $chandla->giver_name,
                    $chandla->giver_phone,
                    $chandla->giver_email,
                    ucfirst($chandla->category),
                    $chandla->payment_method_label,
                    $chandla->amount,
                    $chandla->receipt_number,
                    $chandla->notes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function eventSummary(Request $request)
    {
        $query = Event::with('user')
            ->withCount('chandlas')
            ->withSum('chandlas', 'amount');

        if ($request->event_id) {
            $query->where('id', $request->event_id);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->start_date && $request->end_date) {
            $query->whereBetween('event_date', [$request->start_date, $request->end_date]);
        }

        $events = $query->orderBy('event_date', 'desc')->get();

        $summaries = $events->map(function ($event) {
            $chandlaCount = (int) ($event->chandlas_count ?? 0);
            $amount = (float) ($event->chandlas_sum_amount ?? 0);
            $freeLimit = min((int) ($event->free_entry_limit ?? 50), 50);
            $plan = $event->pricing_plan ?? 'free';
            $perEntryPrice = (float) ($event->per_entry_price ?? 1);
            $unlimitedPrice = (float) ($event->unlimited_price ?? 500);
            $extraEntries = max(0, $chandlaCount - $freeLimit);
            $usageFee = $plan === 'payg' ? $extraEntries * $perEntryPrice : 0;
            $planFee = $plan === 'unlimited' ? $unlimitedPrice : 0;

            return [
                'event' => $event,
                'user' => $event->user,
                'chandla_count' => $chandlaCount,
                'total_amount' => $amount,
                'plan' => $plan,
                'free_limit' => $freeLimit,
                'extra_entries' => $extraEntries,
                'usage_fee' => $usageFee,
                'plan_fee' => $planFee,
                'total_fee' => $usageFee + $planFee,
            ];
        });

        $eventOptions = Event::orderBy('event_date', 'desc')->get();
        $userOptions = User::where('is_deleted', false)->orderBy('name')->get();

        return view('admin.reports.event-summary', [
            'summaries' => $summaries,
            'events' => $eventOptions,
            'users' => $userOptions,
        ]);
    }
}
