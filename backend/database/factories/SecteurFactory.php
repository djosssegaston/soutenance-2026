<?php

namespace Database\Factories;

use App\Models\Secteur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Secteur>
 */
class SecteurFactory extends Factory
{
    protected $model = Secteur::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'icone' => $this->faker->randomElement(['bi-building', 'bi-shop', 'bi-tree', 'bi-laptop']),
            'is_active' => true,
            'order_column' => $this->faker->numberBetween(1, 100),
        ];
    }
}
