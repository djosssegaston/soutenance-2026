<?php

namespace Database\Factories;

use App\Models\DocumentRule;
use App\Models\Secteur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DocumentRule>
 */
class DocumentRuleFactory extends Factory
{
    protected $model = DocumentRule::class;

    public function definition(): array
    {
        return [
            'secteur_id' => Secteur::factory(),
            'label' => $this->faker->sentence(3),
            'slug' => $this->faker->unique()->slug(1),
            'obligatoire' => $this->faker->boolean(),
            'acteur' => $this->faker->randomElement(['porteur', 'investisseur']),
            'types_mime' => 'pdf,jpg,png',
            'max_size' => 5120,
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'order_column' => $this->faker->numberBetween(1, 100),
        ];
    }
}
