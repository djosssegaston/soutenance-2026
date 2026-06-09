<?php

namespace Database\Factories;

use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Funding>
 */
class FundingFactory extends Factory
{
    protected $model = Funding::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'institution_id' => Institution::factory(),
            'montant' => $this->faker->numberBetween(3000000, 50000000),
            'date_financement' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'statut' => $this->faker->randomElement(['finance', 'decaissé']),
        ];
    }
}
