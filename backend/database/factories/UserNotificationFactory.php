<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserNotification>
 */
class UserNotificationFactory extends Factory
{
    protected $model = UserNotification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['validation_projet', 'financement_recu', 'remboursement_confirme', 'litige_cree', 'message']),
            'contenu' => $this->faker->sentence(10),
            'lu' => $this->faker->boolean(30),
        ];
    }
}
