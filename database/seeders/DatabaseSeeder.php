<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;


// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        /*  \App\Models\User::factory()->create([
             
            'name' => 'super_admin',
            'prenom' => 'super_admin',
            'type' => '1',
            'nb_appel_recu' => '11',
            'nb_appel_traite' => '11',
            'cin' => 'BH1111',
            'date_embauche' => now(),
            'niveau_etude' => 'bac',
            'is_actif' => '1',	
            'solde_conge' => '1000',
            'email' => 'superadmin@email.com',
            'password' => Hash::make('superadmin'), // password
        
         ]);  */
        
         \App\Models\Societe::factory()->create([
             
            'raison_sociale' => 'societe',
            'user_id' => '1',
            'nom_contact' => 'ahmed',
            'prenom_contact' => 'slimani',
            'email' => 'ahmed@email.com',
            
        
         ]); 
    }
}
