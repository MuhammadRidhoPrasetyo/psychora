<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kraepelin_form_columns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('kraepelin_form_id')->constrained()->cascadeOnDelete();
            $table->integer('column_number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kraepelin_form_columns');
    }
};
