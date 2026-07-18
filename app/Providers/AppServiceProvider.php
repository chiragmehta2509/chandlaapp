<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        require_once app_path('helpers.php');

        // @canEdit — main user OR family editor (i.e., not a read-only viewer).
        // Use to hide ADD/EDIT buttons. Editors can perform these actions.
        Blade::if('canEdit', function () {
            $u = Auth::user();
            return $u instanceof User && !$u->isFamilyViewer();
        });

        // @canDelete — main user only. Editors cannot DELETE; viewers can't either.
        // Use to hide DELETE buttons (trash icons, destroy forms, etc.).
        Blade::if('canDelete', function () {
            $u = Auth::user();
            return $u instanceof User && !$u->isFamilyMember();
        });

        // @isMainUser — main user only. Use for plan purchases, family-member management,
        // anything that affects the parent account's billing or sub-accounts.
        Blade::if('isMainUser', function () {
            $u = Auth::user();
            return $u instanceof User && !$u->isFamilyMember();
        });

        // @familyMember — render block for any family sub-account (viewer or editor).
        Blade::if('familyMember', function () {
            $u = Auth::user();
            return $u instanceof User && $u->isFamilyMember();
        });

        // @familyViewer — render block only for read-only family viewer accounts.
        Blade::if('familyViewer', function () {
            $u = Auth::user();
            return $u instanceof User && $u->isFamilyViewer();
        });

        // @familyEditor — render block only for full-access family editor accounts.
        Blade::if('familyEditor', function () {
            $u = Auth::user();
            return $u instanceof User && $u->isFamilyEditor();
        });
    }
}

