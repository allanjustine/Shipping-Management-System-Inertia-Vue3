<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role_admin     =   Role::create(['name' =>'Admin']);
        $role_standard    =   Role::create(['name' =>'Standard']);

        $manage_user = Permission::create(['name' => 'manage-user']);
        $manage_ship = Permission::create(['name' => 'manage-ship']);
        $view_ticket = Permission::create(['name' => 'view-ticket']);
        $add_ticket =  Permission::create(['name' => 'add-ticket']);
        $delete_ticket = Permission::create(['name' => 'delete-ticket']);
        $manage_ticket = Permission::create(['name' => 'manage-ticket']);


        $permission_admin =  [ $manage_user, $manage_ship,  $manage_ticket, $view_ticket, $delete_ticket
                                 ];

        $permission_doctor =  [$add_ticket, $manage_ticket];

        $role_admin->syncPermissions($permission_admin);
        $role_standard->syncPermissions($permission_doctor);
        // $role_patient->givePermissionTo($add_ticket, $edit_ticket, $delete_ticket);

        User::find(1)->assignRole($role_admin);
        User::find(2)->assignRole($role_standard);

    }
}
