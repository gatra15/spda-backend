<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewRole extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['name' => 'Supervisor']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Manager']);
        $user = User::findOrFail(1);
        $user->assignRole('Admin');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
