<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_essay_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('question_id')->constrained()->cascadeOnDelete();
            $table->string('answer_text');
            $table->integer('score');
            $table->enum('match_type', ['exact', 'fuzzy', 'contains', 'regex'])->default('exact');
            $table->integer('priority')->default(0);
            $table->timestamps();

            $table->index(['question_id', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_essay_answers');
    }
};
