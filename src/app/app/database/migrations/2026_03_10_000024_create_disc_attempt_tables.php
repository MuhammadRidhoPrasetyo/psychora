<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disc_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('disc_form_id')->constrained()->cascadeOnDelete();
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'expired'])->default('not_started');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->decimal('score', 10, 2)->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('disc_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('disc_question_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('most_option_id')->constrained('disc_options')->cascadeOnDelete();
            $table->foreignUuid('least_option_id')->constrained('disc_options')->cascadeOnDelete();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->index(['disc_attempt_id', 'disc_question_id']);
        });

        Schema::create('disc_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_attempt_id')->constrained()->cascadeOnDelete();
            $table->integer('most_d')->default(0);
            $table->integer('most_i')->default(0);
            $table->integer('most_s')->default(0);
            $table->integer('most_c')->default(0);
            $table->integer('most_star')->default(0);
            $table->integer('least_d')->default(0);
            $table->integer('least_i')->default(0);
            $table->integer('least_s')->default(0);
            $table->integer('least_c')->default(0);
            $table->integer('least_star')->default(0);
            $table->integer('score_d')->default(0);
            $table->integer('score_i')->default(0);
            $table->integer('score_s')->default(0);
            $table->integer('score_c')->default(0);
            $table->string('dominant_profile')->nullable();
            $table->text('interpretation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disc_results');
        Schema::dropIfExists('disc_answers');
        Schema::dropIfExists('disc_attempts');
    }
};
