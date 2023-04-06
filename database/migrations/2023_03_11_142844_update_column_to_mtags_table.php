<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnToMtagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mtags', function (Blueprint $table) {
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
        Schema::table('mtags', function (Blueprint $table) {
            $table->dropColumn('approved_at');
            $table->dropColumn('approved_by');
            $table->dropColumn('rejected_at');
            $table->dropColumn('rejected_by');
        });
    }
}
