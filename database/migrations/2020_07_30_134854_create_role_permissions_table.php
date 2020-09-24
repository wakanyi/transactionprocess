<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('roleID');
            $table->unsignedBigInteger('permissionID');
            $table->timestamps();

            $table->foreign('roleID')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');

            $table->foreign('permissionID')
                    ->references('id')
                    ->on('permissions')
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
        Schema::dropIfExists('role_permissions');
    }
}
