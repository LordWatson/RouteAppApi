<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Give all permissions to admin
        $adminRole = Role::create(['name' => 'admin'])
            ->givePermissionTo(Permission::all());

        // Only allow regular users permission to read other users
        $userRole = Role::create(['name' => 'user'])
            ->givePermissionTo(Permission::where('name', 'read user'));
    }
}
