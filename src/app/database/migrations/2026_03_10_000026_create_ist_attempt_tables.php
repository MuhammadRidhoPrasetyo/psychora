<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ist_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ist_form_id')->constrained()->cascadeOnDelete();
            $table->string('current_subtest_code', 10)->nullable();
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'expired'])->default('not_started');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->decimal('total_score', 10, 2)->nullable();
            $table->integer('iq_score')->nullable();
            $table->timestamps();

            $table->index(['status', 'current_subtest_code']);
        });

        Schema::create('ist_subtest_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ist_subtest_id')->constrained()->cascadeOnDelete();
            $table->string('subtest_code', 10);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'skipped'])->default('not_started');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('deadline_at')->nullable();
            $table->integer('raw_score')->nullable();
            $table->decimal('scaled_score', 10, 2)->nullable();
            $table->unsignedBigInteger('random_seed')->nullable();
            $table->timestamps();

            $table->index(['subtest_code', 'status']);
        });

        Schema::create('ist_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_subtest_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('question_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->longText('answer_text')->nullable();
            $table->json('answer_json')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 8, 2)->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->index('is_correct');
        });

        Schema::create('ist_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_attempt_id')->constrained()->cascadeOnDelete();
            $table->string('category');
            $table->integer('raw_score')->nullable();
            $table->decimal('scaled_score', 10, 2)->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->text('interpretation')->nullable();
            $table->timestamps();

            $table->index(['ist_attempt_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ist_results');
        Schema::dropIfExists('ist_answers');
        Schema::dropIfExists('ist_subtest_attempts');
        Schema::dropIfExists('ist_attempts');
    }
};
