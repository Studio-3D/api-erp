<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            [
                'email' => 'superadmin@gmail.com',
            ],
            [
                'societe_id' => 1,
                'name' => 'super_admin',
                'prenom' => 'super_admin',
                'password' => Hash::make('superadmin'),
                'role' => '1', // adapte si besoin
            ]
        );
    }
}
