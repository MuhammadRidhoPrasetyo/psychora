<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ist_subtest_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ist_subtest_id')->constrained()->cascadeOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->decimal('raw_score', 10, 2)->nullable();
            $table->decimal('scaled_score', 10, 2)->nullable();
            $table->timestamps();

            $table->unique(['ist_attempt_id', 'ist_subtest_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ist_subtest_attempts');
    }
};
