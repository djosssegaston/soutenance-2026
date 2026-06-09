<?php

use App\Enums\ProjectStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $map = [
            'brouillon' => ProjectStatus::DRAFT->value,
            'soumis' => ProjectStatus::SUBMITTED->value,
            'en_attente' => ProjectStatus::UNDER_ADMIN_REVIEW->value,
            'en_analyse_admin' => ProjectStatus::UNDER_ADMIN_REVIEW->value,
            'valide' => ProjectStatus::ADMIN_VALIDATED->value,
            'rejete' => ProjectStatus::ADMIN_REJECTED->value,
            'refuse' => ProjectStatus::ADMIN_REJECTED->value,
            'en_analyse_institution' => ProjectStatus::UNDER_INSTITUTION_REVIEW->value,
            'entretien_planifie' => ProjectStatus::INTERVIEW_SCHEDULED->value,
            'entretien_confirme' => ProjectStatus::INTERVIEW_CONFIRMED->value,
            'documents_demandes' => ProjectStatus::DOCUMENTS_REQUESTED->value,
            'accepte' => ProjectStatus::INSTITUTION_ACCEPTED->value,
            'accepte_institution' => ProjectStatus::INSTITUTION_ACCEPTED->value,
            'finance' => ProjectStatus::FUNDED->value,
            'financé' => ProjectStatus::FUNDED->value,
            'en_remboursement' => ProjectStatus::ACTIVE->value,
            'termine' => ProjectStatus::COMPLETED->value,
            'en_defaut' => ProjectStatus::ACTIVE->value,
            'annule' => ProjectStatus::CANCELLED->value,
        ];

        foreach ($map as $legacy => $normalized) {
            DB::table('projects')
                ->where('statut', $legacy)
                ->update(['statut' => $normalized]);
        }

        DB::table('projects')
            ->whereNull('statut')
            ->orWhere('statut', '')
            ->update(['statut' => ProjectStatus::DRAFT->value]);
    }

    public function down(): void
    {
        $reverseMap = [
            ProjectStatus::DRAFT->value => 'brouillon',
            ProjectStatus::SUBMITTED->value => 'soumis',
            ProjectStatus::UNDER_ADMIN_REVIEW->value => 'en_analyse_admin',
            ProjectStatus::ADMIN_VALIDATED->value => 'valide',
            ProjectStatus::ADMIN_REJECTED->value => 'rejete',
            ProjectStatus::UNDER_INSTITUTION_REVIEW->value => 'en_analyse_institution',
            ProjectStatus::INTERVIEW_SCHEDULED->value => 'entretien_planifie',
            ProjectStatus::INTERVIEW_CONFIRMED->value => 'entretien_confirme',
            ProjectStatus::DOCUMENTS_REQUESTED->value => 'documents_demandes',
            ProjectStatus::INSTITUTION_ACCEPTED->value => 'accepte',
            ProjectStatus::FUNDED->value => 'finance',
            ProjectStatus::ACTIVE->value => 'en_remboursement',
            ProjectStatus::COMPLETED->value => 'termine',
            ProjectStatus::CANCELLED->value => 'annule',
        ];

        foreach ($reverseMap as $normalized => $legacy) {
            DB::table('projects')
                ->where('statut', $normalized)
                ->update(['statut' => $legacy]);
        }
    }
};
