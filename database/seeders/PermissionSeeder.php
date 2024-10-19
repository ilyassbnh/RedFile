<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'manage users',
            'manage categories',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());

        // User gets limited permissions
        $userRole->givePermissionTo(['view documents', 'create documents']);
    }
}
