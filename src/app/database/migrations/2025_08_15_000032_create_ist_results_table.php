<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ist_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_attempt_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['verbal', 'numerical', 'figural']);
            $table->decimal('raw_score', 10, 2)->nullable();
            $table->decimal('scaled_score', 10, 2)->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->text('interpretation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ist_results');
    }
};
