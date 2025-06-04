<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['to-do', 'in_progress', 'done'])->default('to-do');
            $table->date('due_date');
            $table->boolean('reminder_sent')->default(false);
            $table->string('share_token')->nullable();
            $table->string('google_event_id')->nullable();
            $table->boolean('sync_to_calendar')->default(false);
            $table->timestamp('calendar_synced_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'priority']);
            $table->index(['due_date', 'reminder_sent']);
            $table->index('google_event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};