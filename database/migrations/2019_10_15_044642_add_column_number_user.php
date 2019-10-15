<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnNumberUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string("mnumber")->nullable()->after("username");
            $table->string("cperson")->nullable()->after("mnumber");
            $table->string("cnumber")->nullable()->after("cperson");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn("mnumber");
            $table->dropColumn("cperson");
            $table->dropColumn("cnumber");
        });
    }
}
