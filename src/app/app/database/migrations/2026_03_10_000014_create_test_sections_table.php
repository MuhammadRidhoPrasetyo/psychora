<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_sections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('instruction')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['test_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_sections');
    }
};
