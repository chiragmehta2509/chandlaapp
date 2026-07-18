<?php

namespace App\Http\Controllers\Api\Transaction;

use App\Http\Controllers\Controller;
use App\Models\PackPaymentReceipt;
use App\Models\PaymentTransaction;
use App\Models\UPITransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $authUser */
        $authUser = $request->user();
        $userId = $authUser->isFamilyMember() ? $authUser->id : $authUser->dataOwnerId();

        // 1. Fetch unified transactions
        $newTxns = PaymentTransaction::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        $now = now();
        foreach ($newTxns as $txn) {
            if ($txn->isPending() && $txn->created_at->diffInMinutes($now) >= 15) {
                $txn->status = PaymentTransaction::STATUS_FAILED;
                $txn->failure_reason = 'Cancelled due to inactivity (15m timeout).';
                $txn->save();
            }
        }

        $excludePaymentIds = $newTxns->whereNotNull('razorpay_payment_id')->pluck('razorpay_payment_id')->toArray();

        $mappedNewTxns = $newTxns->map(function (PaymentTransaction $t) {
            $isLgc = in_array($t->package_key, ['matrimonial_200', 'matrimonial_500'], true);
            return [
                'kind' => $isLgc ? 'matrimonial' : 'pack',
                'date' => ($t->paid_at ?: $t->created_at)->toIso8601String(),
                'amount' => (float) $t->amount_inr,
                'status' => $t->status === PaymentTransaction::STATUS_SUCCESS ? 'completed' : ($t->status === PaymentTransaction::STATUS_PENDING || $t->status === PaymentTransaction::STATUS_PROCESSING ? 'pending' : 'failed'),
                'reference' => $t->razorpay_payment_id ?: $t->razorpay_order_id,
                'method' => $t->payment_method ?: 'razorpay',
                'title' => $t->package_name,
                'subtitle' => 'Transaction #' . $t->transaction_number,
                'pack_type' => $t->package_key,
                'txn_number' => $t->transaction_number,
            ];
        });

        // 2. Fetch old pack receipts
        $packs = PackPaymentReceipt::where('user_id', $userId)
            ->whereNotIn('razorpay_payment_id', $excludePaymentIds)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (PackPaymentReceipt $r) {
                return [
                    'kind' => 'pack',
                    'date' => $r->created_at->toIso8601String(),
                    'amount' => $r->amount_paise / 100,
                    'status' => 'completed',
                    'reference' => $r->razorpay_payment_id,
                    'method' => 'razorpay',
                    'title' => $this->packLabel($r->pack_type),
                    'subtitle' => 'Plan / pack purchase',
                    'pack_type' => $r->pack_type,
                    'txn_number' => null,
                ];
            });

        // 3. Fetch old matrimonial subscriptions
        $matrimonial = \App\Models\MatrimonialPlan::where('user_id', $userId)
            ->whereNull('upi_transaction_id')
            ->whereNotIn('payment_id', $excludePaymentIds)
            ->orderByDesc('created_at')
            ->get()
            ->map(function (\App\Models\MatrimonialPlan $p) {
                $planDef = config("matrimonial.plans.{$p->plan_type}");
                $months = $planDef['months'] ?? 3;
                return [
                    'kind' => 'matrimonial',
                    'date' => $p->created_at->toIso8601String(),
                    'amount' => (float) $p->price,
                    'status' => 'completed',
                    'reference' => $p->payment_id,
                    'method' => 'razorpay',
                    'title' => 'Find Partner Plan (' . $months . ' Months)',
                    'subtitle' => 'Matrimonial subscription',
                    'pack_type' => null,
                    'txn_number' => null,
                ];
            });

        // 4. Fetch old UPI/GPay transactions
        $upi = UPITransaction::where('user_id', $userId)
            ->where(function($q) use ($excludePaymentIds) {
                $q->whereNull('razorpay_payment_id')
                  ->orWhereNotIn('razorpay_payment_id', $excludePaymentIds);
            })
            ->with('event:id,title')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (UPITransaction $t) {
                return [
                    'kind' => 'upi',
                    'date' => ($t->paid_at ?: $t->created_at)->toIso8601String(),
                    'amount' => (float) $t->amount,
                    'status' => $t->status === 'success' ? 'completed' : ($t->status === 'pending' || $t->status === 'processing' ? 'pending' : 'failed'),
                    'reference' => $t->razorpay_payment_id ?: $t->transaction_id,
                    'method' => $t->payment_method,
                    'title' => $t->event?->title ? ('Event: ' . $t->event->title) : 'Event payment',
                    'subtitle' => $t->description ?: 'UPI / GPay payment',
                    'pack_type' => null,
                    'txn_number' => null,
                ];
            });

        $merged = $mappedNewTxns->concat($packs)->concat($matrimonial)->concat($upi)
            ->sortByDesc('date')
            ->values();

        // Apply filters
        $filterStatus = $request->query('status');
        if ($filterStatus !== null && $filterStatus !== '') {
            $mappedStatus = match($filterStatus) {
                'success', 'completed' => 'completed',
                'in_process', 'process', 'pending' => 'pending',
                'failed' => 'failed',
                'refunded' => 'refunded',
                default => $filterStatus,
            };
            $merged = $merged->filter(function ($tx) use ($mappedStatus) {
                return $tx['status'] === $mappedStatus;
            })->values();
        }

        $filterKind = $request->query('kind');
        if (in_array($filterKind, ['pack', 'upi', 'matrimonial'], true)) {
            $merged = $merged->where('kind', $filterKind)->values();
        }

        // Totals
        $totalSpent = $merged->where('status', 'completed')->sum('amount');
        $packCount = $merged->where('kind', 'pack')->where('status', 'completed')->count();
        $upiCount = $merged->where('kind', 'upi')->where('status', 'completed')->count();

        // Paginate manually
        $perPage = $request->get('per_page', 20);
        $page = max(1, (int) $request->query('page', 1));
        $totalRows = $merged->count();
        $items = $merged->slice(($page - 1) * $perPage, $perPage)->values();

        $transactions = new LengthAwarePaginator(
            $items,
            $totalRows,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return response()->json([
            'success' => true,
            'data' => $transactions,
            'summary' => [
                'total_spent' => (float) $totalSpent,
                'pack_count' => (int) $packCount,
                'upi_count' => (int) $upiCount,
            ]
        ]);
    }

    public function show(Request $request, string $txnNumber)
    {
        /** @var User $authUser */
        $authUser = $request->user();
        $userId = $authUser->isFamilyMember() ? $authUser->id : $authUser->dataOwnerId();

        $transaction = PaymentTransaction::where('transaction_number', $txnNumber)
            ->where('user_id', $userId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $transaction
        ]);
    }

    private function packLabel(string $packType): string
    {
        return match ($packType) {
            'celebration' => 'Celebration Plan (₹' . config('packs.celebration.amount_inr', 300) . ')',
            'ledger_duo' => 'Host Plus Plan (₹' . config('packs.ledger_duo.amount_inr', 500) . ')',
            'family' => 'Family Plan (₹' . config('packs.family.amount_inr', 600) . ')',
            'premium_bundle' => 'Premium Host Plan (₹' . config('packs.premium_bundle.amount_inr', 700) . ')',
            'guest_pay_single' => 'Guest Contribution — single event (₹' . config('packs.guest_pay_single.amount_inr', 400) . ')',
            default => ucfirst(str_replace('_', ' ', $packType)),
        };
    }
}
