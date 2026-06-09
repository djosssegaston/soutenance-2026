<?php

namespace Database\Seeders;

use App\Models\DocumentRule;
use Illuminate\Database\Seeder;

class DocumentRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            // --- Porteur registration documents (context=registration, acteur=porteur) ---
            [
                'context' => 'registration',
                'acteur' => 'porteur',
                'label' => "Recto de la pièce d'identité",
                'slug' => 'recto',
                'description' => "Face avant de la pièce d'identité",
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 1,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'porteur',
                'label' => "Verso de la pièce d'identité",
                'slug' => 'verso',
                'description' => "Face arrière de la pièce d'identité",
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 2,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'porteur',
                'label' => 'Selfie',
                'slug' => 'selfie',
                'description' => 'Photo portrait du visage',
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png',
                'max_size' => 5,
                'order_column' => 3,
                'is_active' => true,
            ],

            // --- Institution registration documents (context=registration, acteur=institution) ---
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => 'Registre de Commerce (RCCM)',
                'slug' => 'rccm_file',
                'description' => 'Registre de commerce et de crédit mobilier',
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 1,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => 'Agrément',
                'slug' => 'agrement_file',
                'description' => "Agrément de l'institution",
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 2,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => 'Identifiant Fiscal (IFU)',
                'slug' => 'ifu_file',
                'description' => 'Carte de contribuable / IFU',
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 3,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => 'Logo',
                'slug' => 'logo_file',
                'description' => "Logo officiel de l'institution",
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png',
                'max_size' => 5,
                'order_column' => 4,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => "Recto de la pièce d'identité",
                'slug' => 'recto_institution',
                'description' => "Face avant de la pièce d'identité du responsable",
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 5,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => "Verso de la pièce d'identité",
                'slug' => 'verso_institution',
                'description' => "Face arrière de la pièce d'identité du responsable",
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 6,
                'is_active' => true,
            ],
            [
                'context' => 'registration',
                'acteur' => 'institution',
                'label' => 'Attestation bancaire',
                'slug' => 'attestation',
                'description' => 'Attestation de compte bancaire',
                'obligatoire' => true,
                'types_mime' => 'jpg,jpeg,png,pdf',
                'max_size' => 5,
                'order_column' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            DocumentRule::create($rule);
        }
    }
}
