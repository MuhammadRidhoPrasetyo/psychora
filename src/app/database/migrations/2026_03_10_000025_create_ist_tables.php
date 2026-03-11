<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ist_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });

        Schema::create('ist_subtests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_form_id')->constrained()->cascadeOnDelete();
            $table->string('subtest_code', 10);
            $table->string('subtest_name');
            $table->integer('sort_order');
            $table->integer('duration_minutes')->nullable();
            $table->integer('max_score')->nullable();
            $table->timestamps();

            $table->index(['ist_form_id', 'subtest_code']);
            $table->index('sort_order');
        });

        Schema::create('ist_form_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_form_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('ist_subtest_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_randomized')->default(false);
            $table->integer('number_of_questions')->default(100);
            $table->integer('sort_order')->default(0);
            $table->integer('minimum_score')->nullable();
            $table->double('multiplier')->default(1);
            $table->integer('duration_minutes')->nullable();
            $table->boolean('clue_first')->default(false);
            $table->timestamps();

            $table->unique(['ist_form_id', 'ist_subtest_id']);
            $table->index(['ist_form_id', 'sort_order']);
        });

        Schema::create('ist_instructions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_form_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('ist_subtest_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
        });

        Schema::create('ist_clues', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_form_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('ist_subtest_id')->nullable()->constrained()->nullOnDelete();
            $table->text('clue');
            $table->integer('duration')->nullable();
            $table->timestamps();
        });

        Schema::create('ist_subtest_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_subtest_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('question_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['ist_subtest_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ist_subtest_questions');
        Schema::dropIfExists('ist_clues');
        Schema::dropIfExists('ist_instructions');
        Schema::dropIfExists('ist_form_items');
        Schema::dropIfExists('ist_subtests');
        Schema::dropIfExists('ist_forms');
    }
};
