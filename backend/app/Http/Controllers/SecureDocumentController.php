<?php

namespace App\Http\Controllers;

use App\Enums\ProjectStatus;
use App\Models\DocumentAccessLog;
use App\Models\FinancementDocument;
use App\Models\Funding;
use App\Models\InstitutionAnalysis;
use App\Models\KycDocument;
use App\Models\ProjectDocument;
use Illuminate\Support\Facades\Storage;

class SecureDocumentController extends Controller
{
    private function checkProjectDocumentAccess(ProjectDocument $document, $user): string
    {
        $project = $document->project;

        if (! $project || ! $user) {
            return 'denied';
        }

        if ($user->id === $project->user_id) {
            return 'owner';
        }

        if ($user->role === 'admin') {
            return 'viewer';
        }

        if ($user->role === 'institution' && $user->institution) {
            $visibleStatuses = ProjectStatus::institutionVisibleValues();

            if (in_array($project->statut, $visibleStatuses, true)) {
                return 'viewer';
            }

            $exists = InstitutionAnalysis::where('project_id', $project->id)
                ->where('institution_id', $user->institution->id)
                ->exists();

            if ($exists) {
                return 'viewer';
            }

            $exists = Funding::where('project_id', $project->id)
                ->where('institution_id', $user->institution->id)
                ->exists();

            if ($exists) {
                return 'viewer';
            }
        }

        return 'denied';
    }

    private function checkFinancementDocumentAccess(FinancementDocument $document, $user): string
    {
        $funding = $document->funding;

        if (! $funding || ! $user) {
            return 'denied';
        }

        $project = $funding->project;

        if (! $project) {
            return 'denied';
        }

        if ($user->id === $project->user_id) {
            return 'owner';
        }

        if ($user->role === 'admin') {
            return 'viewer';
        }

        if ($user->role === 'institution' && $user->institution && $user->institution->id === $funding->institution_id) {
            return 'viewer';
        }

        return 'denied';
    }

    private function checkKycDocumentAccess(KycDocument $document, $user): string
    {
        if (! $user) {
            return 'denied';
        }

        if ($user->id === $document->user_id) {
            return 'owner';
        }

        if ($user->role === 'admin') {
            return 'viewer';
        }

        return 'denied';
    }

