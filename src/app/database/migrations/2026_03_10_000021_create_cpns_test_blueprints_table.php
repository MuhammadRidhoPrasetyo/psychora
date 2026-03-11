<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpns_test_blueprints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->enum('category_code', ['TWK', 'TIU', 'TKP']);
            $table->integer('total_questions');
            $table->integer('passing_score')->nullable();
            $table->timestamps();

            $table->index(['test_id', 'category_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpns_test_blueprints');
    }
};
