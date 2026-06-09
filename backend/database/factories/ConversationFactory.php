<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'type' => 'porteur_institution',
        ];
    }
}
