<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\InstitutionAnalysis;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FinancementsSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::whereIn('statut', [
            ProjectStatus::FUNDED->value,
            ProjectStatus::ACTIVE->value,
            ProjectStatus::COMPLETED->value,
        ])->get();

        foreach ($projects as $project) {
            $analysis = InstitutionAnalysis::where('project_id', $project->id)->first();
            $institutionId = $analysis ? $analysis->institution_id : Institution::inRandomOrder()->value('id');

            $montant = $project->montant_demande;
            $funding = Funding::factory()->create([
                'project_id' => $project->id,
                'institution_id' => $institutionId,
                'montant' => $montant,
                'date_financement' => Carbon::now()->subMonths(random_int(1, 6)),
                'statut' => 'finance',
            ]);

            $project->update([
                'montant_finance' => $funding->montant,
            ]);
        }
    }
}
