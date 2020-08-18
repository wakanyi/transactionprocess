<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_roles', function (Blueprint $table) {
            $table->id();
            $table->integer('userID');
            $table->integer('roleID');
            $table->timestamps();

            $table->foreign('userID')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');

            $table->foreign('roleID')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_roles');
    }
}
