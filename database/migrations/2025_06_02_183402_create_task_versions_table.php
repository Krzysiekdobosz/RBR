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
    Schema::create('task_versions', function (Blueprint $table) {
        $table->id();

        // powiązanie z oryginalnym zadaniem
        $table->foreignId('task_id')
            ->constrained()
            ->cascadeOnDelete();

        // powiązanie z właścicielem (denormalizacja)
        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnDelete();

        // dane snapshotu
        $table->string('name');
        $table->text('description')->nullable();
        $table->enum('priority', ['low', 'medium', 'high']);
        $table->enum('status',   ['to-do', 'in_progress', 'done']);
        $table->date('due_date');
        $table->boolean('reminder_sent')->default(false);

        $table->timestamp('created_at');   // kiedy utworzono wersję

        // Przydatny indeks do szybkiego filtrowania po użytkowniku
        $table->index('user_id');
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_versions');
    }
};
