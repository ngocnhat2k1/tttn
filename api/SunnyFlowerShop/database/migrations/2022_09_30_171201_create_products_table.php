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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name")->index();
            $table->longText("description");
            $table->integer("price")->comment("VND format")->unsigned();
            $table->unsignedTinyInteger("percent_sale")->comment("Max is 100; Default is 0")->default(0);
            $table->longText("img")->nullable();
            // $table->string("noteable")->comment("Unique thing about this product");
            $table->integer("quantity")->unsigned();
            $table->boolean("status")->default(1)->comment("1 for in stock; 0 for out of stock");
            $table->boolean("deleted_at")->nullable()->comment("Value not NULL will be SOFT deleted (i mean hide)");
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
        Schema::dropIfExists('products');
    }
};
