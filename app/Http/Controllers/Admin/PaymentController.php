<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\MarriageInvitation;
use App\Models\MatrimonialPlan;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Models\UPITransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PlanActivatedMail;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'razorpay');
        
        if ($tab === 'razorpay') {
            $query = PaymentTransaction::with('user');
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('transaction_number', 'like', '%' . $request->search . '%')
                      ->orWhere('razorpay_order_id', 'like', '%' . $request->search . '%')
                      ->orWhere('razorpay_payment_id', 'like', '%' . $request->search . '%');
                });
            }
            
            $payments = $query->orderBy('created_at', 'desc')->paginate(20);
        } else {
            $query = UPITransaction::with('user');
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('transaction_id', 'like', '%' . $request->search . '%')
                      ->orWhere('razorpay_order_id', 'like', '%' . $request->search . '%');
                });
            }
            
            $payments = $query->orderBy('created_at', 'desc')->paginate(20);
        }

        $stats = [
            'total_razorpay' => PaymentTransaction::count(),
            'success_razorpay' => PaymentTransaction::where('status', PaymentTransaction::STATUS_SUCCESS)->count(),
            'total_upi' => UPITransaction::count(),
            'completed_upi' => UPITransaction::where('status', 'completed')->count(),
            'pending_upi' => UPITransaction::where('status', 'pending')->count(),
            'total_revenue' => PaymentTransaction::where('status', PaymentTransaction::STATUS_SUCCESS)->sum('amount_inr') + 
                               UPITransaction::where('status', 'completed')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'stats', 'tab'));
    }

    public function show($id, Request $request)
    {
        $type = $request->query('type');
        
        if ($type === 'razorpay' || str_starts_with((string)$id, 'TXN-')) {
            $payment = PaymentTransaction::with('user')->where('transaction_number', $id)->orWhere('id', $id)->firstOrFail();
            return view('admin.payments.show', [
                'payment' => $payment,
                'isRazorpay' => true,
            ]);
        }
        
        $payment = UPITransaction::with(['user', 'event'])->findOrFail($id);
        return view('admin.payments.show', [
            'payment' => $payment,
            'isRazorpay' => false,
        ]);
    }

    public function verify(Request $request, $id)
    {
        $payment = UPITransaction::with('event')->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:completed,failed',
        ]);

        $payment->status = $validated['status'];
        if ($validated['status'] === 'completed') {
            $payment->paid_at = now();
        }
        $payment->save();

        $metadata = $payment->metadata ?? [];
        if ($validated['status'] === 'completed' && ($metadata['type'] ?? null) === 'plan') {
            $eventId = $metadata['event_id'] ?? $payment->event_id;
            $plan = $metadata['plan'] ?? null;
            if ($eventId && $plan) {
                $event = Event::find($eventId);
                if ($event) {
                    $event->pricing_plan = $plan;
                    if ($plan === 'unlimited') {
                        $event->unlimited_purchased_at = now();
                    } else {
                        $event->unlimited_purchased_at = null;
                    }
                    $event->save();

                    $buyer = $event->user;
                    if ($buyer && filter_var($buyer->email, FILTER_VALIDATE_EMAIL)) {
                        try {
                            Mail::to($buyer->email)->send(new PlanActivatedMail($event));
                        } catch (\Exception $e) {
                            Log::error('Failed to send PlanActivatedMail', ['error' => $e->getMessage(), 'user_id' => $buyer->id]);
                        }
                    }
                }
            }
        }

        if ($validated['status'] === 'completed' && ($metadata['type'] ?? null) === 'marriage_invitation') {
            $invitationId = $metadata['marriage_invitation_id'] ?? null;
            if ($invitationId) {
                $invitation = MarriageInvitation::find($invitationId);
                if ($invitation) {
                    $invitation->paid_at = now();
                    $invitation->save();
                }
            }
        }

        if ($validated['status'] === 'completed' && ($metadata['type'] ?? null) === 'matrimonial_plan') {
            if (! MatrimonialPlan::where('upi_transaction_id', $payment->id)->exists()) {
                $planKey = $metadata['matrimonial_plan'] ?? null;
                $def = is_string($planKey) ? config("matrimonial.plans.{$planKey}") : null;
                $validMatrimonialKeys = array_merge(
                    array_keys(config('matrimonial.plans', [])),
                    [MatrimonialPlan::TYPE_6M, MatrimonialPlan::TYPE_12M, MatrimonialPlan::TYPE_500, MatrimonialPlan::TYPE_200]
                );
                if ($def && is_string($planKey) && in_array($planKey, array_unique($validMatrimonialKeys), true)) {
                    $start = Carbon::today();
                    $expiry = (clone $start)->addMonths((int) $def['months'])->endOfDay();
                    MatrimonialPlan::create([
                        'user_id' => $payment->user_id,
                        'upi_transaction_id' => $payment->id,
                        'plan_type' => $planKey,
                        'price' => (float) $def['price_inr'],
                        'start_date' => $start,
                        'expiry_date' => $expiry->toDateString(),
                        'payment_id' => 'UPI:' . $payment->transaction_id,
                        'razorpay_order_id' => null,
                    ]);
                }
            }
        }

        if ($validated['status'] === 'completed' && ($metadata['type'] ?? null) === 'plan' && $payment->event) {
            $buyer = $payment->event->user;
            if ($buyer && $buyer->referred_by && $buyer->referral_rewarded_at === null) {
                $referrer = User::find($buyer->referred_by);
                if ($referrer) {
                    $referrer->free_event_credits = ((int) $referrer->free_event_credits) + 1;
                    $referrer->save();
                    $buyer->referral_rewarded_at = now();
                    $buyer->save();
                }
            }
        }

        return back()->with('success', 'Payment status updated.');
    }

    public function export(Request $request)
    {
        $tab = $request->query('tab', 'razorpay');

        if ($tab === 'razorpay') {
            $headers = ['Transaction Number', 'User', 'Package Key', 'Package Name', 'Amount (INR)', 'Status', 'Order ID', 'Payment ID', 'Created At'];
            $query = PaymentTransaction::with('user');
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('transaction_number', 'like', '%' . $request->search . '%')
                      ->orWhere('razorpay_order_id', 'like', '%' . $request->search . '%')
                      ->orWhere('razorpay_payment_id', 'like', '%' . $request->search . '%');
                });
            }
            $rows = $query->orderBy('created_at', 'desc')->get()->map(function($t) {
                return [
                    $t->transaction_number,
                    $t->user->name ?? 'N/A',
                    $t->package_key,
                    $t->package_name,
                    $t->amount_inr,
                    $t->status,
                    $t->razorpay_order_id,
                    $t->razorpay_payment_id,
                    $t->created_at->toDateTimeString(),
                ];
            })->toArray();
        } else {
            $headers = ['Transaction ID', 'User', 'Amount', 'Status', 'Payment Method', 'Created At'];
            $query = UPITransaction::with('user');
            if ($request->status) {
                $query->where('status', $request->status);
            }
            if ($request->search) {
                $query->where('transaction_id', 'like', '%' . $request->search . '%');
            }
            $rows = $query->orderBy('created_at', 'desc')->get()->map(function($t) {
                return [
                    $t->transaction_id,
                    $t->user->name ?? 'N/A',
                    $t->amount,
                    $t->status,
                    $t->payment_method,
                    $t->created_at->toDateTimeString(),
                ];
            })->toArray();
        }

        $callback = function() use ($headers, $rows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($rows as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments_' . $tab . '_' . date('Ymd_His') . '.csv"',
        ]);
    }
}
