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
        Schema::create('gacha_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('gacha_id');
            $table->bigInteger('product_id');
            $table->tinyInteger('is_last')->default(0);
            $table->integer('order')->default(0);
            $table->integer('count');
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
        Schema::dropIfExists('gacha_products');
    }
};
