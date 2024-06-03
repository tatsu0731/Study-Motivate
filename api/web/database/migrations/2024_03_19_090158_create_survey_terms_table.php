<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('survey_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('survey_category_id')->constrained();
            $table->date('start_date');
            $table->unsignedInteger('deadline');
            $table->unsignedInteger('frequency');
            $table->unsignedInteger('count');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_terms');
    }
};
