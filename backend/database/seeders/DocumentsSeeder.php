<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Database\Seeder;

class DocumentsSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            $count = random_int(2, 4);
            ProjectDocument::factory()->count($count)->create([
                'project_id' => $project->id,
            ]);
        }
    }
}
