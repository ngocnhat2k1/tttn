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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string("user_name");
            $table->string("email")->unique();
            $table->string("password");
            $table->longText("avatar")->nullable();
            // this "default_avatar" is temporary
            $table->string("default_avatar")->default("https://i.kym-cdn.com/photos/images/facebook/001/923/856/7a0");
            $table->boolean("level")->default(0)->comment("1 for Super Admin; 0 for Admin");

            // Temporary, will be deleted after i need to do a login function again
            // $table->string("token")->nullable();
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
        Schema::dropIfExists('admins');
    }
};
