<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $s = User::create([
            'name' => 'Supervisor',
            'username' => 'supervisor',
            'email' => 'supervisor@spda.co.id',
            'password' => Hash::make('12345678'),
        ]);

        $s->assignRole('Supervisor');

        $o = User::create([
            'name' => 'Operator',
            'username' => 'operator',
            'email' => 'operator@spda.co.id',
            'password' => Hash::make('12345678'),
        ]);

        $o->assignRole('Operator');

        $m = User::create([
            'name' => 'Manager',
            'username' => 'manager',
            'email' => 'manager@spda.co.id',
            'password' => Hash::make('12345678'),
        ]);

        $m->assignRole('Manager');

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
