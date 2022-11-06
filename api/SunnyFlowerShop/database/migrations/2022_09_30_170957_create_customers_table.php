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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string("first_name");
            $table->string("last_name");
            $table->string("email")->unique()->index();
            $table->string("password");
            $table->longText("avatar")->nullable();
            // this "default_avatar" column is temporary
            $table->string("default_avatar")->default("https://styles.redditmedia.com/t5_1p1y2i/styles/profileIcon_3ryimhk1cpm41.jpg?width=256&height=256&frame=1&crop=256:256,smart&s=f611c9c0634b41165820ab80c11b8229d1398945");
            // $table->string("phone_number");
            $table->boolean("subscribed")->comment("SubscribeValueEnum");
            $table->boolean("disabled")->nullable()->comment("Default value is NULL, 0 for disable");
            // $table->rememberToken();
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
        Schema::dropIfExists('customers');
    }
};
