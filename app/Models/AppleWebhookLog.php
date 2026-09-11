<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppleWebhookLog extends Model
{
    // ── Status constants ─────────────────────────────────────────────────────
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSED  = 'processed';
    public const STATUS_IGNORED    = 'ignored';
    public const STATUS_FAILED     = 'failed';

    protected $table = 'apple_webhook_logs';

    protected $fillable = [
        'notification_uuid',
        'notification_type',
        'subtype',
        'payload',
        'status',
        'error_message',
        'processed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed_at' => 'datetime',
    ];

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function markProcessed(): void
    {
        $this->update([
            'status'       => self::STATUS_PROCESSED,
            'processed_at' => now(),
        ]);
    }

    public function markIgnored(?string $reason = null): void
    {
        $this->update([
            'status'        => self::STATUS_IGNORED,
            'error_message' => $reason,
            'processed_at'  => now(),
        ]);
    }

    public function markFailed(string $error): void
    {
        $this->update([
            'status'        => self::STATUS_FAILED,
            'error_message' => $error,
        ]);
    }
}
