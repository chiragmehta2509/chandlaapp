<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PushNotification\DeviceTokenController;
use App\Http\Controllers\Api\PushNotification\PushNotificationController;
use App\Http\Controllers\Api\PushNotification\AdminNotificationController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Notification\NotificationController;
use App\Http\Controllers\Api\Event\EventController;
use App\Http\Controllers\Api\Contact\ContactController;
use App\Http\Controllers\Api\Entry\EntryController;
use App\Http\Controllers\Api\Invitation\InvitationController;
use App\Http\Controllers\Api\UPI\UPIController;
use App\Http\Controllers\Api\Report\ReportController;
use App\Http\Controllers\Api\Chandla\ChandlaController;
use App\Http\Controllers\Api\Pack\PackController;
use App\Http\Controllers\Api\Transaction\TransactionController;
use App\Http\Controllers\Api\Invitation\MarriageInvitationController as ApiMarriageInvitationController;
use App\Http\Controllers\Api\Ganpati\GanpatiController;
use App\Http\Controllers\Api\FamilyMember\FamilyMemberController;
use App\Http\Controllers\Api\Expense\ExpenseController;
use App\Http\Controllers\Api\Subscription\SubscriptionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| Base URL: https://skylighttech.in/chandlaApp/api/v1
*/

// Public routes
Route::prefix('v1')->group(function () {
    
    // Route List Endpoint
    Route::get('/routes', function () {
        $routeCollection = Route::getRoutes();
        $routes = [];
        foreach ($routeCollection as $value) {
            $uri = $value->uri();
            if (str_contains($uri, 'api/')) {
                $methods = array_filter($value->methods(), function ($m) {
                    return in_array($m, ['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
                });
                
                if (empty($methods)) {
                    continue;
                }
                
                $routes[] = [
                    'method' => implode('|', $methods),
                    'uri'    => $uri,
                    'name'   => $value->getName(),
                    'action' => $value->getActionName(),
                ];
            }
        }
        
        usort($routes, function ($a, $b) {
            return strcmp($a['uri'], $b['uri']);
        });

        return response()->json([
            'success' => true,
            'count'   => count($routes),
            'routes'  => $routes
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    });
    
    // -------------------------------------------------------------------------
    // App Version Check (static — update values here when releasing a new build)
    // GET /api/v1/app-version
    // -------------------------------------------------------------------------
    Route::get('/app-version', function () {
        return response()->json([
            'success' => true,
            'data'    => [
                'android' => [
                    'min_version'    => '1.0.0',
                    'min_build'      => 18,
                    'latest_version' => '1.0.0',
                    'latest_build'   => 7,
                    'store_url'      => env('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=com.skylighttech.chandla_book'),
                    'force_update'   => true,
                    'update_message' => 'A critical update is required. Please update the app to continue.',
                ],
                'ios'     => [
                    'min_version'    => '1.0.0',
                    'min_build'      => 18,
                    'latest_version' => '1.0.0',
                    'latest_build'   => 7,
                    'store_url'      => env('APP_STORE_URL', 'https://apps.apple.com/us/app/chandla-book/id6796605523'),
                    'force_update'   => true,
                    'update_message' => 'A critical update is required. Please update the app to continue.',
                ],
            ],
        ]);
    });

    // Authentication Routes (Public)
    Route::prefix('auth')->group(function () {
        // Social Login
        Route::post('/google/login', [AuthController::class, 'googleLogin']);
        Route::post('/facebook/login', [AuthController::class, 'facebookLogin']);
        Route::post('/apple/login', [AuthController::class, 'appleLogin']);
        
        // Phone OTP
        Route::post('/phone/send-otp', [AuthController::class, 'sendOTP']);
        Route::post('/phone/verify-otp', [AuthController::class, 'verifyOTP']);
        
        // Email/Password
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/register/verify', [AuthController::class, 'verifyRegisterOtp']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);

        // WhatsApp Deep-Link Account Verification
        // Flutter intercepts the WhatsApp link and posts the token here instead of
        // opening the browser. Returns phone_verified status + auth token.
        Route::post('/verify-account', [AuthController::class, 'verifyAccount']);
    });
});

// Protected routes - Authentication required via Sanctum
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    
    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/update-profile', [AuthController::class, 'updateProfile']);
    });
    
    // User Routes
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UserController::class, 'getProfile']);
        Route::put('/profile', [UserController::class, 'updateProfile']);
        Route::post('/profile/send-verification', [UserController::class, 'sendProfileVerification']);
        Route::post('/avatar', [UserController::class, 'uploadAvatar']);
        Route::delete('/avatar', [UserController::class, 'deleteAvatar']);
        
        // Subscription
        Route::get('/subscription', [UserController::class, 'getSubscription']);
        Route::get('/subscription/plans', [SubscriptionController::class, 'plans']);
        Route::get('/subscription/history', [SubscriptionController::class, 'history']);
        Route::post('/subscription/purchase', [SubscriptionController::class, 'purchase']);
        Route::post('/subscription/verify', [SubscriptionController::class, 'verify']);
        Route::post('/subscriptions/activate', [SubscriptionController::class, 'verify']);
        Route::post('/subscription/upgrade', [UserController::class, 'upgradeSubscription']);
        Route::post('/subscription/cancel', [UserController::class, 'cancelSubscription']);
        
        // Account Management
        Route::post('/deactivate', [UserController::class, 'deactivateAccount']);
        Route::post('/delete', [UserController::class, 'deleteAccount']);
        Route::get('/stats', [UserController::class, 'getStats']);
        Route::get('/active-plan', [UserController::class, 'activePlan']);
        Route::post('/plan/update', [UserController::class, 'updatePlan']);
    });
    
    // Notification Routes
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::get('/{id}', [NotificationController::class, 'show']);
        Route::put('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::put('/read-all', [NotificationController::class, 'markAllAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/clear-all', [NotificationController::class, 'clearAll']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
        Route::delete('/', [NotificationController::class, 'clearAll']);
        
        // Device Token Management
        Route::post('/device/register', [NotificationController::class, 'registerDevice']);
        Route::delete('/device/{id}', [NotificationController::class, 'unregisterDevice']);
        Route::get('/device/list', [NotificationController::class, 'listDevices']);
        
        // Preferences
        Route::get('/preferences', [NotificationController::class, 'getPreferences']);
        Route::put('/preferences', [NotificationController::class, 'updatePreferences']);
    });
    
    // Event Routes
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::get('/upcoming', [EventController::class, 'upcoming']);
        Route::get('/past', [EventController::class, 'past']);
        Route::get('/archived', [EventController::class, 'archived']);
        Route::get('/{id}', [EventController::class, 'show']);
        Route::get('/{id}/pdf', [EventController::class, 'downloadPdf']);
        Route::post('/', [EventController::class, 'store']);
        Route::put('/{id}', [EventController::class, 'update']);
        Route::post('/{id}', [EventController::class, 'update']);
        Route::delete('/{id}', [EventController::class, 'destroy']);
        Route::post('/{id}/archive', [EventController::class, 'archive']);
        Route::post('/{id}/unarchive', [EventController::class, 'unarchive']);
        Route::post('/{id}/duplicate', [EventController::class, 'duplicate']);
        Route::get('/{id}/stats', [EventController::class, 'getStats']);
        
        // Collaborators
        Route::get('/{id}/collaborators', [EventController::class, 'getCollaborators']);
        Route::post('/{id}/collaborators', [EventController::class, 'addCollaborator']);
        Route::put('/{id}/collaborators/{userId}', [EventController::class, 'updateCollaborator']);
        Route::delete('/{id}/collaborators/{userId}', [EventController::class, 'removeCollaborator']);

        // Event Pricing Plan & GPay unlocks
        Route::post('/{id}/plan/razorpay-order', [EventController::class, 'createPlanRazorpayOrder']);
        Route::post('/{id}/plan/razorpay-verify', [EventController::class, 'verifyPlanRazorpay']);
        Route::post('/{id}/direct-gpay-unlock/razorpay-order', [EventController::class, 'createDirectGpayRazorpayOrder']);
        Route::post('/{id}/direct-gpay-unlock/razorpay-verify', [EventController::class, 'verifyDirectGpayRazorpay']);
        Route::post('/{id}/direct-gpay-unlock/redeem-guest-pay-pack', [EventController::class, 'redeemGuestPayPack']);
    });
    
    // Contact Routes
    Route::prefix('contacts')->group(function () {
        Route::get('/', [ContactController::class, 'index']);
        Route::get('/favorites', [ContactController::class, 'favorites']);
        Route::get('/search', [ContactController::class, 'search']);
        Route::get('/{id}', [ContactController::class, 'show']);
        Route::post('/', [ContactController::class, 'store']);
        Route::put('/{id}', [ContactController::class, 'update']);
        Route::delete('/{id}', [ContactController::class, 'destroy']);
        Route::post('/{id}/favorite', [ContactController::class, 'toggleFavorite']);
        
        // Import/Export
        Route::post('/bulk', [ContactController::class, 'bulkStore']);
        Route::post('/import', [ContactController::class, 'import']);
        Route::get('/export', [ContactController::class, 'export']);
        Route::get('/export/template', [ContactController::class, 'downloadTemplate']);
    });
    
    // Entry / RSVP Routes
    Route::prefix('entries')->group(function () {
        Route::get('/', [EntryController::class, 'index']);
        Route::get('/event/{eventId}', [EntryController::class, 'getByEvent']);
        Route::get('/{id}', [EntryController::class, 'show']);
        Route::post('/', [EntryController::class, 'store']);
        Route::put('/{id}', [EntryController::class, 'update']);
        Route::delete('/{id}', [EntryController::class, 'destroy']);
        Route::put('/{id}/status', [EntryController::class, 'updateStatus']);
        Route::post('/bulk', [EntryController::class, 'bulkCreate']);
        Route::put('/bulk/status', [EntryController::class, 'bulkUpdateStatus']);
    });

    // Chandlas / Ledger Routes
    Route::prefix('chandlas')->group(function () {
        Route::get('/', [ChandlaController::class, 'index']);
        Route::get('/stats', [ChandlaController::class, 'stats']);
        Route::get('/pdf', [ChandlaController::class, 'downloadPdf']);
        Route::get('/{id}', [ChandlaController::class, 'show']);
        Route::post('/', [ChandlaController::class, 'store']);
        Route::put('/{id}', [ChandlaController::class, 'update']);
        Route::delete('/{id}', [ChandlaController::class, 'destroy']);
    });

    // Ganpati Special Routes
    Route::prefix('ganpati')->group(function () {
        Route::get('/check-exists', [GanpatiController::class, 'checkExists']);
        Route::get('/', [GanpatiController::class, 'index']);
        Route::post('/', [GanpatiController::class, 'store']);
        Route::get('/{id}', [GanpatiController::class, 'show']);
        Route::put('/{id}', [GanpatiController::class, 'update']);
        Route::delete('/{id}', [GanpatiController::class, 'destroy']);
        Route::post('/{id}/scanner', [GanpatiController::class, 'updateScanner']);
        Route::get('/{id}/pdf', [GanpatiController::class, 'downloadPdf']);
        Route::get('/{id}/qr', [GanpatiController::class, 'qr']);
        Route::post('/{id}/chandlas', [GanpatiController::class, 'storeChandla']);
        Route::get('/{id}/chandlas', [GanpatiController::class, 'listChandlas']);
        Route::get('/{id}/chandlas/{chandlaId}', [GanpatiController::class, 'showChandla']);
        Route::put('/{id}/chandlas/{chandlaId}', [GanpatiController::class, 'updateChandla']);
        Route::delete('/{id}/chandlas/{chandlaId}', [GanpatiController::class, 'destroyChandla']);
    });
    
    // Invitation Routes
    Route::prefix('invitations')->group(function () {
        Route::get('/', [InvitationController::class, 'index']);
        Route::get('/event/{eventId}', [InvitationController::class, 'getByEvent']);
        Route::get('/{id}', [InvitationController::class, 'show']);
        Route::get('/code/{code}', [InvitationController::class, 'getByCode']);
        Route::post('/', [InvitationController::class, 'store']);
        Route::put('/{id}', [InvitationController::class, 'update']);
        Route::delete('/{id}', [InvitationController::class, 'destroy']);
        
        // Send Invitations
        Route::post('/{id}/send', [InvitationController::class, 'send']);
        Route::post('/{id}/send-bulk', [InvitationController::class, 'sendBulk']);
        
        // Share
        Route::post('/{id}/share', [InvitationController::class, 'share']);
        Route::get('/{id}/shares', [InvitationController::class, 'getShares']);
        
        // Generate
        Route::post('/{id}/generate-pdf', [InvitationController::class, 'generatePDF']);
        Route::post('/{id}/generate-image', [InvitationController::class, 'generateImage']);
        
        // Response
        Route::post('/{code}/respond', [InvitationController::class, 'respond']);
        Route::get('/{id}/analytics', [InvitationController::class, 'getAnalytics']);
    });

    // Marriage Invitation Routes
    Route::prefix('marriage-invitations')->group(function () {
        Route::get('/', [ApiMarriageInvitationController::class, 'index']);
        Route::get('/{id}', [ApiMarriageInvitationController::class, 'show']);
        Route::post('/', [ApiMarriageInvitationController::class, 'store']);
        Route::put('/{id}', [ApiMarriageInvitationController::class, 'update']);
        Route::post('/{id}/payment/razorpay-order', [ApiMarriageInvitationController::class, 'createRazorpayOrder']);
        Route::post('/{id}/payment/razorpay-verify', [ApiMarriageInvitationController::class, 'verifyRazorpay']);
    });

    // Pack Purchase / Subscription Routes
    Route::prefix('packs')->group(function () {
        Route::get('/', [PackController::class, 'index']);
        Route::post('/{slug}/order', [PackController::class, 'createOrder']);
        Route::post('/{slug}/verify', [PackController::class, 'verifyPayment']);
    });

    // ── Subscription Routes (consolidated) ───────────────────────────────────
    Route::prefix('subscription')->group(function () {
        Route::get('/',          [SubscriptionController::class, 'current']);   // current plan + features
        Route::get('/plans',     [SubscriptionController::class, 'plans']);     // all available plans
        Route::get('/history',   [SubscriptionController::class, 'history']);   // payment history
        Route::post('/purchase', [SubscriptionController::class, 'purchase']);  // create Razorpay order
        Route::post('/verify',   [SubscriptionController::class, 'verify']);    // verify & activate plan
        Route::post('/cancel',   [SubscriptionController::class, 'cancel']);    // cancel legacy subscription
    });

    Route::post('/payments/razorpay/verify', [SubscriptionController::class, 'verify']);

    // Transactions Route
    Route::prefix('transactions')->group(function () {
        Route::get('/', [TransactionController::class, 'index']);
        Route::get('/{txnNumber}', [TransactionController::class, 'show']);
    });

    // Family Members Routes
    Route::prefix('family-members')->group(function () {
        Route::get('/', [FamilyMemberController::class, 'index']);
        Route::post('/', [FamilyMemberController::class, 'store']);
        Route::get('/{id}', [FamilyMemberController::class, 'show']);
        Route::put('/{id}', [FamilyMemberController::class, 'update']);
        Route::delete('/{id}', [FamilyMemberController::class, 'destroy']);
        Route::post('/{id}/toggle-active', [FamilyMemberController::class, 'toggleActive']);
    });
    
    // UPI Payment Routes
    Route::prefix('payments')->group(function () {
        Route::get('/', [UPIController::class, 'index']);
        Route::get('/{id}', [UPIController::class, 'show']);
        Route::post('/create-order', [UPIController::class, 'createOrder']);
        Route::post('/verify', [UPIController::class, 'verifyPayment']);
        Route::post('/refund', [UPIController::class, 'refund']);
        Route::get('/history', [UPIController::class, 'getHistory']);
        Route::get('/stats', [UPIController::class, 'getStats']);
    });
    
    // Report Routes
    Route::prefix('reports')->group(function () {
        Route::get('/events', [ReportController::class, 'eventsReport']);
        Route::get('/entries', [ReportController::class, 'entriesReport']);
        Route::get('/invitations', [ReportController::class, 'invitationsReport']);
        Route::get('/payments', [ReportController::class, 'paymentsReport']);
        Route::get('/contacts', [ReportController::class, 'contactsReport']);
        Route::get('/dashboard', [ReportController::class, 'dashboard']);
        Route::get('/export/{type}', [ReportController::class, 'exportReport']);
    });
    
    // Sync Routes (for offline support)
    Route::prefix('sync')->group(function () {
        Route::post('/events', [EventController::class, 'sync']);
        Route::post('/contacts', [ContactController::class, 'sync']);
        Route::post('/entries', [EntryController::class, 'sync']);
        Route::post('/expenses', [ExpenseController::class, 'sync']);
        Route::get('/status', function () {
            return response()->json(['status' => 'sync_enabled']);
        });
    });

    // Expense Management Routes
    Route::prefix('expenses')->group(function () {
        Route::get('/categories',   [ExpenseController::class, 'categories']);     // list all categories
        Route::get('/stats',        [ExpenseController::class, 'stats']);          // overall / event stats
        Route::get('/cash-ledger',  [ExpenseController::class, 'cashLedger']);    // cash in vs cash out ledger
        Route::get('/pdf',          [ExpenseController::class, 'pdf']);            // generate/download PDF via api
        Route::get('/event/{eventId}', [ExpenseController::class, 'byEvent']);    // all expenses for one event
        Route::get('/',             [ExpenseController::class, 'index']);          // list all (with filters)
        Route::get('/{id}',         [ExpenseController::class, 'show']);           // single expense
        Route::post('/',            [ExpenseController::class, 'store']);          // create expense
        Route::put('/{id}',         [ExpenseController::class, 'update']);         // update expense
        Route::post('/{id}',        [ExpenseController::class, 'update']);         // update (multipart fallback)
        Route::delete('/{id}',      [ExpenseController::class, 'destroy']);        // delete expense
    });
});

// Push Notification Module Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/device-token', [DeviceTokenController::class, 'register']);
    
    Route::get('/notifications', [PushNotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [PushNotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [PushNotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [PushNotificationController::class, 'markAllRead']);
    Route::delete('/notifications/{id}', [PushNotificationController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/notifications', [App\Http\Controllers\Api\PushNotification\AdminNotificationController::class, 'index']);
    Route::post('/admin/notifications/send', [App\Http\Controllers\Api\PushNotification\AdminNotificationController::class, 'send']);
});

require __DIR__ . '/vendor_api.php';

