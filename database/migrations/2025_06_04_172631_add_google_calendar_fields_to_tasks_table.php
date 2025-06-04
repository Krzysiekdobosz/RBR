<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->after('share_token');
            $table->boolean('sync_to_calendar')->default(false)->after('google_event_id');
            $table->timestamp('calendar_synced_at')->nullable()->after('sync_to_calendar');
            
            $table->index('google_event_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'sync_to_calendar', 'calendar_synced_at']);
        });
    }
};
