<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CallcenterDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('callcenterdetails', function (Blueprint $table){
            $table->unsignedBigInteger('cc_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('cc_id')->references('id')->on('call_centers');
            $table->foreign('user_id')->references('id')->on('users');
            $table->primary(['cc_id','user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('callcenterdetails');
    }
}
