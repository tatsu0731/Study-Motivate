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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('社名');
            $table->unsignedInteger('industry')->comment('業種 
            0: 水産・農林業 
            1: メーカー 
            2: サービス・インフラ 
            3: 商社（総合・専門） 
            4: 銀行・証券・保険・金融 
            5: 情報（広告・通信・マスコミ） 
            6: 百貨店・専門店・流通・小売 
            7: IT・ソフトウェア・情報処理');
            $table->unsignedInteger('valid')->comment('0:契約中 
            1: 契約解除 
            2: 契約停止（保留中）');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
