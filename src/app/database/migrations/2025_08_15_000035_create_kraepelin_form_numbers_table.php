<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraepelin_form_numbers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kraepelin_form_column_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->tinyInteger('value');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraepelin_form_numbers');
    }
};
