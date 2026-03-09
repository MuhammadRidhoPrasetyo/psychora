<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraepelin_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kraepelin_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('kraepelin_form_column_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->tinyInteger('top_number');
            $table->tinyInteger('bottom_number');
            $table->tinyInteger('user_answer')->nullable();
            $table->tinyInteger('correct_answer');
            $table->boolean('is_correct')->nullable();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraepelin_answers');
    }
};
