<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        DB::table('roles')->insert([
//            'name'     => 'super admin',
//            'guard_name'    => 'web',
//        ]);
//
//        DB::table('roles')->insert([
//            'name'     => 'admin',
//            'guard_name'    => 'web',
//        ]);

        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'agent']);

        Permission::create(['name' => 'add role']);
        Permission::create(['name' => 'edit role']);
        Permission::create(['name' => 'delete role']);
    }
}
