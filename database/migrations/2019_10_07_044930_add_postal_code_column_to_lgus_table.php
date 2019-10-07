<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostalCodeColumnToLgusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lgus', function (Blueprint $table) {
            $table->integer("postalCode")->nullable()->after("address");
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
            $table->dropColumn("postalCode");
        });
    }
}
