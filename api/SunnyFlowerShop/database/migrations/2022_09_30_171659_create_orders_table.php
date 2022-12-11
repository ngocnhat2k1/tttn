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
            $table->dateTime("expected_delivery_time")->nullable()->comment("By default it's 7 days after order is created");
            $table->dateTime("date_order");
            $table->string("id_delivery");
            $table->string("order_code")->nullable();
            $table->string("street");
            $table->string("ward");
            $table->string("district");
            $table->string("province");
            $table->string("name_receiver");
            $table->string("phone_receiver");
            $table->unsignedBigInteger("total_price")->unsigned();
            $table->unsignedBigInteger("total_fee")->unsigned()->comment("Shipping fee. Take note that Shop will handle this and NOT customer")->nullable();
            $table->string("trans_type")->comment("Transport type")->nullable();
            $table->TinyInteger("status")->default(0)->comment("OrderStatusEnum");
            $table->unsignedTinyInteger("paid_type")->comment("0 for Cash Settlemen; 1 for Online Cash; 2 for QR Online Cash (through phone)");
            // $table->boolean("deleted_by")->nullable()->comment("This one has 3 status: 1 for deleted by admin: 0 for customer and NULL for not delete");
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
