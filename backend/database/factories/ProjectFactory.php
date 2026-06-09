<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $titles = [
            'Unite de transformation d\'ananas',
            'Cooperative de soja a Parakou',
            'Mini-centrale solaire rurale',
            'Plateforme de paiement mobile locale',
            'Centre de sante communautaire',
            'Unite de conditionnement de noix de cajou',
            'Hub logistique Cotonou - Porto-Novo',
            'Fermes avicoles modernes',
            'Reseau de points de vente alimentaires',
            'Centre de formation numerique',
        ];
        $sectors = [
            'Agriculture',
            'Agro-industrie',
            'Energie',
            'Fintech',
            'Sante',
            'Education',
            'Transport',
            'Commerce',
            'Logistique',
            'Artisanat',
        ];
        $cities = ['Cotonou', 'Porto-Novo', 'Abomey-Calavi', 'Parakou', 'Bohicon', 'Ouidah', 'Natitingou', 'Djougou'];

        return [
            'user_id' => User::factory()->state([
                'role' => 'porteur',
            ]),
            'titre' => $this->faker->randomElement($titles),
            'description' => $this->faker->paragraph(3),
            'secteur' => $this->faker->randomElement($sectors),
            'montant_demande' => $this->faker->numberBetween(3000000, 60000000),
            'montant_finance' => 0,
            'duree' => $this->faker->randomElement(['6 mois', '12 mois', '18 mois', '24 mois']),
            'localisation' => $this->faker->randomElement($cities),
            'statut' => ProjectStatus::DRAFT->value,
        ];
    }
}
