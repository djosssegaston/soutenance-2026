<?php

namespace Database\Seeders;

use App\Models\Funding;
use App\Models\Repayment;
use App\Models\RepaymentConfirmation;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class RemboursementsSeeder extends Seeder
{
    public function run(): void
    {
        $financements = Funding::with('project')->get();

        foreach ($financements as $financement) {
            $months = random_int(6, 10);
            $total = (int) round($financement->montant * 1.12);
            $monthly = (int) ceil($total / $months);
            $paidMonths = random_int(1, max(1, $months - 2));

            for ($i = 1; $i <= $months; $i++) {
                $dueDate = Carbon::parse($financement->date_financement)->addMonths($i);
                $status = 'en_attente';
                $paidAt = null;

                if ($i <= $paidMonths) {
                    $status = 'paye';
                    $paidAt = $dueDate->copy()->subDays(random_int(0, 5));
                } elseif ($dueDate->isPast()) {
                    $status = 'en_retard';
                }

                $repayment = Repayment::factory()->create([
                    'project_id' => $financement->project_id,
                    'montant_total' => $monthly,
                    'date_echeance' => $dueDate,
                    'date_paiement' => $paidAt,
                    'statut' => $status,
                ]);

                if ($status === 'paye') {
                    RepaymentConfirmation::factory()->create([
                        'remboursement_id' => $repayment->id,
                        'institution_id' => $financement->institution_id,
                        'date_confirmation' => $paidAt ? $paidAt->copy()->addDays(random_int(1, 3)) : null,
                    ]);
                }
            }
        }
    }
}
