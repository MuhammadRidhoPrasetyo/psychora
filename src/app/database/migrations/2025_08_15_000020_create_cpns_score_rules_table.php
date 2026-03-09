<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cpns_score_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_type_id')->constrained()->cascadeOnDelete();
            $table->enum('category_code', ['TWK', 'TIU', 'TKP']);
            $table->decimal('correct_score', 8, 2);
            $table->decimal('wrong_score', 8, 2)->default(0);
            $table->decimal('empty_score', 8, 2)->default(0);
            $table->timestamps();

            $table->unique(['test_type_id', 'category_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cpns_score_rules');
    }
};
