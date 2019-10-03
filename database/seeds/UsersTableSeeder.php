<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $user = new User;
        $user->firstname = 'superadmin_fname';
        $user->middlename = 'superadmin_mname';
        $user->lastname = 'superadmin_lname';
        $user->extname = '';
        $user->email = 'superadmin@gmail.com';
        $user->username = 'superadmin';
        $user->password = bcrypt(123);
        $user->active = 0;
        $user->assignRole('super admin');
        $user->save();
    }
}
