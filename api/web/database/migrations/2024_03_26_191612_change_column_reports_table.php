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
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('survey_category_id', 'survey_term_id');
            $table->dropForeign(['survey_category_id']);
            $table->foreign('survey_term_id')->references('id')->on('survey_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->renameColumn('survey_term_id', 'survey_category_id');
            $table->dropForeign(['survey_term_id']);
            $table->foreign('survey_category_id')->references('id')->on('survey_categories');
        });
    }
};
