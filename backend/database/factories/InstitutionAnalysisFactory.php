<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstitutionAnalysis>
 */
class InstitutionAnalysisFactory extends Factory
{
    protected $model = InstitutionAnalysis::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'institution_id' => Institution::factory(),
            'statut' => $this->faker->randomElement(['en_analyse', 'termine', 'rejete']),
            'risk_score' => $this->faker->numberBetween(45, 95),
            'commentaire' => $this->faker->sentence(10),
            'entretien_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
        ];
    }
}
