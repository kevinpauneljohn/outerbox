<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCallCenterIdColumnToLgus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgus', function (Blueprint $table) {
            $table->unsignedInteger('call_center_id')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lgus', function (Blueprint $table) {
            $table->dropColumn('call_center_id');
        });
    }
}
