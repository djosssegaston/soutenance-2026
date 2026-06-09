<?php

namespace App\Support;

use App\Enums\ProjectStatus;
use App\Models\Funding;
use App\Models\Institution;
use App\Models\Project;
use App\Models\Repayment;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Collection;

trait DashboardDataHelpers
{
    protected function monthStart(int $offset): Carbon
    {
        return Carbon::now()->subMonths($offset)->startOfMonth();
    }

    protected function monthEnd(int $offset): Carbon
    {
        return Carbon::now()->subMonths($offset)->endOfMonth();
    }

    protected function buildTrend(float $current, float $previous, string $suffix, bool $invert = false): array
    {
        $delta = $current - $previous;
        $label = $suffix === ''
            ? (string) ($delta >= 0 ? '+' : '').round($delta, 1)
            : sprintf('%+0.1f%s', $previous > 0 ? ($delta / $previous) * 100 : 0, $suffix);

        $isUp = $invert ? $delta < 0 : $delta >= 0;

        return [
            'label' => $label,
            'class' => $isUp ? 'is-up' : 'is-down',
            'icon' => $isUp ? 'bi-arrow-up-right' : 'bi-arrow-down-right',
        ];
    }

    protected function formatMoney(int|float|string $amount): string
    {
        return number_format((float) $amount, 0, ',', ' ').' FCFA';
    }

    protected function formatMoneyCompact(int|float|string $amount): string
    {
        $amount = (float) $amount;

        if ($amount >= 1000000000) {
            return round($amount / 1000000000, 1).' Mrd FCFA';
        }
        if ($amount >= 1000000) {
            return round($amount / 1000000, 1).'M FCFA';
        }
        if ($amount >= 1000) {
            return round($amount / 1000, 1).'K FCFA';
        }

        return $amount.' FCFA';
    }

    protected function projectCode(int $id): string
    {
        return 'PRJ-'.str_pad((string) $id, 3, '0', STR_PAD_LEFT);
    }

