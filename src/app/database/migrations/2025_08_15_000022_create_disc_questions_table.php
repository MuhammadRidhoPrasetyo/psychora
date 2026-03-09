<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disc_questions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('disc_form_id')->constrained()->cascadeOnDelete();
            $table->text('prompt')->nullable();
            $table->integer('number');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disc_questions');
    }
};
