<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papikostick_answers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('papikostick_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('papikostick_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('selected_option_id')->constrained('papikostick_item_options')->restrictOnDelete();
            $table->integer('score_value')->default(0);
            $table->dateTime('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['papikostick_attempt_id', 'papikostick_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papikostick_answers');
    }
};
