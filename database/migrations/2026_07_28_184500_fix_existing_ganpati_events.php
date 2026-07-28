<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $ganpatiTypeId = DB::table('event_types')
            ->where('slug', 'ganpati_special')
            ->value('id');

        if ($ganpatiTypeId) {
            DB::table('events')
                ->whereNull('event_type_id')
                ->where('title', 'like', '%ganpati%')
                ->update([
                    'event_type_id' => $ganpatiTypeId,
                    'pricing_plan' => 'unlimited' // Ensure pricing plan is set to unlimited for Ganpati events
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse migration needed for data fix
    }
};
