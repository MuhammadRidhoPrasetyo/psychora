<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_test_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('program_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('test_type_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['program_id', 'test_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_test_types');
    }
};
