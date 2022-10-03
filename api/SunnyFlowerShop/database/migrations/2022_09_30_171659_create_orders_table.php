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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            // $table->foreign("customer_id")->references("id")->on("customers");
            $table->integer("customer_id"); // foreign key of id in customers table
            $table->foreignId("voucher_id")->nullable(); // NULL only happen when customer don't use voucher
            $table->dateTime("date_order");
            $table->string("address");
            $table->string("name_receiver");
            $table->string("phone_receiver");
            $table->integer("total_price")->unsigned();
            $table->unsignedTinyInteger("status")->comment("OrderStatusEnum");
            $table->boolean("paid_type")->comment("0 for Cash Settlemen; 1 for Online Cash");
            $table->boolean("deleted_at")->nullable()->comment("Anything beside NULL value will be detemined as canceled order");
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
        Schema::dropIfExists('orders');
    }
};
