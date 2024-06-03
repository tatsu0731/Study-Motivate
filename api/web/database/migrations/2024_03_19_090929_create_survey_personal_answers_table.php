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
        Schema::create('survey_personal_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('survey_content_id')->constrained();
            $table->foreignId('department_id')->constrained();
            $table->unsignedInteger('gender')->comment('0: 男性 
            1: 女性
            2: その他');
            $table->unsignedInteger('age')->comment('0: 20代 
            1: 30代
            2: 40代
            3: 50代
            4: 60代
            5: 70代以上');
            $table->unsignedInteger('years_of_service')->comment('入社歴: 
            0.1年目 
            1.2～4年目 
            2.5～9年目 
            3.10～14年目 
            4.15～19年目 
            5.20～24年目 
            6.25～29年目 
            7.30～40年目 
            8.40年目以上');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_personal_answers');
    }
};
