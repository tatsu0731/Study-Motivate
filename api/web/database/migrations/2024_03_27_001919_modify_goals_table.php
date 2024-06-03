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
        Schema::table('goals', function (Blueprint $table) {
            $table->foreignId('survey_term_id')->constrained();
            $table->dropForeign(['survey_content_id']);
            $table->dropColumn('survey_content_id');
            $table->foreignId('survey_question_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('goals', function (Blueprint $table) {
            $table->foreignId('survey_content_id')->constrained();
            $table->dropForeign(['survey_term_id']);
            $table->dropColumn('survey_term_id');
            $table->dropForeign(['survey_question_id']);
            $table->dropColumn('survey_question_id');
        });
    }
};
