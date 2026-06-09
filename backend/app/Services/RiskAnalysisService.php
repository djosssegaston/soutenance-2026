<?php

namespace App\Services;

use App\Models\Project;

class RiskAnalysisService
{
    public function calculateRiskScore(Project $project): int
    {
        $score = 50;

        // Facteur 1: Montant (FCFA)
        if ($project->montant_demande > 5000000) {
            $score += 15;
        } elseif ($project->montant_demande > 2000000) {
            $score += 10;
        } elseif ($project->montant_demande > 1000000) {
            $score += 5;
        } elseif ($project->montant_demande < 500000) {
            $score -= 5;
        }

        // Facteur 2: Secteur (risque climatique inclus pour l'agriculture)
        $secteursRisques = ['agriculture', 'elevage', 'pêche', 'pisciculture'];
        $secteursModeres = ['énergie', 'transport', 'logistique', 'artisanat'];
        $secteursStables = ['services', 'technologie', 'commerce', 'éducation', 'santé'];

        if (in_array(strtolower($project->secteur), $secteursRisques)) {
            $score += 15;
        } elseif (in_array(strtolower($project->secteur), $secteursModeres)) {
            $score -= 5;
        } elseif (in_array(strtolower($project->secteur), $secteursStables)) {
            $score -= 10;
        }

        // Facteur 3: Documents fournis
        $docsCount = $project->documents()->count();
        if ($docsCount >= 4) {
            $score -= 20;
        } elseif ($docsCount >= 2) {
            $score -= 10;
        } elseif ($docsCount === 0) {
            $score += 15;
        }

        // Facteur 4: Genre (femmes meilleures rembourseuses - études PADME/FECECAM)
        if ($project->owner && $project->owner->sexe === 'F') {
            $score -= 5;
        }

        // Facteur 5: Durée du projet
        $mois = $this->parseDureeEnMois($project->duree);
        if ($mois !== null) {
            if ($mois > 24) {
                $score += 10;
            } elseif ($mois > 12) {
                $score += 5;
            } elseif ($mois <= 12) {
                $score -= 5;
            }
        }

        // Facteur 6: Expérience du porteur
        $ownerProjectsCount = Project::where('user_id', $project->user_id)->count();
        if ($ownerProjectsCount > 3) {
            $score -= 15;
        } elseif ($ownerProjectsCount > 1) {
            $score -= 10;
        }

        // Facteur 7: Type de porteur
        if ($project->owner && $project->owner->porteur_type === 'personnel') {
            $score += 5;
        } elseif ($project->owner && $project->owner->porteur_type === 'entreprise') {
            $score -= 5;
        }

        return max(0, min(100, $score));
    }

    public function calculateCredibilityScore(Project $project): int
    {
        $score = 50;

        // Validation admin
        if ($project->validations()->where('decision', 'valide')->exists()) {
            $score += 15;
        }

        // Qualité de la description
        if ($project->description) {
            $len = strlen($project->description);
            if ($len > 500) {
                $score += 15;
            } elseif ($len > 200) {
                $score += 10;
            } else {
                $score += 5;
            }
        }

        // Localisation précise
        if ($project->localisation) {
            $score += 5;
        }

        // Documents financiers
        if ($project->documents()->where('type', 'financier')->exists()) {
            $score += 5;
        }

        // Business plan (document clé pour les IMF)
        if ($project->documents()->where('type', 'business_plan')->exists()) {
            $score += 10;
        }

        // Activité déclarée du porteur
        if ($project->owner && $project->owner->activite) {
            $score += 5;
        }

        // Pièce d'identité fournie
        if ($project->documents()->where('type', 'identite')->exists()) {
            $score += 5;
        }

        return max(0, min(100, $score));
    }

    public function calculateSolvabilityScore(Project $project): int
    {
        $score = 50;

        // Montant demandé
        if ($project->montant_demande < 1000000) {
            $score += 25;
        } elseif ($project->montant_demande < 5000000) {
            $score += 15;
        } elseif ($project->montant_demande < 10000000) {
            $score += 0;
        } else {
            $score -= 15;
        }

        // Entreprise enregistrée
        if ($project->owner && $project->owner->entreprise_nom) {
            $score += 15;
        }

        // Cohérence secteur d'activité du porteur avec le projet
        if ($project->owner && $project->owner->entreprise_secteur) {
            if (strtolower($project->owner->entreprise_secteur) === strtolower($project->secteur)) {
                $score += 10;
            }
        }

        // Capacité de remboursement mensuelle (proxy montant / durée)
        $mois = $this->parseDureeEnMois($project->duree);
        if ($mois !== null && $mois > 0) {
            $mensualite = $project->montant_demande / $mois;
            if ($mensualite > 1000000) {
                $score -= 10;
            } elseif ($mensualite > 500000) {
                $score -= 0;
            } elseif ($mensualite > 100000) {
                $score += 10;
            } else {
                $score += 15;
            }
        }

        return max(0, min(100, $score));
    }

    public function calculateGlobalNote(int $risk, int $credibility, int $solvability): int
    {
        $inverseRisk = 100 - $risk;
        $note = ($inverseRisk * 0.40) + ($solvability * 0.35) + ($credibility * 0.25);

        return (int) round($note);
    }

    public function getRecommendation(int $globalNote): string
    {
        if ($globalNote >= 80) {
            return 'Financement fortement recommandé. Dossier solide.';
        }
        if ($globalNote >= 65) {
            return 'Financement recommandé sous réserve de garanties complémentaires.';
        }
        if ($globalNote >= 50) {
            return 'Analyse complémentaire requise. Risque modéré.';
        }

        return 'Financement non recommandé. Risque trop élevé.';
    }

    public function getViabilityLabel(int $riskScore): string
    {
        if ($riskScore < 30) {
            return 'Très Élevée';
        }
        if ($riskScore < 50) {
            return 'Élevée';
        }
        if ($riskScore < 75) {
            return 'Moyenne';
        }

        return 'Faible';
    }

    public function detectAnomalies(Project $project): array
    {
        $anomalies = [];

        if ($project->montant_demande <= 0) {
            $anomalies[] = 'Montant demandé invalide';
        }

        if (empty($project->description)) {
            $anomalies[] = 'Description manquante';
        }

        if ($project->documents()->count() === 0) {
            $anomalies[] = 'Aucun document justificatif';
        }

        return $anomalies;
    }

    private function parseDureeEnMois(?string $duree): ?int
    {
        if ($duree === null || $duree === '') {
            return null;
        }

        if (preg_match('/^(\d+)\s*mois/i', $duree, $matches)) {
            return (int) $matches[1];
        }

        if (preg_match('/^(\d+)\s*an/i', $duree, $matches)) {
            return (int) $matches[1] * 12;
        }

        return null;
    }
}
