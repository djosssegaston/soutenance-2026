<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\Repayment;
use App\Models\RepaymentConfirmation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RepaymentConfirmation>
 */
class RepaymentConfirmationFactory extends Factory
{
    protected $model = RepaymentConfirmation::class;

    public function definition(): array
    {
        return [
            'remboursement_id' => Repayment::factory(),
            'institution_id' => Institution::factory(),
            'date_confirmation' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
