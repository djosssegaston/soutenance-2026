<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectValidation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectValidation>
 */
class ProjectValidationFactory extends Factory
{
    protected $model = ProjectValidation::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'admin_id' => User::factory()->state(['role' => 'admin']),
            'decision' => $this->faker->randomElement(['valide', 'refuse']),
            'commentaire' => $this->faker->sentence(10),
            'date_decision' => $this->faker->dateTimeBetween('-2 months', 'now'),
        ];
    }
}
