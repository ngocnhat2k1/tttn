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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            // Will add unique to name column later
            $table->string("name")->index(); // Will add unique() later on
            $table->unsignedTinyInteger("percent")->comment("Max is 100");
            $table->unsignedInteger("usage")->default(5)->comment("Minimum limit usage is 5");
            $table->dateTime("expired_date");
            $table->boolean("deleted")->nullable()->comment("1 for deleted, NULL for default value");
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
        Schema::dropIfExists('vouchers');
    }
};
