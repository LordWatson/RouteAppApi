<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users with an admin email address
        $adminUsers = User::where('email', 'like', '%@admin.com')->get();

        // Add admin users to admin role
        foreach($adminUsers as $adminUser){
            if(isset($adminUser)) $adminUser->assignRole('admin');
        }

        // Get all non admin users
        $users = User::whereNot('email', 'like', '%@admin.com')->get();

        // Assign regular user role
        foreach($users as $user){
            $user->assignRole('user');
        }
    }
}
