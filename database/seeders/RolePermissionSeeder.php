<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Seeder;

# php artisan db:seed --class=RolePermissionSeeder

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Create permissions
        $usersPermission = Permission::create(['name' => 'users']);
        $rolesPermission = Permission::create(['name' => 'roles']);
        $importersPermission = Permission::create(['name' => 'importers']);

        $importsPermission = Permission::create(['name' => 'imports']);

        // Assign permissions to roles
        $adminRole->givePermissionTo($usersPermission, $rolesPermission, $importersPermission, $importsPermission);
        $userRole->givePermissionTo($importsPermission);
    }
}