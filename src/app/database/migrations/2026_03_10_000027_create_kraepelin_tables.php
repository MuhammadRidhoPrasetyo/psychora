<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraepelin_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('kraepelin_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('kraepelin_form_id')->constrained()->cascadeOnDelete();
            $table->integer('numbers_per_column');
            $table->integer('columns_count');
            $table->integer('duration_per_column');
            $table->integer('total_answered')->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_wrong')->default(0);
            $table->integer('total_skipped')->default(0);
            $table->decimal('speed_score', 10, 2)->nullable();
            $table->decimal('accuracy_score', 10, 2)->nullable();
            $table->decimal('stability_score', 10, 2)->nullable();
            $table->decimal('final_score', 10, 2)->nullable();
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'expired'])->default('not_started');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('kraepelin_attempt_columns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kraepelin_attempt_id')->constrained()->cascadeOnDelete();
            $table->integer('column_number');
            $table->timestamps();

            $table->index(['kraepelin_attempt_id', 'column_number']);
        });

        Schema::create('kraepelin_attempt_numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kraepelin_attempt_column_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->tinyInteger('value');
            $table->timestamps();

            $table->index(['kraepelin_attempt_column_id', 'position']);
        });

        Schema::create('kraepelin_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kraepelin_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('kraepelin_attempt_column_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->tinyInteger('top_number');
            $table->tinyInteger('bottom_number');
            $table->tinyInteger('user_answer')->nullable();
            $table->tinyInteger('correct_answer');
            $table->boolean('is_correct')->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->index(['kraepelin_attempt_id', 'position']);
            $table->index('is_correct');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraepelin_answers');
        Schema::dropIfExists('kraepelin_attempt_numbers');
        Schema::dropIfExists('kraepelin_attempt_columns');
        Schema::dropIfExists('kraepelin_attempts');
        Schema::dropIfExists('kraepelin_forms');
    }
};
