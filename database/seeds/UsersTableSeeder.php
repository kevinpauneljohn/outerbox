<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'firstname'     => 'admin_fname',
            'middlename'    => 'admin_mname',
            'lastname'      => 'admin_lname',
            'extname'       => '',
            'email'         => 'admin@gmail.com',
            'username'      => 'admin',
            'password'      => bcrypt(123)
        ]);

        DB::table('users')->insert([
            'firstname'     => 'superadmin_fname',
            'middlename'    => 'superadmin_mname',
            'lastname'      => 'superadmin_lname',
            'extname'       => '',
            'email'         => 'superadmin@gmail.com',
            'username'      => 'superadmin',
            'password'      => bcrypt(123)
        ]);
    }
}
