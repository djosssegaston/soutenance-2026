<?php

namespace Database\Seeders;

use App\Models\DocumentRule;
use App\Models\Secteur;
use Illuminate\Database\Seeder;

class DefaultSecteursSeeder extends Seeder
{
    public function run(): void
    {
        $secteurs = [
            ['nom' => 'Agriculture', 'description' => 'Agriculture, élevage, pêche', 'icone' => 'bi-tree', 'order_column' => 1],
            ['nom' => 'Commerce', 'description' => 'Commerce général, distribution', 'icone' => 'bi-cart', 'order_column' => 2],
            ['nom' => 'Technologie', 'description' => 'IT, numérique, innovation', 'icone' => 'bi-laptop', 'order_column' => 3],
            ['nom' => 'Santé', 'description' => 'Santé, médical, bien-être', 'icone' => 'bi-heart-pulse', 'order_column' => 4],
            ['nom' => 'Éducation', 'description' => 'Éducation, formation', 'icone' => 'bi-book', 'order_column' => 5],
            ['nom' => 'Transport', 'description' => 'Transport, logistique', 'icone' => 'bi-truck', 'order_column' => 6],
            ['nom' => 'Immobilier', 'description' => 'Immobilier, construction', 'icone' => 'bi-building', 'order_column' => 7],
            ['nom' => 'Services', 'description' => 'Services aux entreprises et particuliers', 'icone' => 'bi-gear', 'order_column' => 8],
        ];

        foreach ($secteurs as $s) {
            Secteur::create($s);
        }

        $docs = [
            ['label' => 'Pièce d\'identité', 'slug' => 'piece_identite', 'obligatoire' => true, 'order_column' => 1],
            ['label' => 'Registre de commerce', 'slug' => 'registre_commerce', 'obligatoire' => true, 'order_column' => 2],
            ['label' => 'Plan d\'affaires', 'slug' => 'plan_affaires', 'obligatoire' => true, 'order_column' => 3],
            ['label' => 'Devis prévisionnel', 'slug' => 'devis_previsionnel', 'obligatoire' => true, 'order_column' => 4],
            ['label' => 'Carte de contribuable', 'slug' => 'carte_contribuable', 'obligatoire' => true, 'order_column' => 5],
            ['label' => 'Diplômes/Certificats', 'slug' => 'diplomes', 'obligatoire' => false, 'order_column' => 6],
            ['label' => 'Garantie bancaire', 'slug' => 'garantie_bancaire', 'obligatoire' => false, 'order_column' => 7],
            ['label' => 'Photos du projet', 'slug' => 'photos_projet', 'obligatoire' => false, 'order_column' => 8],
            ['label' => 'Attestation de domicile', 'slug' => 'attestation_domicile', 'obligatoire' => true, 'order_column' => 9],
            ['label' => 'Casier judiciaire', 'slug' => 'casier_judiciaire', 'obligatoire' => true, 'order_column' => 10],
        ];

        foreach ($docs as $d) {
            DocumentRule::create($d);
        }
    }
}
