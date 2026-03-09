<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papikostick_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('papikostick_form_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'in_progress', 'submitted', 'expired', 'evaluated'])->default('draft');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papikostick_attempts');
    }
};
