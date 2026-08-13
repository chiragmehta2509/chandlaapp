<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\EventTypeController as AdminEventTypeController;
use App\Http\Controllers\Admin\ChandlaController as AdminChandlaController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Client\EventController as ClientEventController;
use App\Http\Controllers\Client\ChandlaController as ClientChandlaController;
use App\Http\Controllers\Client\ContactController as ClientContactController;
use App\Http\Controllers\Client\QRCodeController as ClientQRCodeController;
use App\Http\Controllers\Client\CashInventoryController as ClientCashInventoryController;
use App\Http\Controllers\Client\GPayController as ClientGPayController;
use App\Http\Controllers\Client\PlanPaymentController as ClientPlanPaymentController;
use App\Http\Controllers\Client\DirectGpayUnlockController as ClientDirectGpayUnlockController;
use App\Http\Controllers\Client\MarriageInvitationController as ClientMarriageInvitationController;
use App\Http\Controllers\Client\PreWeddingController as ClientPreWeddingController;
use App\Http\Controllers\Client\PackPurchaseController as ClientPackPurchaseController;
use App\Http\Controllers\Client\SupportController as ClientSupportController;
use App\Http\Controllers\Client\FamilyMemberController as ClientFamilyMemberController;
use App\Http\Controllers\Client\TransactionHistoryController as ClientTransactionHistoryController;
use App\Http\Controllers\Client\GanpatiController as ClientGanpatiController;
use App\Http\Controllers\Client\ExpenseController as ClientExpenseController;
use App\Http\Controllers\Public\PaymentController as PublicPaymentController;
use App\Http\Controllers\Public\DirectGPayController as PublicDirectGPayController;
use App\Http\Controllers\Public\SeoController as PublicSeoController;
use App\Http\Controllers\Matrimonial\MatrimonialController;
use App\Http\Controllers\Matrimonial\MatrimonialInterestController;
use App\Http\Controllers\Matrimonial\MatrimonialPlanController;
use App\Http\Controllers\Webhooks\MatrimonialRazorpayWebhookController;

Route::view('/', 'public.home')->name('public.home');

// Fallback to serve files from storage/app/public when the public/storage symlink is missing or
// not followed by Apache (common on shared hosting). Triggered only if a real file isn't already
// served by the symlink, since web servers handle real files before passing requests to PHP.
Route::get('/storage/{path}', function (string $path) {
    $path = ltrim($path, '/');
    if (str_contains($path, '..')) {
        abort(404);
    }
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    if (! $disk->exists($path)) {
        abort(404);
    }
    return response()->file($disk->path($path), [
        'Cache-Control' => 'public, max-age=86400',
    ]);
})->where('path', '.*')->name('storage.fallback');

Route::view('/privacy', 'public.privacy')->name('public.privacy');
Route::view('/terms',   'public.terms')->name('public.terms');
Route::view('/refund',  'public.refund')->name('public.refund');
Route::view('/about',   'public.about')->name('public.about');
Route::get('/contact', [PublicSeoController::class, 'contact'])->name('public.contact');
Route::get('/robots.txt', [PublicSeoController::class, 'robots'])->name('public.robots');
Route::get('/sitemap.xml', [PublicSeoController::class, 'sitemap'])->name('public.sitemap');
Route::get('/packages/{slug}', [PublicSeoController::class, 'package'])->name('public.package');

/** Razorpay: auto-activate Find Partner after Payment Link (configure secret + URL in dashboard). */
Route::post('/webhooks/razorpay', MatrimonialRazorpayWebhookController::class);
Route::post('/webhooks/razorpay-payments', [\App\Http\Controllers\Webhooks\RazorpayWebhookController::class, 'handle']);

/** WhatsApp Cloud API webhook (Meta verification + event callbacks). */
Route::get('/webhooks/whatsapp',  [\App\Http\Controllers\Webhooks\WhatsAppWebhookController::class, 'verify']);
Route::post('/webhooks/whatsapp', [\App\Http\Controllers\Webhooks\WhatsAppWebhookController::class, 'handle']);

Route::view('/docs', 'swagger');

// Public payment routes used by QR links
Route::get('/payment/{event}/{token?}', [PublicPaymentController::class, 'showPaymentForm'])->name('public.payment');
Route::post('/payment/{event}/{token?}', [PublicPaymentController::class, 'submitPayment'])->name('public.payment.submit');

