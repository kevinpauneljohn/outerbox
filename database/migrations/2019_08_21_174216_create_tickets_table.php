<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('lead_id');
            $table->unsignedInteger('user_assigned_id');
            $table->unsignedInteger('user_created_id');
            $table->unsignedInteger('lgu_id')->nullable();
            $table->dateTime('date_reported')->nullable();
            $table->dateTime('time_handled')->nullable();
            $table->dateTime('duration_before_agent_handled_call')->nullable();
            $table->dateTime('call_duration')->nullable();
            $table->dateTime('duration_until_agent_transfer_request')->nullable();
            $table->dateTime('duration_accepted_lgu')->nullable();
            $table->dateTime('duration_response')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('tickets');
    }
}
