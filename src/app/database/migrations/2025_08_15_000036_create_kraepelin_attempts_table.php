<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraepelin_attempts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('kraepelin_form_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['draft', 'in_progress', 'submitted', 'expired', 'evaluated'])->default('draft');
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->integer('total_answered')->default(0);
            $table->integer('total_correct')->default(0);
            $table->integer('total_wrong')->default(0);
            $table->decimal('speed_score', 10, 2)->nullable();
            $table->decimal('accuracy_score', 10, 2)->nullable();
            $table->decimal('stability_score', 10, 2)->nullable();
            $table->decimal('final_score', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraepelin_attempts');
    }
};
