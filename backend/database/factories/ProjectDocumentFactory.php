<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectDocument>
 */
class ProjectDocumentFactory extends Factory
{
    protected $model = ProjectDocument::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['business_plan', 'kbis', 'statuts', 'budget', 'identite']);

        return [
            'project_id' => Project::factory(),
            'type' => $type,
            'fichier' => $type.'_'.$this->faker->uuid().'.pdf',
            'statut_validation' => $this->faker->randomElement(['en_attente', 'valide', 'refuse']),
            'uploaded_at' => $this->faker->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
