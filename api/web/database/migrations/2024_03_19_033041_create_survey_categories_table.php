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
        Schema::create('survey_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->string('name')->comment('カテゴリータイトル');
            $table->foreignId('department_id')->nullable()->constrained();
            $table->unsignedInteger('category')->comment('0: メインアンケート 
            1: マンスリーアンケート');
            $table->unsignedInteger('status')->comment('0: 実施前
            1: 実施中 
            2: 停止中');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_categories');
    }
};
