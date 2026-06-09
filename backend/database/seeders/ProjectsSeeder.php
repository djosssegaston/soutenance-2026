<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use App\Models\ProjectStatusHistory;
use App\Models\ProjectValidation;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        $porteurs = User::where('role', 'porteur')->get();
        $admin = User::where('role', 'admin')->first();
        $institutions = Institution::all();

        $workflow = [
            ['statut' => ProjectStatus::SUBMITTED->value, 'validation' => 'en_analyse_admin', 'analysis' => false, 'funding' => false],
            ['statut' => ProjectStatus::ADMIN_VALIDATED->value, 'validation' => 'valide', 'analysis' => false, 'funding' => false],
            ['statut' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value, 'validation' => 'valide', 'analysis' => true, 'funding' => false],
            ['statut' => ProjectStatus::INSTITUTION_ACCEPTED->value, 'validation' => 'valide', 'analysis' => true, 'funding' => false],
            ['statut' => ProjectStatus::FUNDED->value, 'validation' => 'valide', 'analysis' => true, 'funding' => true],
            ['statut' => ProjectStatus::ACTIVE->value, 'validation' => 'valide', 'analysis' => true, 'funding' => true],
            ['statut' => ProjectStatus::COMPLETED->value, 'validation' => 'valide', 'analysis' => true, 'funding' => true],
            ['statut' => ProjectStatus::ADMIN_REJECTED->value, 'validation' => 'rejete', 'analysis' => false, 'funding' => false],
        ];

        $index = 0;
        foreach ($porteurs as $porteur) {
            $count = 2;
            for ($i = 0; $i < $count; $i++) {
                $state = $workflow[$index % count($workflow)];
                $index++;

                $project = Project::factory()->create([
                    'user_id' => $porteur->id,
                    'statut' => $state['statut'],
                ]);

                ProjectStatusHistory::create([
                    'project_id' => $project->id,
                    'old_status' => ProjectStatus::DRAFT->value,
                    'new_status' => $project->statut,
                    'reason' => 'Seed initial',
                    'actor_id' => $admin?->id,
                    'metadata' => ['source' => 'ProjectsSeeder'],
                ]);

                if ($admin && $state['validation'] === 'valide') {
                    ProjectValidation::factory()->create([
                        'project_id' => $project->id,
                        'admin_id' => $admin->id,
                        'decision' => 'valide',
                        'commentaire' => 'Validation initiale du projet.',
                    ]);
                }

                if ($state['analysis']) {
                    $institution = $institutions->random();
                    InstitutionAnalysis::factory()->create([
                        'project_id' => $project->id,
                        'institution_id' => $institution->id,
                        'statut' => in_array($state['statut'], [ProjectStatus::INSTITUTION_ACCEPTED->value, ProjectStatus::FUNDED->value, ProjectStatus::ACTIVE->value, ProjectStatus::COMPLETED->value], true)
                            ? 'accepte'
                            : 'en_analyse',
                        'risk_score' => random_int(50, 95),
                    ]);
                }
            }
        }

        // Assure au moins un projet par statut
        foreach ($workflow as $state) {
            if (! Project::where('statut', $state['statut'])->exists()) {
                $project = Project::factory()->create([
                    'user_id' => $porteurs->random()->id,
                    'statut' => $state['statut'],
                ]);

                ProjectStatusHistory::create([
                    'project_id' => $project->id,
                    'old_status' => ProjectStatus::DRAFT->value,
                    'new_status' => $project->statut,
                    'reason' => 'Seed initial',
                    'actor_id' => $admin?->id,
                    'metadata' => ['source' => 'ProjectsSeeder'],
                ]);

                if ($admin && $state['validation'] === 'valide') {
                    ProjectValidation::factory()->create([
                        'project_id' => $project->id,
                        'admin_id' => $admin->id,
                        'decision' => 'valide',
                        'commentaire' => 'Validation initiale du projet.',
                    ]);
                }

                if ($state['analysis']) {
                    $institution = $institutions->random();
                    InstitutionAnalysis::factory()->create([
                        'project_id' => $project->id,
                        'institution_id' => $institution->id,
                        'statut' => in_array($state['statut'], [ProjectStatus::INSTITUTION_ACCEPTED->value, ProjectStatus::FUNDED->value, ProjectStatus::ACTIVE->value, ProjectStatus::COMPLETED->value], true)
                            ? 'accepte'
                            : 'en_analyse',
                        'risk_score' => random_int(50, 95),
                    ]);
                }
            }
        }
    }
}
