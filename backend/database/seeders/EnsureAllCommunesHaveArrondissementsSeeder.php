<?php

namespace Database\Seeders;

use App\Models\Arrondissement;
use App\Models\Commune;
use App\Models\Quartier;
use Illuminate\Database\Seeder;

class EnsureAllCommunesHaveArrondissementsSeeder extends Seeder
{
    public function run(): void
    {
        $communes = Commune::whereDoesntHave('arrondissements')->get();
        $count = $communes->count();

        $this->command->info("Ajout d'arrondissements pour {$count} communes...");

        foreach ($communes as $commune) {
            $arrondissement = Arrondissement::create([
                'commune_id' => $commune->id,
                'nom' => 'Centre',
            ]);

            Quartier::create([
                'arrondissement_id' => $arrondissement->id,
                'nom' => 'Principal',
            ]);
        }

        $this->command->info("Terminé : {$count} arrondissements et {$count} quartiers créés.");
    }
}
