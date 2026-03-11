<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('psychotest_aspects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
        });

        Schema::create('psychotest_characteristics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_aspect_id')->constrained()->cascadeOnDelete();
            $table->string('code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['psychotest_aspect_id', 'code']);
            $table->index('sort_order');
        });

        Schema::create('psychotest_characteristic_scores', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_characteristic_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('score');
            $table->text('description');
            $table->timestamps();

            $table->index(['psychotest_characteristic_id', 'score']);
        });

        Schema::create('psychotest_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('psychotest_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_form_id')->constrained()->cascadeOnDelete();
            $table->integer('number');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['psychotest_form_id', 'number']);
            $table->index('is_active');
        });

        Schema::create('psychotest_question_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_question_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->text('statement');
            $table->tinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['psychotest_question_id', 'sort_order']);
        });

        Schema::create('psychotest_option_characteristic_mappings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('psychotest_option_id')->constrained('psychotest_question_options')->cascadeOnDelete();
            $table->foreignUuid('psychotest_aspect_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('psychotest_characteristic_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('weight');
            $table->timestamps();

            $table->index(['psychotest_option_id', 'psychotest_characteristic_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('psychotest_option_characteristic_mappings');
        Schema::dropIfExists('psychotest_question_options');
        Schema::dropIfExists('psychotest_questions');
        Schema::dropIfExists('psychotest_forms');
        Schema::dropIfExists('psychotest_characteristic_scores');
        Schema::dropIfExists('psychotest_characteristics');
        Schema::dropIfExists('psychotest_aspects');
    }
};
