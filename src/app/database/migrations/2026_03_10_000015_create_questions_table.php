<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('test_section_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('question_type', ['multiple_choice', 'multi_select', 'essay', 'true_false', 'number_input', 'matrix'])->default('multiple_choice');
            $table->longText('content');
            $table->string('media_url')->nullable();
            $table->longText('explanation')->nullable();
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->nullable();
            $table->decimal('score', 8, 2)->default(1);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['test_id', 'sort_order']);
            $table->index(['test_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
