<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('userID')->unique();
            $table->string('name');
            $table->string('email')->unique()->notNullable();
            $table->string('phone_number');
            $table->string('address');
            $table->string('country');
            $table->string('region');
            $table->string('password');
            $table->string('usertype');
            $table->json('profile_picture')->nullable();
            $table->json('id_document')->nullable();
            $table->json('tin_certificate')->nullable();
            $table->json('passport')->nullable();
            $table->boolean('email_verify')->default(0);
            $table->boolean('admin_verify')->default(0);
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
        Schema::dropIfExists('users');
    }
}
