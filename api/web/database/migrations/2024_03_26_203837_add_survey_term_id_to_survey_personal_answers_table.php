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
        Schema::table('survey_personal_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('survey_term_id')->nullable()->after('survey_content_id');
            $table->foreign('survey_term_id')->references('id')->on('survey_terms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_personal_answers', function (Blueprint $table) {
            $table->dropForeign(['survey_term_id']);
            $table->dropColumn('survey_term_id');
        });
    }
};
