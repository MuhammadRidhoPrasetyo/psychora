<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_no')->default(1);
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'submitted', 'expired', 'evaluated'])->default('draft');
            $table->decimal('total_score', 10, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->json('result_payload')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'test_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_attempts');
    }
};
