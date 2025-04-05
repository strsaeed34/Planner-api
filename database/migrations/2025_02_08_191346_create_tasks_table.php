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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('goal_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_recursive')->default(false);
            $table->integer('estimated_time')->nullable(); // ⏳ Estimated duration in minutes
            $table->enum('status', ['pending', 'in_progress', 'completed', 'canceled'])->default('pending'); // 🏁 Task status
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); // ⚡ Priority level
            $table->date('date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
