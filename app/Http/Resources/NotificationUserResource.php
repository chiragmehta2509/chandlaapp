<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, // this is the notification_users.id which is used for operations like /notifications/{id}/read
            'notification_id' => $this->notification_id,
            'title' => $this->notification->title ?? '',
            'message' => $this->notification->message ?? '',
            'image' => $this->notification->image ?? null,
            'action_type' => $this->notification->action_type ?? 'none',
            'action_value' => $this->notification->action_value ?? null,
            'is_read' => (bool)$this->is_read,
            'read_at' => $this->read_at ? $this->read_at->toDateTimeString() : null,
            'created_at' => $this->created_at ? $this->created_at->toDateTimeString() : null,
        ];
    }
}