    protected function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name));
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials ?: '--';
    }

    protected function mapProjectStatus(ProjectStatus|string|null $status): array
    {
        $enum = ProjectStatus::fromStorageOr($status);

        return [
            'label' => $enum->label(),
            'class' => $enum->badgeClass(),
        ];
    }

    protected function mapValidationStatus(string $status): array
    {
        $map = [
            'en_attente' => ['label' => 'en attente', 'class' => 'status-submitted submitted'],
            'en_analyse_admin' => ['label' => 'non analyse', 'class' => 'status-submitted submitted'],
            'valide' => ['label' => 'valide', 'class' => 'status-approved approved'],
            'refuse' => ['label' => 'refuse', 'class' => 'status-rejected rejected'],
        ];

        return $map[$status] ?? ['label' => $status, 'class' => 'status-submitted submitted'];
    }

    protected function mapFundingStatus(string $status): array
    {
        $map = [
            'en_cours_de_traitement' => ['label' => 'en cours de traitement', 'class' => 'status-submitted submitted'],
            'en_analyse' => ['label' => 'en analyse', 'class' => 'status-submitted submitted'],
            'entretien_planifie' => ['label' => 'entretien planifie', 'class' => 'status-approved approved'],
            'accepte' => ['label' => 'accepte', 'class' => 'status-approved approved'],
            'finance' => ['label' => 'deja finance', 'class' => 'status-funded funded'],
            'termine' => ['label' => 'termine', 'class' => 'status-approved approved'],
        ];

        return $map[$status] ?? ['label' => $status, 'class' => 'status-submitted submitted'];
    }

    protected function mapRisk(int $score): array
    {
        if ($score >= 80) {
            return ['label' => 'Faible', 'class' => 'risk-low', 'score_class' => 'mini-progress__bar--success'];
        }
        if ($score >= 60) {
            return ['label' => 'Moyen', 'class' => 'risk-medium', 'score_class' => ''];
        }

        return ['label' => 'Eleve', 'class' => 'risk-high', 'score_class' => 'mini-progress__bar--danger'];
    }

    protected function scoreClass(float $score): string
    {
        if ($score >= 85) {
            return 'bg-success-token';
        }
        if ($score >= 70) {
            return 'bg-warning-token';
        }

        return 'bg-danger-token';
    }

    protected function notificationTitle(string $type): string
    {
        $map = [
            'validation_projet' => 'Projet valide',
            'refus_projet' => 'Projet refuse',
            'financement_recu' => 'Financement recu',
            'entretien_planifie' => 'Entretien planifie',
            'remboursement_confirme' => 'Paiement confirme',
            'litige_cree' => 'Litige ouvert',
            'message' => 'Nouveau message',
        ];

        return $map[$type] ?? 'Notification';
    }

    protected function formatNotifications(Collection $notifications): array
    {
        return $notifications->map(function (UserNotification $notification) {
            return [
                'title' => $this->notificationTitle($notification->type),
                'message' => $notification->content,
                'time' => $notification->created_at ? $notification->created_at->diffForHumans() : 'Maintenant',
                'variant' => $this->notificationVariant($notification->type),
                'read' => (bool) $notification->is_read,
            ];
        })->all();
    }

    protected function formatTimeline(Collection $notifications): array
    {
        return $notifications->map(function (UserNotification $notification) {
            return [
                'title' => $this->notificationTitle($notification->type),
                'message' => $notification->content,
                'time' => $notification->created_at ? $notification->created_at->diffForHumans() : 'A l instant',
                'variant' => $this->notificationVariant($notification->type),
            ];
        })->all();
    }

    protected function notificationVariant(string $type): string
    {
        if (str_contains($type, 'remboursement')) {
            return 'success';
        }
        if (str_contains($type, 'litige')) {
            return 'warning';
        }

        return 'info';
    }

    protected function buildSectorBreakdown(): array
    {
        $totals = Project::selectRaw('secteur, COUNT(*) as total')
            ->whereNotNull('secteur')
            ->groupBy('secteur')
            ->orderByDesc('total')
            ->get();

        $totalProjects = $totals->sum('total');
        $colors = ['#00C486', '#0048DC', '#FCA028', '#F35120', '#6A726F'];

        $breakdown = [];
        $top = $totals->take(4);
        $used = 0;
        foreach ($top as $index => $row) {
            $percent = $totalProjects > 0 ? round(($row->total / $totalProjects) * 100) : 0;
            $used += $percent;
            $breakdown[] = [
                'label' => $row->secteur,
                'percent' => $percent,
                'color' => $colors[$index] ?? '#6A726F',
            ];
        }
        $remaining = max(0, 100 - $used);
        $breakdown[] = [
            'label' => 'Autre',
            'percent' => $remaining,
            'color' => $colors[4],
        ];

        return $breakdown;
    }

    protected function buildFundingChart(): array
    {
        $months = $this->lastMonths(6);
        $labels = $months->map(fn (Carbon $date) => $this->monthShort($date))->all();
        $fundingValues = [];
        $repaymentValues = [];

        foreach ($months as $date) {
            $fundingValues[] = round(Funding::whereBetween('date_financement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])->sum('montant') / 1000000, 1);
            $repaymentValues[] = round(Repayment::whereBetween('date_paiement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])->sum('montant_rembourse') / 1000000, 1);
        }

        return [
            'labels' => $labels,
            'financements' => $fundingValues,
            'remboursements' => $repaymentValues,
        ];
    }

    protected function buildRepaymentChart(Collection $projectIds): array
    {
        $months = $this->lastMonths(6);
        $labels = $months->map(fn (Carbon $date) => $this->monthShort($date))->all();
        $values = [];

        foreach ($months as $date) {
            $values[] = round(Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'paye')
                ->whereBetween('date_paiement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('montant_rembourse') / 1000, 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    protected function buildInstitutionPerformanceChart(Institution $institution): array
    {
        $months = $this->lastMonths(6);
        $labels = $months->map(fn (Carbon $date) => $this->monthShort($date))->all();
        $roiValues = [];
        $riskValues = [];

        $projectIds = Funding::where('institution_id', $institution->id)->pluck('project_id');

        foreach ($months as $date) {
            $funded = Funding::where('institution_id', $institution->id)
                ->whereBetween('date_financement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('montant');
            $paid = Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'paye')
                ->whereBetween('date_paiement', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->sum('montant_rembourse');
            $roiValues[] = $funded > 0 ? round((($paid / $funded) - 1) * 100, 1) : 0;

            $lateCount = Repayment::whereIn('project_id', $projectIds)
                ->where('statut', 'en_retard')
                ->whereBetween('date_echeance', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->count();
            $dueCount = Repayment::whereIn('project_id', $projectIds)
                ->whereBetween('date_echeance', [$date->copy()->startOfMonth(), $date->copy()->endOfMonth()])
                ->count();
            $riskValues[] = $dueCount > 0 ? round(($lateCount / $dueCount) * 100, 1) : 0;
        }

        return [
            'labels' => $labels,
            'roi' => $roiValues,
            'risk' => $riskValues,
        ];
    }

    protected function institutionPerformanceScore(Institution $institution): float
    {
        $projectIds = Funding::where('institution_id', $institution->id)->pluck('project_id');
        $total = Repayment::whereIn('project_id', $projectIds)->count();
        if ($total === 0) {
            return 0;
        }
        $onTime = Repayment::whereIn('project_id', $projectIds)
            ->where('statut', 'paye')
            ->whereColumn('date_paiement', '<=', 'date_echeance')
            ->count();

        return round(($onTime / $total) * 100, 1);
    }

    protected function percent(int $value, int $total): int
    {
        return $total > 0 ? (int) round(($value / $total) * 100) : 0;
    }

    protected function lastMonths(int $count): Collection
    {
        return collect(range($count - 1, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
    }

    protected function monthShort(Carbon $date): string
    {
        $map = [
            1 => 'Jan',
            2 => 'Fev',
            3 => 'Mar',
            4 => 'Avr',
            5 => 'Mai',
            6 => 'Jun',
            7 => 'Juil',
            8 => 'Aou',
            9 => 'Sep',
            10 => 'Oct',
            11 => 'Nov',
            12 => 'Dec',
        ];

        return $map[$date->month] ?? $date->format('M');
    }
}
