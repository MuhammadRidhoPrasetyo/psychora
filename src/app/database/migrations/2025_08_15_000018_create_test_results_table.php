<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->decimal('raw_score', 10, 2)->nullable();
            $table->decimal('final_score', 10, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->text('interpretation')->nullable();
            $table->timestamps();

            $table->unique('test_attempt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_results');
    }
};
