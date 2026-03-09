<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disc_results', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_attempt_id')->constrained()->cascadeOnDelete();
            $table->integer('score_d')->default(0);
            $table->integer('score_i')->default(0);
            $table->integer('score_s')->default(0);
            $table->integer('score_c')->default(0);
            $table->string('dominant_profile')->nullable();
            $table->text('interpretation')->nullable();
            $table->timestamps();

            $table->unique('disc_attempt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disc_results');
    }
};
