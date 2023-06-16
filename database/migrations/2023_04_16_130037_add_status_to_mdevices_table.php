<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToMdevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mdevices', function (Blueprint $table) {
            $table->char('status', 1)->nullable()->default(0);
            $table->timestamp('checked_at')->nullable();
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
            $table->dropColumn('status');
            $table->dropColumn('checked_at');
        });
    }
}
