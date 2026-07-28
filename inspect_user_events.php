<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Searching for user containing 'u' in name/email:\n";
$users = \App\Models\User::where('email', 'like', '%u%')
    ->orWhere('name', 'like', '%u%')
    ->get();
foreach ($users as $user) {
    echo "User ID: {$user->id} | Name: {$user->name} | Email: {$user->email}\n";
    $events = \App\Models\Event::where('user_id', $user->id)->get();
    echo "  Total Events: " . $events->count() . "\n";
    foreach ($events as $event) {
         echo "    Event ID: {$event->id} | Title: {$event->title} | Type ID: " . ($event->event_type_id ?? 'NULL') . " | Type: " . ($event->event_type ?? 'NULL') . " | Created: {$event->created_at}\n";
    }
}

echo "\nLatest Ganpati Special events in database (event_type_id = 7 or title like 'ganpati' or event_type = 'ganpati_special'):\n";
$ganpatiTypeId = \App\Models\EventType::where('slug', 'ganpati_special')->value('id');
$events = \App\Models\Event::where('event_type_id', $ganpatiTypeId)
    ->orWhere('event_type', 'ganpati_special')
    ->orWhere('title', 'like', '%ganpati%')
    ->orderBy('id', 'desc')
    ->take(5)
    ->get();
foreach ($events as $event) {
    echo "Event ID: {$event->id} | Title: {$event->title} | User ID: {$event->user_id} | Type ID: " . ($event->event_type_id ?? 'NULL') . " | Type: " . ($event->event_type ?? 'NULL') . " | Created: {$event->created_at}\n";
}
