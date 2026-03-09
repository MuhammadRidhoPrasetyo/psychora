<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('papikostick_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('papikostick_form_id')->constrained()->cascadeOnDelete();
            $table->integer('item_no');
            $table->text('statement');
            $table->foreignUuid('dimension_id')->nullable()->constrained('papikostick_dimensions')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('papikostick_items');
    }
};
