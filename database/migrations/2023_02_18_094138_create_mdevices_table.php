<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMdevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mdevices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('table');
            $table->string('room');
            $table->text('photo')->nullable();
            $table->text('tag')->nullable();
            $table->timestamps();
            $table->softDeletes()->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mdevices');
    }
}
