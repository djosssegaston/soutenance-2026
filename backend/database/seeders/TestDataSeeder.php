<?php

namespace Database\Seeders;

use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use App\Models\RendezVous;
use App\Models\Repayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing data
        $user = User::where('role', 'porteur')->first();
        $institution = Institution::first();
        $project = Project::first();
        $funding = Funding::with('project')->first();

        if (! $user || ! $institution || ! $project) {
            echo "Missing required data (user, institution, or project)\n";

            return;
        }

        // Clear existing test data
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('rendez_vous')->truncate();
        DB::table('remboursements')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create Rendez-vous with different statuses
        $rdvData = [
            [
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'project_id' => $project->id,
                'date_heure' => Carbon::now()->addDays(5),
                'objet' => 'Discussion financement projet',
                'lieu' => 'Agence Abidjan',
                'statut' => 'planifie',
                'notes_porteur' => 'Apporter dossiers',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'project_id' => $project->id,
                'date_heure' => Carbon::now()->subDays(2),
                'objet' => 'Signature convention',
                'lieu' => 'Visio',
                'statut' => 'accepte',
                'notes_porteur' => 'Signé par les deux parties',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'project_id' => $project->id,
                'date_heure' => Carbon::now()->subDays(10),
                'objet' => 'Revue technique',
                'lieu' => 'Bureau Yamoussoukro',
                'statut' => 'rejete',
                'notes_porteur' => 'Indisponible',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'project_id' => $project->id,
                'date_heure' => Carbon::now()->subDays(20),
                'objet' => 'Premier paiement',
                'lieu' => 'Banque ATL',
                'statut' => 'termine',
                'notes_porteur' => 'Paiement effectué',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $user->id,
                'institution_id' => $institution->id,
                'project_id' => $project->id,
                'date_heure' => Carbon::now()->subDays(5),
                'objet' => 'Rencontre imprevue',
                'lieu' => 'Cocody',
                'statut' => 'annule',
                'notes_porteur' => 'Annulé par l\'institution',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rdvData as $data) {
            RendezVous::create($data);
        }

        echo "Rendez-vous créés avec succès!\n";

        // Create Remboursements with different statuses
        if ($funding) {
            $remboursementData = [
                [
                    'project_id' => $funding->project_id,
                    'institution_id' => $funding->institution_id,
                    'montant_total' => 500000,
                    'montant_restant' => 0,
                    'date_echeance' => Carbon::now()->subMonths(3),
                    'date_paiement' => Carbon::now()->subMonths(3)->addDays(2),
                    'statut' => 'paye',
                    'methode_paiement' => 'mobile_money',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $funding->project_id,
                    'institution_id' => $funding->institution_id,
                    'montant_total' => 500000,
                    'montant_restant' => 500000,
                    'date_echeance' => Carbon::now()->addDays(15),
                    'statut' => 'en_attente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $funding->project_id,
                    'institution_id' => $funding->institution_id,
                    'montant_total' => 500000,
                    'montant_restant' => 500000,
                    'date_echeance' => Carbon::now()->subDays(10),
                    'statut' => 'en_retard',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $funding->project_id,
                    'institution_id' => $funding->institution_id,
                    'montant_total' => 750000,
                    'montant_restant' => 0,
                    'date_echeance' => Carbon::now()->subMonths(1),
                    'date_paiement' => Carbon::now()->subMonths(1)->subDays(1),
                    'statut' => 'paye',
                    'methode_paiement' => 'virement',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'project_id' => $funding->project_id,
                    'institution_id' => $funding->institution_id,
                    'montant_total' => 300000,
                    'montant_restant' => 300000,
                    'date_echeance' => Carbon::now()->addDays(30),
                    'statut' => 'en_attente',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($remboursementData as $data) {
                Repayment::create($data);
            }

            echo "Remboursements créés avec succès!\n";
        } else {
            echo "No funding found, skipping remboursements creation\n";
        }

        echo "\nTotal rendez-vous: ".RendezVous::count()."\n";
        echo 'Total remboursements: '.Repayment::count()."\n";
    }
}
