<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychotest_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychotest_form_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'expired'])->default('not_started');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->decimal('score', 10, 2)->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('psychotest_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychotest_question_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychotest_option_id')->constrained('psychotest_question_options')->cascadeOnDelete();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->index(['psychotest_attempt_id', 'psychotest_question_id']);
        });

        Schema::create('psychotest_result_characteristics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychotest_characteristic_id')->constrained()->cascadeOnDelete();
            $table->integer('raw_score');
            $table->tinyInteger('scaled_score');
            $table->timestamps();

            $table->index(['psychotest_attempt_id', 'psychotest_characteristic_id']);
        });

        Schema::create('psychotest_result_aspects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychotest_aspect_id')->constrained()->cascadeOnDelete();
            $table->float('raw_score');
            $table->float('scaled_score');
            $table->timestamps();

            $table->index(['psychotest_attempt_id', 'psychotest_aspect_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychotest_result_aspects');
        Schema::dropIfExists('psychotest_result_characteristics');
        Schema::dropIfExists('psychotest_answers');
        Schema::dropIfExists('psychotest_attempts');
    }
};
