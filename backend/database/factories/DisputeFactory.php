<?php

namespace Database\Factories;

use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dispute>
 */
class DisputeFactory extends Factory
{
    protected $model = Dispute::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['decaissement_retarde', 'document_conteste', 'paiement_manquant']),
            'statut' => $this->faker->randomElement(['ouvert', 'en_mediation', 'resolu']),
            'description' => $this->faker->sentence(12),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