// Direct GPay: QR opens site → form → UPI deep link → record Chandla (GPAY GPAY)
Route::get('/e/{event}/gpay', [PublicDirectGPayController::class, 'show'])->whereNumber('event')->name('public.direct-gpay');
Route::post('/e/{event}/gpay/prepare', [PublicDirectGPayController::class, 'prepare'])->whereNumber('event')->name('public.direct-gpay.prepare');
Route::get('/e/{event}/gpay/pay', [PublicDirectGPayController::class, 'pay'])->whereNumber('event')->name('public.direct-gpay.pay');
Route::post('/e/{event}/gpay/complete', [PublicDirectGPayController::class, 'complete'])->whereNumber('event')->name('public.direct-gpay.complete');

// Password reset routes (used by Laravel reset link emails)
Route::get('/client/forgot-password', [ClientAuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/client/forgot-password', [ClientAuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/client/reset-password/{token}', [ClientAuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/client/reset-password', [ClientAuthController::class, 'resetPassword'])->name('password.update');

// Matrimonial (Find Partner) — same client login; no changes to other modules
Route::middleware(['auth:web'])->prefix('matrimonial')->name('client.matrimonial.')->group(function () {
    Route::get('/', [MatrimonialController::class, 'index'])->name('index');
    Route::get('/profiles/{matrimonialProfile}', [MatrimonialController::class, 'show'])->name('profiles.show');
    Route::get('/profile/edit', [MatrimonialController::class, 'profileEdit'])->name('profile.edit');
    Route::post('/profile', [MatrimonialController::class, 'profileUpdate'])->name('profile.update');

    Route::get('/plans', [MatrimonialPlanController::class, 'showPlans'])->name('plans');
    Route::post('/plans/order', [MatrimonialPlanController::class, 'createOrder'])->name('plans.order');
    Route::post('/plans/verify', [MatrimonialPlanController::class, 'verify'])->name('plans.verify');

    Route::get('/interests', [MatrimonialInterestController::class, 'index'])->name('interests.index');
    Route::get('/interest-privacy', [MatrimonialInterestController::class, 'blocks'])->name('interest-privacy');
    Route::post('/interest-blocks', [MatrimonialInterestController::class, 'block'])->name('interest-blocks.store');
    Route::post('/interest-blocks/{blockedUserId}/remove', [MatrimonialInterestController::class, 'unblock'])->name('interest-blocks.remove')->whereNumber('blockedUserId');

    Route::middleware('check.matrimonial.plan')->group(function () {
        Route::post('/interests', [MatrimonialInterestController::class, 'store'])->name('interests.store');
        Route::post('/interests/{id}/accept', [MatrimonialInterestController::class, 'accept'])->name('interests.accept');
        Route::post('/interests/{id}/reject', [MatrimonialInterestController::class, 'reject'])->name('interests.reject');
    });
});

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Auth Routes
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Protected Routes
    Route::middleware(['admin.auth'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Users Management
        Route::resource('users', AdminUserController::class);
        Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
        
        // Events Management
        Route::resource('events', AdminEventController::class)->only(['index', 'show', 'destroy']);

        // Event Types Management
        Route::resource('event-types', AdminEventTypeController::class);

        // Chandlas Management
        Route::resource('chandlas', AdminChandlaController::class)->only(['index', 'show', 'destroy']);
        Route::post('/chandlas/{id}/verify', [AdminChandlaController::class, 'verify'])->name('chandlas.verify');
        
        // Contacts Management
        Route::resource('contacts', AdminContactController::class)->only(['index', 'show']);
        
        // Payments Management
        Route::get('/payments/export', [AdminPaymentController::class, 'export'])->name('payments.export');
        Route::resource('payments', AdminPaymentController::class)->only(['index', 'show']);
        Route::post('/payments/{id}/verify', [AdminPaymentController::class, 'verify'])->name('payments.verify');

        // Reports
        Route::get('/reports/chandla', [AdminReportController::class, 'chandlaReport'])->name('reports.chandla');
        Route::get('/reports/chandla/export', [AdminReportController::class, 'exportChandlaReport'])->name('reports.chandla.export');
        Route::get('/reports/events-summary', [AdminReportController::class, 'eventSummary'])->name('reports.events-summary');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'create'])->name('notifications.create');
        Route::post('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'store'])->name('notifications.store');

        // Plan Management
        Route::get('/plans', [AdminPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [AdminPlanController::class, 'createPack'])->name('plans.create');
        Route::post('/plans', [AdminPlanController::class, 'storePack'])->name('plans.store');
        Route::get('/plans/{id}/edit', [AdminPlanController::class, 'editPack'])->name('plans.edit');
        Route::put('/plans/{id}', [AdminPlanController::class, 'updatePack'])->name('plans.update');
        Route::delete('/plans/{id}', [AdminPlanController::class, 'destroyPack'])->name('plans.destroy');
        Route::get('/plans/{level}/subscribers', [AdminPlanController::class, 'subscribers'])->name('plans.subscribers')->whereNumber('level');
        Route::post('/plans/grant', [AdminPlanController::class, 'grantPlan'])->name('plans.grant');
        Route::post('/plans/revoke', [AdminPlanController::class, 'revokePlan'])->name('plans.revoke');
    });
});

// Client Portal Routes
Route::prefix('client')->name('client.')->group(function () {
    // Client Auth Routes
    Route::get('/login', [ClientAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [ClientAuthController::class, 'login']);
    Route::get('/register', [ClientAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [ClientAuthController::class, 'register']);
    Route::get('/account_verification/{token}', [ClientAuthController::class, 'verifyAccountLink'])->name('register.verify.link');
    Route::get('/register/verify', [ClientAuthController::class, 'showOtpVerificationForm'])->name('register.verify');
    Route::post('/register/verify', [ClientAuthController::class, 'verifyOtp'])->name('register.verify.submit');
    Route::post('/logout', [ClientAuthController::class, 'logout'])->name('logout');    // Client Protected Routes
    Route::middleware(['auth:web', 'force.password.change', 'family.readonly'])->group(function () {
        Route::get('/change-password', [ClientAuthController::class, 'showChangePasswordForm'])->name('password.edit');
        Route::post('/change-password', [ClientAuthController::class, 'changePassword'])->name('password.update');
        Route::get('/profile', [ClientAuthController::class, 'showProfile'])->name('profile');
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
        Route::get('/plans', [ClientSupportController::class, 'plans'])->name('plans');
        Route::get('/faq', [ClientSupportController::class, 'faq'])->name('faq');
        Route::get('/contact', [ClientSupportController::class, 'contact'])->name('contact');
        Route::view('/about', 'public.about')->name('about');

        // Family Members (read-only sub-accounts)
        Route::get('/family-members', [ClientFamilyMemberController::class, 'index'])->name('family-members.index');
        Route::post('/family-members', [ClientFamilyMemberController::class, 'store'])->name('family-members.store');
        Route::delete('/family-members/{id}', [ClientFamilyMemberController::class, 'destroy'])->whereNumber('id')->name('family-members.destroy');
        Route::post('/family-members/{id}/reset-password', [ClientFamilyMemberController::class, 'resetPassword'])->whereNumber('id')->name('family-members.reset-password');
        Route::post('/family-members/{id}/role', [ClientFamilyMemberController::class, 'updateRole'])->whereNumber('id')->name('family-members.update-role');

        // Transaction history (pack purchases + UPI/event payments)
        Route::get('/transactions', [ClientTransactionHistoryController::class, 'index'])->name('transactions.index');

        Route::get('/direct-gpay-unlock', [ClientDirectGpayUnlockController::class, 'redirectLegacy'])->name('direct-gpay-unlock.show');
        Route::post('/direct-gpay-unlock', [ClientDirectGpayUnlockController::class, 'redirectLegacy'])->name('direct-gpay-unlock.store');

        // Events Management — Direct GPay unlock requires Guest Contribution (level 2)
        Route::middleware('plan.feature:2')->group(function () {
            Route::get('/events/{event}/direct-gpay-unlock', [ClientDirectGpayUnlockController::class, 'show'])->name('events.direct-gpay-unlock.show');
            Route::post('/events/{event}/direct-gpay-unlock', [ClientDirectGpayUnlockController::class, 'store'])->name('events.direct-gpay-unlock.store');
            Route::post('/events/{event}/direct-gpay-unlock/razorpay-order', [ClientDirectGpayUnlockController::class, 'createRazorpayOrder'])->name('events.direct-gpay-unlock.razorpay.order');
            Route::post('/events/{event}/direct-gpay-unlock/razorpay-verify', [ClientDirectGpayUnlockController::class, 'verifyRazorpay'])->name('events.direct-gpay-unlock.razorpay.verify');
            Route::post('/events/{event}/direct-gpay-unlock/redeem-guest-pay-pack', [ClientDirectGpayUnlockController::class, 'redeemGuestPayPack'])->name('events.direct-gpay-unlock.redeem-guest-pay-pack');
        });
        Route::resource('events', ClientEventController::class);
        Route::post('/events/{id}/direct-gpay/upi', [ClientEventController::class, 'saveDirectGpayUpi'])->name('events.direct-gpay.upi');
        Route::get('/events/{id}/direct-gpay/qr.png', [ClientEventController::class, 'directGpayQrPng'])->name('events.direct-gpay.qr');
        Route::post('/events/{id}/plan', [ClientEventController::class, 'updatePlan'])->name('events.plan.update');
        Route::get('/events/{id}/plan/payment', [ClientPlanPaymentController::class, 'show'])->name('events.plan.payment');
        Route::post('/events/{id}/plan/payment', [ClientPlanPaymentController::class, 'store'])->name('events.plan.payment.store');
        Route::post('/events/{id}/plan/razorpay-order', [ClientPlanPaymentController::class, 'createRazorpayOrder'])->name('events.plan.razorpay.order');
        Route::post('/events/{id}/plan/razorpay-verify', [ClientPlanPaymentController::class, 'verifyRazorpay'])->name('events.plan.razorpay.verify');

        // QR Code
        Route::get('/qrcode/{eventId}', [ClientQRCodeController::class, 'show'])->name('qrcode.show');
        Route::get('/qrcode/{eventId}/generate', [ClientQRCodeController::class, 'generate'])->name('qrcode.generate');
        Route::get('/qrcode/{eventId}/download', [ClientQRCodeController::class, 'download'])->name('qrcode.download');

        // GPay Upload
        Route::get('/gpay/upload', [ClientGPayController::class, 'showUploadForm'])->name('gpay.upload');
        Route::post('/gpay/upload', [ClientGPayController::class, 'upload'])->name('gpay.store');
        Route::post('/gpay/details', [ClientGPayController::class, 'saveDetails'])->name('gpay.details');
        Route::post('/gpay/quick-upload', [ClientGPayController::class, 'quickUpload'])->name('gpay.quick-upload');
        Route::get('/gpay/view-image/{id}', [ClientGPayController::class, 'viewImage'])->name('gpay.view-image');
        Route::get('/gpay/upi-qr/{eventId}', [ClientGPayController::class, 'upiQr'])->name('gpay.upi-qr');

        // Chandlas Management
        Route::get('/chandlas/lookup-giver', [ClientChandlaController::class, 'lookupGiver'])->name('chandlas.lookup-giver');
        Route::get('/chandlas/search-givers', [ClientChandlaController::class, 'searchGivers'])->name('chandlas.search-givers');
        Route::get('/chandlas/free-limit/download', [ClientChandlaController::class, 'freeLimitDownload'])->name('chandlas.free-limit.download');
        Route::post('/chandlas/{id}/clone', [ClientChandlaController::class, 'clone'])->name('chandlas.clone');
        Route::get('/chandlas/pdf', [ClientChandlaController::class, 'ledgerPdf'])->name('chandlas.pdf');
        Route::resource('chandlas', ClientChandlaController::class);
        Route::get('/events/{eventId}/chandlas/pdf', [ClientChandlaController::class, 'pdf'])->name('events.chandlas.pdf');

        // Cash Inventory
        Route::get('/events/{eventId}/cash-inventory', [ClientCashInventoryController::class, 'show'])->name('cash-inventory.show');
        Route::post('/events/{eventId}/cash-inventory', [ClientCashInventoryController::class, 'update'])->name('cash-inventory.update');
        
        // Contacts Management
        Route::get('/contacts/import', [ClientContactController::class, 'importForm'])->name('contacts.import');
        Route::post('/contacts/import', [ClientContactController::class, 'import'])->name('contacts.import.store');
        Route::resource('contacts', ClientContactController::class);
        Route::post('/contacts/{id}/toggle-favorite', [ClientContactController::class, 'toggleFavorite'])->name('contacts.toggle-favorite');

        // Marriage invitation cards (₹300 Razorpay — instant webhook unlock)
        Route::get('/marriage-invitations', [ClientMarriageInvitationController::class, 'index'])->name('marriage-invitations.index');
        Route::get('/marriage-invitations/create', [ClientMarriageInvitationController::class, 'create'])->name('marriage-invitations.create');
        Route::post('/marriage-invitations', [ClientMarriageInvitationController::class, 'store'])->name('marriage-invitations.store');
        Route::get('/marriage-invitations/create/{template}', [ClientMarriageInvitationController::class, 'createWithTemplateHint'])->name('marriage-invitations.create.hint')->where('template', '[a-z_]+');
        Route::post('/marriage-invitations/template/{template}', [ClientMarriageInvitationController::class, 'storeWithTemplate'])->name('marriage-invitations.store.legacy')->where('template', '[a-z_]+');
        Route::get('/marriage-invitations/{id}/edit', [ClientMarriageInvitationController::class, 'edit'])->name('marriage-invitations.edit')->whereNumber('id');
        Route::put('/marriage-invitations/{id}', [ClientMarriageInvitationController::class, 'update'])->name('marriage-invitations.update')->whereNumber('id');
        Route::get('/marriage-invitations/template-demo/{layout}', [ClientMarriageInvitationController::class, 'templateDemoPreview'])
            ->name('marriage-invitations.template-demo')
            ->where('layout', '[a-z_]+');
        Route::post('/marriage-invitations/{id}/payment/razorpay-order', [ClientMarriageInvitationController::class, 'createRazorpayOrder'])->name('marriage-invitations.payment.razorpay.order')->whereNumber('id');
        Route::post('/marriage-invitations/{id}/payment/razorpay-verify', [ClientMarriageInvitationController::class, 'verifyRazorpay'])->name('marriage-invitations.payment.razorpay.verify')->whereNumber('id');
        Route::get('/marriage-invitations/{id}/payment', [ClientMarriageInvitationController::class, 'payment'])->name('marriage-invitations.payment')->whereNumber('id');
        Route::post('/marriage-invitations/{id}/payment', [ClientMarriageInvitationController::class, 'paymentStore'])->name('marriage-invitations.payment.store')->whereNumber('id');
        Route::get('/marriage-invitations/{id}/download', [ClientMarriageInvitationController::class, 'download'])->name('marriage-invitations.download')->whereNumber('id');
        Route::get('/marriage-invitations/{id}/export/png', [ClientMarriageInvitationController::class, 'exportPng'])->name('marriage-invitations.export.png')->whereNumber('id');
        Route::get('/marriage-invitations/{id}/export/video', [ClientMarriageInvitationController::class, 'exportVideo'])->name('marriage-invitations.export.video')->whereNumber('id');
        Route::get('/marriage-invitations/{id}', [ClientMarriageInvitationController::class, 'show'])->name('marriage-invitations.show')->whereNumber('id');

        Route::get('/pre-wedding', [ClientPreWeddingController::class, 'index'])->name('pre-wedding.index');
        Route::post('/pre-wedding/settings', [ClientPreWeddingController::class, 'updateSettings'])->name('pre-wedding.settings');
        Route::post('/pre-wedding/upload', [ClientPreWeddingController::class, 'upload'])->name('pre-wedding.upload');
        Route::get('/pre-wedding/thumbnail-preview/{milestoneKey}', [ClientPreWeddingController::class, 'thumbnailPreview'])
            ->where('milestoneKey', '[A-Za-z0-9_-]+')
            ->name('pre-wedding.thumbnail-preview');
        Route::get('/pre-wedding/card/{milestoneKey}', [ClientPreWeddingController::class, 'card'])
            ->where('milestoneKey', '[A-Za-z0-9_-]+')
            ->name('pre-wedding.card');
        Route::get('/pre-wedding/card', [ClientPreWeddingController::class, 'card'])->name('pre-wedding.card.query');
        Route::get('/pre-wedding/export/png/{milestoneKey}', [ClientPreWeddingController::class, 'exportPng'])
            ->where('milestoneKey', '[A-Za-z0-9_-]+')
            ->name('pre-wedding.export.png');
        Route::get('/pre-wedding/export/png', [ClientPreWeddingController::class, 'exportPng'])->name('pre-wedding.export.png.query');

        // ── Old redirect routes (kept for backward compat; now redirect to checkout) ──
        Route::get('/packs/celebration/pay', [ClientPackPurchaseController::class, 'celebrationRedirect'])->name('packs.celebration.pay');
        Route::get('/packs/host-duo/pay', [ClientPackPurchaseController::class, 'hostDuoRedirect'])->name('packs.host-duo.pay');
        Route::get('/packs/family/pay', [ClientPackPurchaseController::class, 'familyRedirect'])->name('packs.family.pay');
        Route::get('/packs/bundle/pay', [ClientPackPurchaseController::class, 'bundleRedirect'])->name('packs.bundle.pay');
        Route::get('/packs/guest-pay-single/pay', [ClientPackPurchaseController::class, 'guestPaySingleRedirect'])->name('packs.guest-pay-single.pay');
        Route::get('/packs/professional/pay', [ClientPackPurchaseController::class, 'professionalRedirect'])->name('packs.professional.pay');
        Route::get('/packs/enterprise/pay', [ClientPackPurchaseController::class, 'enterpriseRedirect'])->name('packs.enterprise.pay');
        Route::get('/packs/thanks', [ClientPackPurchaseController::class, 'thanks'])->name('packs.thanks');

        // ── NEW: Razorpay Orders checkout flow ────────────────────────────────
        Route::get('/packs/{pack}/checkout', [ClientPackPurchaseController::class, 'showCheckout'])
            ->name('packs.checkout')
            ->where('pack', '[a-z_-]+');
        Route::post('/packs/{pack}/order', [ClientPackPurchaseController::class, 'createOrder'])
            ->name('packs.order')
            ->where('pack', '[a-z_-]+');
        Route::post('/packs/{pack}/verify', [ClientPackPurchaseController::class, 'verifyPayment'])
            ->name('packs.verify')
            ->where('pack', '[a-z_-]+');

        // ── Transaction detail ────────────────────────────────────────────────
        Route::get('/transactions/{txnNumber}', [\App\Http\Controllers\Client\TransactionHistoryController::class, 'show'])
            ->name('transactions.show')
            ->where('txnNumber', 'TXN-[0-9-]+');

        // ── Ganpati Special ───────────────────────────────────────────────────
        // Free & unlimited for ALL users — no plan restrictions
        Route::prefix('ganpati')->name('ganpati.')->group(function () {
            // Events
            Route::get('/', [ClientGanpatiController::class, 'index'])->name('index');
            Route::get('/create', [ClientGanpatiController::class, 'create'])->name('create');
            Route::post('/', [ClientGanpatiController::class, 'store'])->name('store');
            Route::get('/{id}', [ClientGanpatiController::class, 'show'])->name('show')->whereNumber('id');
            Route::get('/{id}/edit', [ClientGanpatiController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::put('/{id}', [ClientGanpatiController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}', [ClientGanpatiController::class, 'destroy'])->name('destroy')->whereNumber('id');

            // Scanner / UPI QR
            Route::get('/{id}/scanner', [ClientGanpatiController::class, 'scanner'])->name('scanner')->whereNumber('id');
            Route::post('/{id}/scanner', [ClientGanpatiController::class, 'scannerSave'])->name('scanner.save')->whereNumber('id');
            Route::get('/{id}/qr.svg', [ClientGanpatiController::class, 'qr'])->name('qr')->whereNumber('id');

            // PDF download
            Route::get('/{id}/pdf', [ClientGanpatiController::class, 'pdf'])->name('pdf')->whereNumber('id');

            // Chandla (chanda) entries
            Route::get('/{id}/chandlas/create', [ClientGanpatiController::class, 'chandlaCreate'])->name('chandla.create')->whereNumber('id');
            Route::post('/{id}/chandlas', [ClientGanpatiController::class, 'chandlaStore'])->name('chandla.store')->whereNumber('id');
            Route::get('/{id}/chandlas/{chandlaId}/edit', [ClientGanpatiController::class, 'chandlaEdit'])->name('chandla.edit')->whereNumber(['id', 'chandlaId']);
            Route::put('/{id}/chandlas/{chandlaId}', [ClientGanpatiController::class, 'chandlaUpdate'])->name('chandla.update')->whereNumber(['id', 'chandlaId']);
            Route::delete('/{id}/chandlas/{chandlaId}', [ClientGanpatiController::class, 'chandlaDestroy'])->name('chandla.destroy')->whereNumber(['id', 'chandlaId']);
        });

        // ── Expense Management ────────────────────────────────────────────────
        Route::prefix('expenses')->name('expenses.')->group(function () {
            Route::get('/',          [ClientExpenseController::class, 'index'])->name('index');
            Route::get('/pdf',       [ClientExpenseController::class, 'pdf'])->name('pdf');
            Route::get('/create',    [ClientExpenseController::class, 'create'])->name('create');
            Route::post('/',         [ClientExpenseController::class, 'store'])->name('store');
            Route::get('/{id}',      [ClientExpenseController::class, 'show'])->name('show')->whereNumber('id');
            Route::get('/{id}/edit', [ClientExpenseController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::put('/{id}',      [ClientExpenseController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}',   [ClientExpenseController::class, 'destroy'])->name('destroy')->whereNumber('id');
        });
    });
});

require __DIR__ . '/vendor_web.php';