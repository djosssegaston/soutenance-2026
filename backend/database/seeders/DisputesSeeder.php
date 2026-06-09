<?php

namespace Database\Seeders;

use App\Enums\ProjectStatus;
use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DisputesSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::whereIn('statut', [ProjectStatus::ACTIVE->value, ProjectStatus::COMPLETED->value])
            ->whereHas('remboursements', function ($query) {
                $query->where('statut', 'en_retard');
            })
            ->get();
        $admin = User::where('role', 'admin')->first();

        foreach ($projects as $project) {
            Dispute::factory()->create([
                'project_id' => $project->id,
                'user_id' => $admin ? $admin->id : null,
                'type' => 'paiement_manquant',
                'statut' => 'ouvert',
                'description' => 'Litige lie au remboursement du projet.',
            ]);
        }
    }
}
