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
            $table->string('code');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('rejected_by')->nullable();
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
            $table->string('code');
            $table->dropColumn('room_id');
            $table->dropColumn('table_id');
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_by');
        });
    }
}
