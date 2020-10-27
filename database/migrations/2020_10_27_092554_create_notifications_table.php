<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
	    $table->string('notification');
	    $table->unsignedBigInteger('userID');
            $table->boolean('is_read')->default(0);
            $table->timestamps();

<<<<<<< HEAD:database/migrations/2020_10_27_092554_create_notifications_table.php
		$table->foreign('userID')
=======
	    $table->foreign('userID')
>>>>>>> 7652ab9101e9e93a4ef4cd4cfb9bf7f0005ecb95:database/migrations/2020_10_19_092111_create_notifications_table.php
		  ->references('id')
		  ->on('users')
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
        Schema::dropIfExists('notifications');
    }
}
