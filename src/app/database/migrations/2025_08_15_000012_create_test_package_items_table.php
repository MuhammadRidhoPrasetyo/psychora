<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('test_package_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('test_package_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('test_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_package_items');
    }
};
