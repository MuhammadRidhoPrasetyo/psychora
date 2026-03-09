<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papikostick_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('papikostick_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('papikostick_dimension_id')->constrained()->restrictOnDelete();
            $table->decimal('raw_score', 10, 2);
            $table->decimal('normalized_score', 10, 2)->nullable();
            $table->text('interpretation')->nullable();
            $table->timestamps();

            $table->unique(['papikostick_attempt_id', 'papikostick_dimension_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papikostick_results');
    }
};
