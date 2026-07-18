<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'avatar',
        'auth_provider',
        'provider_id',
        'fcm_token',
        'subscription_status',
        'subscription_expires_at',
        'is_active',
        'is_admin',
        'is_deleted',
        'deleted_at',
        'language',
        'referral_code',
        'referred_by',
        'free_event_credits',
        'referral_rewarded_at',
        'celebration_pack_paid_at',
        'premium_bundle_paid_at',
        'ledger_duo_pack_paid_at',
        'guest_pay_single_event_credits',
        'parent_user_id',
        'must_change_password',
        'family_role',
        'family_pack_paid_at',
        'professional_pack_paid_at',
        'enterprise_pack_paid_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'deleted_at' => 'datetime',
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
        'is_deleted' => 'boolean',
        'free_event_credits' => 'integer',
        'referral_rewarded_at' => 'datetime',
        'celebration_pack_paid_at' => 'datetime',
        'premium_bundle_paid_at' => 'datetime',
        'ledger_duo_pack_paid_at' => 'datetime',
        'guest_pay_single_event_credits' => 'integer',
        'must_change_password' => 'boolean',
        'family_pack_paid_at' => 'datetime',
        'professional_pack_paid_at' => 'datetime',
        'enterprise_pack_paid_at' => 'datetime',
    ];

    public const FAMILY_ROLE_VIEWER = 'viewer';
    public const FAMILY_ROLE_EDITOR = 'editor';

    // Relationships
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function upiTransactions()
    {
        return $this->hasMany(UPITransaction::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function collaboratedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_collaborators')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function chandlas()
    {
        return $this->hasMany(Chandla::class);
    }

    public function marriageInvitations()
    {
        return $this->hasMany(MarriageInvitation::class);
    }

    public function preWeddingAssets()
    {
        return $this->hasMany(PreWeddingAsset::class);
    }

    public function preWeddingSetting()
    {
        return $this->hasOne(PreWeddingSetting::class);
    }

    public function matrimonialProfile()
    {
        return $this->hasOne(MatrimonialProfile::class);
    }

    public function matrimonialPlans()
    {
        return $this->hasMany(MatrimonialPlan::class);
    }

    public function matrimonialInterestsSent()
    {
        return $this->hasMany(MatrimonialInterest::class, 'from_user_id');
    }

    public function matrimonialInterestsReceived()
    {
        return $this->hasMany(MatrimonialInterest::class, 'to_user_id');
    }

    public function matrimonialInterestBlocks()
    {
        return $this->hasMany(MatrimonialInterestBlock::class, 'user_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_deleted', false);
    }

    public function scopePremium($query)
    {
        return $query->where('subscription_status', 'premium')
            ->where('subscription_expires_at', '>', now());
    }

    /**
     * Determines the user's highest active subscription tier.
     * 0 = Free
     * 1 = Celebration (₹300)
     * 2 = Guest Contribution (₹400)
     * 3 = Host Plus / Ledger Duo (₹500)
     * 4 = Family Plan (₹600)
     * 5 = Premium Host (₹700)
     * 6 = Professional (₹999)
     * 7 = Enterprise
     */
    public function planLevel(): int
    {
        if ($this->enterprise_pack_paid_at !== null) return 7;
        if ($this->professional_pack_paid_at !== null) return 6;
        if ($this->premium_bundle_paid_at !== null) return 5;
        if ($this->family_pack_paid_at !== null) return 4;
        if ($this->ledger_duo_pack_paid_at !== null) return 3;

        // Guest Contribution credit check
        $hasGuestPay = $this->guest_pay_single_event_credits > 0 ||
            \App\Models\UPITransaction::where('user_id', $this->id)
                ->where('status', 'completed')
                ->where('metadata->source', 'guest_pay_single_pack')
                ->exists();

        if ($hasGuestPay) return 2;
        if ($this->celebration_pack_paid_at !== null) return 1;

        return 0;
    }

    public function maxEventsAllowed(): int
    {
        $level = $this->planLevel();
        if ($level >= 7) return 999; // Unlimited for enterprise
        if ($level >= 6) return 10;
        if ($level >= 5) return 3;
        if ($level >= 3) return 2;
        return 1; // Levels 0, 1, 2
    }

    public function maxFamilyEditorsAllowed(): int
    {
        $level = $this->planLevel();
        if ($level >= 6) return 999; // Unlimited for professional/enterprise
        if ($level >= 4) return 3;
        return 0; // Levels 0, 1, 2, 3 cannot add family editors
    }

    /** Level 1+: Marriage invitation + pre-wedding + video. */
    public function hasCelebrationPackAccess(): bool
    {
        return $this->planLevel() >= 1;
    }

    /** Level 2+: Direct GPay QR Unlocked for all allowed events. */
    public function hasDirectGpayQrUnlocked(): bool
    {
        return $this->planLevel() >= 2;
    }

    /** Level 2+: Unlimited Chandla entries for all allowed events. */
    public function hasLedgerUnlimitedChandla(): bool
    {
        return $this->planLevel() >= 2;
    }

    /** Level 5+: Premium Host. */
    public function hasPremiumChandlaBundle(): bool
    {
        return $this->planLevel() >= 5;
    }

    /** Level 6+: Professional analytics dashboard. */
    public function hasAdvancedAnalytics(): bool
    {
        return $this->planLevel() >= 6;
    }

    // ===== Family viewer (read-only sub-account) =====

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }

    public function familyMembers()
    {
        return $this->hasMany(User::class, 'parent_user_id');
    }

    public function isFamilyMember(): bool
    {
        return $this->parent_user_id !== null;
    }

    public function isFamilyViewer(): bool
    {
        return $this->isFamilyMember() && ($this->family_role ?? self::FAMILY_ROLE_VIEWER) === self::FAMILY_ROLE_VIEWER;
    }

    public function isFamilyEditor(): bool
    {
        return $this->isFamilyMember() && $this->family_role === self::FAMILY_ROLE_EDITOR;
    }

    /** Returns 'viewer' | 'editor' | null. */
    public function familyRole(): ?string
    {
        return $this->isFamilyMember() ? ($this->family_role ?? self::FAMILY_ROLE_VIEWER) : null;
    }

    /**
     * Highest family-member role this account is allowed to add (null = none).
     * - Free / Duo (₹500) / Celebration (₹300) → viewer
     * - Family Plan (₹600) / Complete (₹700) → editor
     */
    public function maxFamilyRole(): string
    {
        if ($this->planLevel() >= 4) {
            return self::FAMILY_ROLE_EDITOR;
        }
        return self::FAMILY_ROLE_VIEWER;
    }

    public function canAddFamilyEditors(): bool
    {
        return $this->maxFamilyRole() === self::FAMILY_ROLE_EDITOR;
    }

    /**
     * Returns the user_id whose data this account should view.
     * For a main user, that's their own id. For a family member, it's the parent's id.
     */
    public function dataOwnerId(): int
    {
        return $this->parent_user_id ?: $this->id;
    }

    public function getDataOwnerIdAttribute(): int
    {
        return $this->dataOwnerId();
    }

    public function allowedUserIds(): array
    {
        return $this->parent_user_id
            ? [$this->id, $this->parent_user_id]
            : [$this->id];
    }
}

