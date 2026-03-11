<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disc_forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('disc_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_form_id')->constrained()->cascadeOnDelete();
            $table->integer('number');
            $table->timestamps();

            $table->index(['disc_form_id', 'number']);
        });

        Schema::create('disc_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_question_id')->constrained()->cascadeOnDelete();
            $table->text('option_text');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['disc_question_id', 'sort_order']);
        });

        Schema::create('disc_option_scorings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_option_id')->constrained()->cascadeOnDelete();
            $table->enum('response_type', ['most', 'least']);
            $table->enum('disc_code', ['D', 'I', 'S', 'C', 'star']);
            $table->integer('score_value');
            $table->timestamps();

            $table->index(['disc_option_id', 'response_type']);
            $table->index('disc_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disc_option_scorings');
        Schema::dropIfExists('disc_options');
        Schema::dropIfExists('disc_questions');
        Schema::dropIfExists('disc_forms');
    }
};
