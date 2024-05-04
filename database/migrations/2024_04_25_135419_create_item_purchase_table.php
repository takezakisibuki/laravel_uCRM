<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_purchase', function (Blueprint $table) {
            $table->id();
            //constrained()メソッドで外部キー制約を設定 onUpdata('cascade')で親テーブルのidが変更された場合に子テーブルのidも変更される
            $table->foreignId('item_id')->constrained()->onUpdate('cascade');;
            $table->foreignId('purchase_id')->constrained()->onUpdate('cascade');;
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_purchase');
    }
};
