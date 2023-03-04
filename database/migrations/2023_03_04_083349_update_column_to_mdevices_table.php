<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnToMdevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdevices', function (Blueprint $table) {
            $table->dropColumn('room');
            $table->bigInteger('room_id')->nullable()->reference('mrooms')->on('id');
            $table->dropColumn('table');
            $table->bigInteger('table_id')->nullable()->reference('mtables')->on('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mdevices', function (Blueprint $table) {
            $table->string('table');
            $table->string('room');
            $table->dropColumn('room_id');
            $table->dropColumn('table_id');
        });
    }
}
