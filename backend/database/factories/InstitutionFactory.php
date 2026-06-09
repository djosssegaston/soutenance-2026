<?php

namespace Database\Factories;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    protected $model = Institution::class;

    public function definition(): array
    {
        $institutions = [
            'Banque Atlantique Benin',
            'BOA Benin',
            'Orabank Benin',
            'Ecobank Benin',
            'UBA Benin',
            'NSIA Banque Benin',
            'BGFI Bank Benin',
            'Caisse Nationale de Credit Agricole du Benin',
            'FECECAM Benin',
            'Finadev Microfinance',
        ];
        $cities = ['Cotonou', 'Porto-Novo', 'Abomey-Calavi', 'Parakou', 'Bohicon', 'Ouidah', 'Natitingou'];

        $name = $this->faker->randomElement($institutions);
        $slug = Str::slug($name, '.');
        $email = 'contact@'.$slug.'.bj';
        $phone = '+229 '.$this->faker->numerify('## ## ## ##');
        $city = $this->faker->randomElement($cities);

        return [
            'user_id' => User::factory()->state([
                'role' => 'institution',
                'statut' => 'verifie',
            ]),
            'nom' => $name,
            'email' => $email,
            'telephone' => $phone,
            'adresse' => $city.', Benin',
            'statut' => $this->faker->randomElement(['actif', 'verifie']),
        ];
    }
}
