<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    private array $skipPaths = [
        'api/v1/auth',
        'api/v1/contact',
        'api/v1/locations',
        'api/v1/check-uniqueness',
        'admin/audit-logs',
        'admin/export',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($this->shouldAudit($request) && $request->user()) {
            $method = $request->method();
            $path = $request->path();

            $action = match (true) {
                $method === 'GET' => "Consultation: {$path}",
                $method === 'POST' => "Création: {$path}",
                $method === 'PUT' || $method === 'PATCH' => "Modification: {$path}",
                $method === 'DELETE' => "Suppression: {$path}",
                default => "Action: {$path}",
            };

            $type = match (true) {
                str_contains($path, 'delete') || str_contains($path, 'suspend') => 'critical',
                str_contains($path, 'validate') || str_contains($path, 'reject') || str_contains($path, 'settings') || str_contains($path, 'security') => 'warning',
                $method !== 'GET' => 'info',
                default => 'info',
            };

            $color = match ($type) {
                'critical' => 'danger',
                'warning' => 'warning',
                default => 'info',
            };

            $icon = match (true) {
                $method === 'GET' => 'bi-eye',
                $method === 'POST' => 'bi-plus-circle',
                $method === 'PUT' || $method === 'PATCH' => 'bi-pencil',
                $method === 'DELETE' => 'bi-trash',
                default => 'bi-activity',
            };

            AuditLog::log($request->user()->id, $action, $icon, $color, $type);
        }

        return $response;
    }

    private function shouldAudit(Request $request): bool
    {
        $path = $request->path();

        foreach ($this->skipPaths as $skip) {
            if (str_starts_with($path, $skip)) {
                return false;
            }
        }

        return $request->is('api/v1/admin/*');
    }
}
