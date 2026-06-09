<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstNames = [
            'Armand', 'Chantal', 'Emmanuel', 'Nadine', 'Aicha', 'Komi', 'Gildas', 'Rosine',
            'Wilfried', 'Gloria', 'Sonia', 'Fidele', 'Pacome', 'Ornella', 'Marius', 'Prisca',
            'Germain', 'Eulalie', 'Odilon', 'Clarisse',
        ];
        $lastNames = [
            'Hounkpe', 'Dossou', 'Zinsou', 'Adjovi', 'Tchibozo', 'Kponton', 'Sossou', 'Agossa',
            'Hounnou', 'Sossoukpe', 'Ahouansou', 'Hounkpatin', 'Houssou', 'Koudjo', 'Alladaye', 'Yehouenou',
        ];

        $first = $this->faker->randomElement($firstNames);
        $last = $this->faker->randomElement($lastNames);
        $name = $first.' '.$last;

        $emailLocal = Str::slug($first.'.'.$last, '.');
        $email = $emailLocal.$this->faker->unique()->numberBetween(10, 999).'@alogoto.bj';

        $phone = '+229 '.$this->faker->numerify('## ## ## ##');

        return [
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'porteur',
            'telephone' => $phone,
            'statut' => $this->faker->randomElement(['actif', 'verifie']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
