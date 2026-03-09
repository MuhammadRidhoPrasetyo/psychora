<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ist_subtests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('ist_form_id')->constrained()->cascadeOnDelete();
            $table->enum('code', ['SE', 'WA', 'AN', 'GE', 'ME', 'RA', 'ZR', 'FA', 'WU']);
            $table->string('name');
            $table->enum('category', ['verbal', 'numerical', 'figural']);
            $table->integer('duration_minutes')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ist_subtests');
    }
};
