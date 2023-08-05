<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crudArray = [
            'create user',
            'read user',
            'edit user',
            'delete user'
        ];

        foreach($crudArray as $permission){
            Permission::create(['name' => $permission]);
        }
    }
}
