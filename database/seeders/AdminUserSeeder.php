<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create a default societe if none exists
        $societe = \App\Models\Societe::firstOrCreate(
            ['email' => 'admin@company.com'],
            [
                'raison_sociale' => 'Default Company',
                'raison_sociale_concatene' => 'default-company',
                'email' => 'admin@company.com',
                'nom_contact' => 'Admin',
                'prenom_contact' => 'User',
                'tel' => '1234567890',
                'adresse' => 'Default Address',
            ]
        );
        
        $this->command->info('Societe created or found: ' . $societe->raison_sociale);
        
        // Check if admin user already exists
        $user = \App\Models\User::where('email', 'superadmin@gmail.com')->first();
        
        if ($user) {
            $this->command->info('Admin user already exists!');
            return;
        }
        
        // Create admin user
        \App\Models\User::create([
            'email' => 'superadmin@gmail.com',
            'name' => 'Super',
            'prenom' => 'Admin',
            'password' => Hash::make('superadmin'),
            'role' => 0,
            'societe_id' => $societe->id,
        ]);
        
        $this->command->info('✅ Admin user created: superadmin@gmail.com / superadmin');
    }
}
