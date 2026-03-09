<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disc_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('disc_question_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('most_option_id')->constrained('disc_options')->restrictOnDelete();
            $table->foreignUuid('least_option_id')->constrained('disc_options')->restrictOnDelete();
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['disc_attempt_id', 'disc_question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disc_answers');
    }
};
