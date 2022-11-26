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
        Schema::create('momo', function (Blueprint $table) {
            $table->id();
            $table->integer("order_id");
            $table->string("partner_code");
            $table->string("order_type")->nullable();
            $table->string("trans_id")->nullable();
            $table->string("pay_type")->nullable();
            $table->integer("status")->comment("-1 is for cancelled order; 0 is in payment state; 1 for confirm payment");
            $table->string("signature");
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
        //
    }
};
