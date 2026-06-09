<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\Repayment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Repayment>
 */
class RepaymentFactory extends Factory
{
    protected $model = Repayment::class;

    public function definition(): array
    {
        $total = $this->faker->numberBetween(200000, 3000000);

        return [
            'project_id' => Project::factory(),
            'montant_total' => $total,
            'montant_rembourse' => 0,
            'montant_restant' => $total,
            'date_echeance' => $this->faker->dateTimeBetween('-2 months', '+6 months'),
            'date_paiement' => null,
            'statut' => $this->faker->randomElement(['en_attente', 'paye', 'en_retard']),
        ];
    }
}
