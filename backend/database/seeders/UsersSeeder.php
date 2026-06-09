<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Armand Zinsou',
            'email' => 'admin@alogoto.bj',
            'role' => 'admin',
            'statut' => 'actif',
            'telephone' => '+229 90 00 00 00',
            // Format login: 8+ chars, uppercase, lowercase, number
            'password' => Hash::make('Admin2026'),
        ]);

        User::factory()->create([
            'name' => 'Nadine Dossou',
            'email' => 'porteur1@alogoto.bj',
            'role' => 'porteur',
            'statut' => 'actif',
            'telephone' => '+229 90 11 11 11',
            'password' => Hash::make('Porteur2026'),
        ]);

        User::factory()->create([
            'name' => 'Banque Atlantique Benin',
            'email' => 'institution1@alogoto.bj',
            'role' => 'institution',
            'statut' => 'verifie',
            'telephone' => '+229 90 22 22 22',
            'password' => Hash::make('Institution2026'),
        ]);

        User::factory()->count(5)->create([
            'role' => 'porteur',
            'statut' => 'actif',
        ]);

        User::factory()->count(3)->create([
            'role' => 'institution',
            'statut' => 'verifie',
        ]);
    }
}
