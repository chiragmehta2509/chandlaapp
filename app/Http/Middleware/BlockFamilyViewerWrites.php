<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Family-member route enforcement.
 *
 * - Viewers (read-only): blocked from any non-GET except logout/own-password.
 * - Editors (full access): can add/edit, but blocked from:
 *     - DELETE methods (cannot delete events / chandla / contacts / etc.)
 *     - Family-member management (cannot add/remove other family members)
 *     - Pack & plan purchases (cannot spend money on parent's account)
 */
class BlockFamilyViewerWrites
{
    /** Routes neither role may invoke (always blocked for any family member on parent's billing). */
    private const FAMILY_BLOCKED_ROUTES = [
        // Plan / pack purchases — billing is parent-only
        'client.packs.celebration.pay',
        'client.packs.celebration.payment-link',
        'client.packs.razorpay.order',
        'client.packs.razorpay.verify',
        'client.events.plan.update',
        'client.events.plan.payment.store',
        'client.events.plan.razorpay.order',
        'client.events.plan.razorpay.verify',
        'client.events.direct-gpay-unlock.store',
        'client.events.direct-gpay-unlock.razorpay.order',
        'client.events.direct-gpay-unlock.razorpay.verify',
        'client.events.direct-gpay-unlock.redeem-guest-pay-pack',
        'client.marriage-invitations.payment',
        'client.marriage-invitations.payment.submit',
        'client.marriage-invitations.payment.razorpay',
        'client.marriage-invitations.payment.razorpay.order',
        'client.marriage-invitations.payment.razorpay.verify',
    ];

    /** Viewer-only allowlist for non-GET requests. */
    private const VIEWER_WRITE_ALLOWLIST = [
        'client.logout',
        'client.password.update',
    ];

    public function handle(Request $request, Closure $next)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (!$user || !$user->isFamilyMember()) {
            return $next($request);
        }

        // If the targeted event or resource belongs to the family member themselves,
        // they have full owner permissions (read, write, delete).
        $ownerId = $this->getTargetResourceOwnerId($request);
        if ($ownerId !== null && $ownerId === (int) $user->id) {
            return $next($request);
        }

        $method = $request->method();
        $routeName = $request->route()?->getName();

        if (in_array($routeName, self::FAMILY_BLOCKED_ROUTES, true)) {
            return $this->forbid($request, 'Only the main account can do this.');
        }

        if (in_array($method, ['GET', 'HEAD'], true)) {
            return $next($request);
        }

        if ($user->isFamilyEditor()) {
            // Editors cannot DELETE — prevents removal of parent's records.
            if ($method === 'DELETE') {
                return $this->forbid($request, 'Family editors cannot delete records. Ask the main account holder.');
            }
            return $next($request);
        }

        // Viewer: block all writes except whitelisted (logout, change own password).
        if (in_array($routeName, self::VIEWER_WRITE_ALLOWLIST, true)) {
            return $next($request);
        }

        return $this->forbid($request, 'Family viewer accounts are read-only.');
    }

    private function getTargetResourceOwnerId(Request $request): ?int
    {
        $routeName = $request->route()?->getName();
        if (!$routeName) {
            return null;
        }

        // 1. Check for Event-related routes
        $event = $request->route('event') ?: $request->route('eventId');
        if (!$event && str_contains($routeName, 'events.')) {
            $event = $request->route('id');
        }
        if ($event) {
            if ($event instanceof \App\Models\Event) {
                return (int) $event->user_id;
            }
            if (is_numeric($event)) {
                $evt = \App\Models\Event::find((int) $event);
                if ($evt) {
                    return (int) $evt->user_id;
                }
            }
        }

        // 2. Check for Chandla-related routes
        $chandla = $request->route('chandla');
        if (!$chandla && str_contains($routeName, 'chandlas.')) {
            $chandla = $request->route('id');
        }
        if ($chandla) {
            if ($chandla instanceof \App\Models\Chandla) {
                return (int) $chandla->user_id;
            }
            if (is_numeric($chandla)) {
                $ch = \App\Models\Chandla::find((int) $chandla);
                if ($ch) {
                    return (int) $ch->user_id;
                }
            }
        }

        // 3. Check for Contact-related routes
        $contact = $request->route('contact');
        if (!$contact && str_contains($routeName, 'contacts.')) {
            $contact = $request->route('id');
        }
        if ($contact) {
            if ($contact instanceof \App\Models\Contact) {
                return (int) $contact->user_id;
            }
            if (is_numeric($contact)) {
                $cnt = \App\Models\Contact::find((int) $contact);
                if ($cnt) {
                    return (int) $cnt->user_id;
                }
            }
        }

        // 4. Check if request has event_id (e.g. storing new Chandla)
        if ($request->has('event_id')) {
            $evt = \App\Models\Event::find((int) $request->input('event_id'));
            if ($evt) {
                return (int) $evt->user_id;
            }
        }

        return null;
    }

    private function forbid(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => $message], 403);
        }
        abort(403, $message);
    }
}