    private function logAccess($user, string $documentType, int $documentId, string $action, ?string $reason = null): void
    {
        try {
            DocumentAccessLog::create([
                'user_id' => $user?->id,
                'document_type' => $documentType,
                'document_id' => $documentId,
                'action' => $action,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'reason' => $reason,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log document access: '.$e->getMessage());
        }
    }

    private function buildSecurityHeaders(string $fileName): array
    {
        return [
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
        ];
    }

    private function loginRequiredResponse(): \Illuminate\Http\Response
    {
        $loginUrl = route('login');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head><meta charset="UTF-8"><title>Connexion requise</title>
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:sans-serif;background:#1a1a2e;display:flex;align-items:center;justify-content:center;height:100vh}
.card{background:#fff;border-radius:12px;padding:48px;text-align:center;max-width:420px;box-shadow:0 4px 24px rgba(0,0,0,.15)}
.icon{font-size:56px;margin-bottom:16px}
h2{color:#333;margin-bottom:8px;font-size:22px}
p{color:#777;margin-bottom:24px;font-size:14px;line-height:1.5}
a{display:inline-block;background:#e74c3c;color:#fff;padding:12px 32px;border-radius:6px;text-decoration:none;font-weight:600;font-size:15px}
</style></head>
<body>
<div class="card">
<div class="icon">🔒</div>
<h2>Connexion requise</h2>
<p>Vous devez &ecirc;tre connect&eacute; pour acc&eacute;der &agrave; ce document.</p>
<a href="{$loginUrl}">Se connecter</a>
</div>
</body>
</html>
HTML;

        return response($html, 200, ['Content-Type' => 'text/html; charset=utf-8']);
    }

    private function deniedResponse(): \Illuminate\Http\Response
    {
        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400">
  <rect width="600" height="400" fill="#f5f5f5"/>
  <circle cx="300" cy="140" r="40" fill="#e74c3c" opacity="0.9"/>
  <path d="M290 130 h-8 v-8 a18 18 0 0 1 36 0 v8 h-8 v-8 a10 10 0 0 0-20 0 v8 z" fill="#fff"/>
  <rect x="278" y="128" width="44" height="32" rx="4" fill="#fff"/>
  <text x="300" y="225" text-anchor="middle" font-family="sans-serif" font-size="20" font-weight="600" fill="#555">Document non accessible</text>
  <text x="300" y="255" text-anchor="middle" font-family="sans-serif" font-size="14" fill="#999">Vous n'avez pas les droits n&eacute;cessaires.</text>
</svg>
SVG;

        return response($svg, 200, ['Content-Type' => 'image/svg+xml']);
    }

    // ==================== ProjectDocument ====================

    public function viewProjectDocument(ProjectDocument $document)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->loginRequiredResponse();
        }

        $access = $this->checkProjectDocumentAccess($document, $user);

        if ($access === 'denied') {
            $this->logAccess($user, 'ProjectDocument', $document->id, 'denied', 'Acces refuse');

            return $this->deniedResponse();
        }

        $this->logAccess($user, 'ProjectDocument', $document->id, 'view');

        $fileName = basename($document->fichier);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return view('documents.secure-viewer', [
            'documentId' => $document->id,
            'documentType' => 'project',
            'fileName' => $fileName,
            'ext' => $ext,
            'canDownload' => ($access === 'owner'),
            'isOwner' => ($access === 'owner'),
            'serveUrl' => route('secure.documents.project.serve', $document->id),
            'downloadUrl' => route('secure.documents.project.download', $document->id),
        ]);
    }

    public function serveProjectDocument(ProjectDocument $document)
    {
        $user = auth()->user();
        $access = $this->checkProjectDocumentAccess($document, $user);

        if ($access === 'denied') {
            $this->logAccess($user, 'ProjectDocument', $document->id, 'denied', 'Tentative d\'acces direct au fichier');

            return $this->deniedResponse();
        }

        if (! Storage::disk('local')->exists($document->fichier)) {
            return $this->deniedResponse();
        }

        $this->logAccess($user, 'ProjectDocument', $document->id, 'serve');

        $mime = Storage::disk('local')->mimeType($document->fichier);
        $fileName = basename($document->fichier);

        return Storage::disk('local')->response($document->fichier, $fileName, array_merge([
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ], $this->buildSecurityHeaders($fileName)));
    }

    public function downloadProjectDocument(ProjectDocument $document)
    {
        $user = auth()->user();
        $access = $this->checkProjectDocumentAccess($document, $user);

        if ($access !== 'owner') {
            $this->logAccess($user, 'ProjectDocument', $document->id, 'denied', 'Tentative de telechargement sans droits');
            abort(403, 'Seul le proprietaire du projet peut telecharger ce document.');
        }

        if (! Storage::disk('local')->exists($document->fichier)) {
            abort(404, 'Fichier non trouve.');
        }

        $this->logAccess($user, 'ProjectDocument', $document->id, 'download');

        $fileName = basename($document->fichier);

        return Storage::disk('local')->download($document->fichier, $fileName, [
            'Content-Type' => Storage::disk('local')->mimeType($document->fichier),
        ]);
    }

    // ==================== FinancementDocument ====================

    public function viewFinancementDocument(FinancementDocument $document)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->loginRequiredResponse();
        }

        $access = $this->checkFinancementDocumentAccess($document, $user);

        if ($access === 'denied') {
            $this->logAccess($user, 'FinancementDocument', $document->id, 'denied', 'Acces refuse');

            return $this->deniedResponse();
        }

        $this->logAccess($user, 'FinancementDocument', $document->id, 'view');

        $fileName = basename($document->fichier);
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return view('documents.secure-viewer', [
            'documentId' => $document->id,
            'documentType' => 'financement',
            'fileName' => $fileName,
            'ext' => $ext,
            'canDownload' => ($access === 'owner'),
            'isOwner' => ($access === 'owner'),
            'serveUrl' => route('secure.documents.financement.serve', $document->id),
            'downloadUrl' => '#',
        ]);
    }

    public function serveFinancementDocument(FinancementDocument $document)
    {
        $user = auth()->user();
        $access = $this->checkFinancementDocumentAccess($document, $user);

        if ($access === 'denied') {
            $this->logAccess($user, 'FinancementDocument', $document->id, 'denied', 'Tentative d\'acces direct au fichier');

            return $this->deniedResponse();
        }

        $disk = 'secure_documents';
        if (! Storage::disk($disk)->exists($document->fichier)) {
            $disk = 'local';
            if (! Storage::disk($disk)->exists($document->fichier)) {
                return $this->deniedResponse();
            }
        }

        $this->logAccess($user, 'FinancementDocument', $document->id, 'serve');

        $mime = Storage::disk($disk)->mimeType($document->fichier);
        $fileName = basename($document->fichier);

        return Storage::disk($disk)->response($document->fichier, $fileName, array_merge([
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ], $this->buildSecurityHeaders($fileName)));
    }

    // ==================== KycDocument ====================

    public function viewKycDocument(KycDocument $document)
    {
        $user = auth()->user();

        if (! $user) {
            return $this->loginRequiredResponse();
        }

        $access = $this->checkKycDocumentAccess($document, $user);

        if ($access === 'denied') {
            $this->logAccess($user, 'KycDocument', $document->id, 'denied', 'Acces refuse');

            return $this->deniedResponse();
        }

        $this->logAccess($user, 'KycDocument', $document->id, 'view');

        return view('documents.kyc-viewer', [
            'document' => $document,
            'canView' => true,
            'isOwner' => ($access === 'owner'),
        ]);
    }

    public function serveKycDocument(KycDocument $document, string $field)
    {
        $user = auth()->user();
        $access = $this->checkKycDocumentAccess($document, $user);

        if ($access === 'denied') {
            $this->logAccess($user, 'KycDocument', $document->id, 'denied', 'Tentative d\'acces direct au fichier');

            return $this->deniedResponse();
        }

        $path = match ($field) {
            'recto' => $document->recto_path,
            'verso' => $document->verso_path,
            'selfie' => $document->selfie_path,
            default => null,
        };

        if (! $path) {
            return $this->deniedResponse();
        }

        $disk = 'secure_documents';
        if (! Storage::disk($disk)->exists($path)) {
            $disk = 'public';
            if (! Storage::disk($disk)->exists($path)) {
                return $this->deniedResponse();
            }
        }

        $this->logAccess($user, 'KycDocument', $document->id, 'serve');

        $mime = Storage::disk($disk)->mimeType($path);
        $fileName = $field.'_'.basename($path);

        return Storage::disk($disk)->response($path, $fileName, array_merge([
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="'.$fileName.'"',
        ], $this->buildSecurityHeaders($fileName)));
    }
}
