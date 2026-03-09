<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ist_subtest_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_subtest_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('question_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['ist_subtest_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ist_subtest_questions');
    }
};
