<?php

namespace App\Services;

use App\Models\Interview;
use App\Models\InterviewHistory;
use Illuminate\Support\Str;

class InterviewService
{
    /**
     * Crée un nouvel entretien avec validation métier
     */
    public function createInterview(array $data, $author)
    {
        $interview = Interview::create($data);

        InterviewHistory::create([
            'interview_id' => $interview->id,
            'action' => 'creation',
            'auteur' => $author,
            'details' => "Entretien programmé le {$interview->date_entretien} à {$interview->heure_entretien}.",
        ]);

        return $interview;
    }

    /**
     * Génère une référence unique pour l'entretien
     */
    public function generateReference()
    {
        return 'INT-'.date('Ymd').'-'.strtoupper(Str::random(4));
    }

    /**
     * Met à jour le statut et enregistre l'historique
     */
    public function updateStatus(Interview $interview, string $status, string $author, string $details = '')
    {
        $oldStatus = $interview->statut;
        $interview->update(['statut' => $status]);

        InterviewHistory::create([
            'interview_id' => $interview->id,
            'action' => 'status_change',
            'auteur' => $author,
            'details' => "Statut changé de {$oldStatus} à {$status}. {$details}",
        ]);

        return $interview;
    }
}
