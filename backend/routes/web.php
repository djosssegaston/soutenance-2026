<?php

use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\AuthWebController;
use App\Http\Controllers\DashboardActionsController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

require_once base_path('../frontend/includes/path_helpers.php');
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardPagesController;
use App\Http\Controllers\SecureDocumentController;

Route::get('/', function () {
    return redirect()->to(frontend_public_url('index.php'));
});

Route::get('/frontend/asset/{path}', [DashboardPagesController::class, 'serveFrontendAsset'])
    ->where('path', '.*');

Route::get('/frontend/dashboard02/shared/{path}', [DashboardPagesController::class, 'serveSharedDashboard02'])
    ->where('path', '.*');

// Page d'accès rapide aux dashboards
Route::get('/dashboards', function () {
    return view('auth.access-dashboards');
})->name('dashboards');

Route::any('/frontend/public/{path}', function (string $path) {
    $base = base_path('../frontend/public');
    $fullPath = realpath($base.DIRECTORY_SEPARATOR.$path);

    if ($fullPath === false || strpos($fullPath, realpath($base)) !== 0) {
        abort(404);
    }

    if (! file_exists($fullPath) || is_dir($fullPath)) {
        abort(404);
    }

    $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        return view('frontend.raw', [
            'template' => $fullPath,
            'data' => [
                'script_name' => '/frontend/public/'.$path,
                'csrf_token' => csrf_token(),
                'user' => auth()->user(),
                'apiLoginUrl' => url('api/v1/auth/login'),
                'apiResendUrl' => url('api/v1/auth/resend-verification'),
            ],
        ]);
    }

    $mimeMap = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'mjs' => 'application/javascript',
        'json' => 'application/json',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        'webp' => 'image/webp',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
        'eot' => 'application/vnd.ms-fontobject',
        'map' => 'application/json',
    ];

    $headers = [];
    if (isset($mimeMap[$ext])) {
        $headers['Content-Type'] = $mimeMap[$ext];
    }

    return response()->file($fullPath, $headers);
})->where('path', '.*');

Route::any('/frontend/dashboard/{path}', [DashboardPagesController::class, 'legacyRedirect'])
    ->where('path', '.*');

// Routes publiques pour les pages dashboard/ (sans auth)
Route::get('/dashboard/admin/public', function () {
    return view('dashboard.dashboard_admin', [
        'no_auth' => true,
        'admin_kpis' => [
            ['icon' => 'bi-folder2', 'title' => 'Projets Totaux', 'value' => '0', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(0, 196, 134, 0.15)', 'icon_color' => 'var(--primary-color-1)'],
            ['icon' => 'bi-cash-coin', 'title' => 'Volume Finance', 'value' => '0 FCFA', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(252, 160, 40, 0.18)', 'icon_color' => 'var(--primary-color-3)'],
            ['icon' => 'bi-graph-up-arrow', 'title' => 'Taux Remboursement', 'value' => '0%', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(0, 72, 220, 0.12)', 'icon_color' => 'var(--primary-color-2)'],
            ['icon' => 'bi-exclamation-triangle', 'title' => 'Taux Defaut', 'value' => '0%', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(243, 81, 32, 0.12)', 'icon_color' => 'var(--primary-color-4)'],
        ],
        'recent_projects' => [],
        'sector_breakdown' => [
            ['label' => 'Agriculture', 'percent' => 35, 'color' => '#00C486'],
            ['label' => 'Technologie', 'percent' => 25, 'color' => '#0048DC'],
            ['label' => 'Sante', 'percent' => 20, 'color' => '#FCA028'],
            ['label' => 'Transport', 'percent' => 15, 'color' => '#F35120'],
            ['label' => 'Autre', 'percent' => 5, 'color' => '#6A726F'],
        ],
        'top_institutions' => [],
        'admin_funding_chart' => ['labels' => [], 'financements' => [], 'remboursements' => []],
        'admin_sector_chart' => ['labels' => [], 'values' => []],
    ]);
})->name('dashboard.admin.public');

Route::get('/dashboard/porteur/public', function () {
    return view('dashboard.dashboard_porteur', [
        'no_auth' => true,
        'porteur_kpis' => [
            ['icon' => 'bi-folder2', 'title' => 'Mes Projets', 'value' => '0', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(0, 196, 134, 0.15)', 'icon_color' => 'var(--primary-color-1)'],
            ['icon' => 'bi-cash-coin', 'title' => 'Finance total recu', 'value' => '0 FCFA', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(252, 160, 40, 0.18)', 'icon_color' => 'var(--primary-color-3)'],
            ['icon' => 'bi-graph-up-arrow', 'title' => 'Pourcentage total de remboursement', 'value' => '0%', 'trend' => '--', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-dash', 'icon_bg' => 'rgba(0, 72, 220, 0.12)', 'icon_color' => 'var(--primary-color-2)'],
            ['icon' => 'bi-credit-card-2-front', 'title' => 'Remboursement total effectue', 'value' => '0 FCFA', 'trend' => 'Mensuel', 'trend_class' => 'is-neutral', 'trend_icon' => 'bi-calendar-check', 'icon_bg' => 'rgba(243, 81, 32, 0.12)', 'icon_color' => 'var(--primary-color-4)'],
        ],
        'porteur_projects' => [],
        'next_payment' => ['project' => 'Aucun remboursement', 'date' => 'A definir', 'amount' => '0 FCFA'],
        'activity_timeline' => [],
        'porteur_repayment_chart' => ['labels' => [], 'values' => []],
    ]);
})->name('dashboard.porteur.public');

Route::get('/dashboard/institution/public', function () {
    return view('dashboard.dashboard_institution', [
        'no_auth' => true,
        'institution_kpis' => [
            'portfolio_active' => '0 projets',
            'portfolio_trend' => '0',
            'volume_invested' => '0 FCFA',
            'volume_trend' => '0%',
            'roi_avg' => '0%',
            'roi_trend' => '0%',
            'performance_score' => '0/100',
            'performance_trend' => '0',
        ],
        'available_projects' => [],
        'institution_portfolio' => ['total' => 1, 'on_time_count' => 0, 'late_count' => 0, 'default_count' => 0, 'on_time_percent' => 0, 'late_percent' => 0, 'default_percent' => 0],
        'institution_performance_chart' => ['labels' => [], 'roi' => [], 'risk' => []],
    ]);
})->name('dashboard.institution.public');

Route::get('/email/verify', function () {
    return view('auth.verify-notice');
})->name('verification.notice');

Route::post('/email/verify/resend', function () {
    $user = auth()->user();
    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('dashboard02.file', ['role' => $user->role, 'path' => 'index.php']);
    }

    $user->sendEmailVerificationNotification();

    return back()->with('status', 'verification-link-sent');
})->name('verification.send');

Route::get('/email/verify/{id}/{hash}', function (int $id, string $hash) {
    $user = \App\Models\User::find($id);
    if (! $user) {
        abort(404);
    }

    if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
        abort(403);
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return redirect()->route('login', ['verified' => 1]);
})->middleware('signed')->name('verification.verify');

Route::get('/backend/public/login', function () {
    return redirect()->route('login');
});

Route::get('/login/institution', [AuthWebController::class, 'showInstitutionLogin'])->name('login.institution');
Route::get('/login/porteur', [AuthWebController::class, 'showPorteurLogin'])->name('login.porteur');
Route::get('/login', function () {
    return redirect()->route('login.porteur');
})->name('login');
Route::post('/login', [AuthWebController::class, 'login'])->middleware('throttle:5,1')->name('login.perform');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

// Registration
Route::get('/register', [\App\Http\Controllers\RegisterController::class, 'showForm'])->name('register');
Route::post('/register', [\App\Http\Controllers\RegisterController::class, 'register'])
    ->middleware('throttle:3,1')
    ->name('register.perform');

// Institution registration
Route::get('/register/institution', [\App\Http\Controllers\RegisterInstitutionController::class, 'showForm'])->name('register.institution');
Route::post('/register/institution', [\App\Http\Controllers\RegisterInstitutionController::class, 'register'])
    ->middleware('throttle:3,1')
    ->name('register.institution.perform');

// OTP Mobile
Route::post('/otp/send', [\App\Http\Controllers\OtpController::class, 'send'])
    ->middleware('throttle:3,1')
    ->name('otp.send');
Route::post('/otp/verify', [\App\Http\Controllers\OtpController::class, 'verify'])
    ->name('otp.verify');

// Social Auth
Route::get('/auth/{provider}/redirect', [\App\Http\Controllers\SocialAuthController::class, 'redirect'])
    ->where('provider', 'google|facebook|twitter-oauth-2')
    ->name('social.redirect');
Route::get('/auth/{provider}/callback', [\App\Http\Controllers\SocialAuthController::class, 'callback'])
    ->where('provider', 'google|facebook|twitter-oauth-2')
    ->name('social.callback');

// Password Reset
Route::get('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'showForgotForm'])
    ->middleware('guest')->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\PasswordResetController::class, 'sendResetLink'])
    ->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\PasswordResetController::class, 'resetPassword'])
    ->middleware('guest')->name('password.update');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/frontend/dashboard02/{role}/{path?}', [DashboardPagesController::class, 'serveDashboard02'])
        ->where('path', '.*')
        ->name('dashboard02.file');

    Route::get('/dashboard', function () {
        return redirect()->route('dashboard.redirect');
    });

    Route::get('/dashboard/redirect', function () {
        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return match ($user->role) {
            'admin' => redirect()->route('dashboard02.file', ['role' => 'admin', 'path' => 'index.php']),
            'institution' => redirect()->route('dashboard02.file', ['role' => 'institution', 'path' => 'index.php']),
            default => redirect()->route('dashboard02.file', ['role' => 'porteur', 'path' => 'index.php']),
        };
    })->name('dashboard.redirect');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard/admin', [DashboardController::class, 'admin'])->name('dashboard.admin');

    Route::post('/dashboard/admin/projects/{project}/validate', [DashboardActionsController::class, 'adminValidate'])->name('admin.projects.validate');
    Route::post('/dashboard/admin/projects/{project}/reject', [DashboardActionsController::class, 'adminReject'])->name('admin.projects.reject');

    Route::get('/dashboard/admin/projets', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'projets_admin')->name('admin.projects');
    Route::get('/dashboard/admin/utilisateurs', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'utilisateurs')->name('admin.users');
    Route::get('/dashboard/admin/finance', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'finance')->name('admin.finance');
    Route::get('/dashboard/admin/litiges', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'litiges')->name('admin.disputes');
    Route::get('/dashboard/admin/audit', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'audit_logs')->name('admin.audit');
    Route::get('/dashboard/admin/messages', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'messages_admin')->name('admin.messages');
    Route::get('/dashboard/admin/notifications', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'notifications_admin')->name('admin.notifications');
    Route::get('/dashboard/admin/profil', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'profil_admin')->name('admin.profile');
    Route::get('/dashboard/admin/securite', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'securite_admin')->name('admin.security');
    Route::get('/dashboard/admin/parametres', [DashboardPagesController::class, 'adminPage'])->defaults('page', 'parametres')->name('admin.settings');
});

Route::middleware(['auth', 'role:porteur'])->group(function () {
    Route::get('/dashboard/porteur', [DashboardController::class, 'porteur'])->name('dashboard.porteur');

    Route::get('/dashboard/porteur/projets', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'mes_projets')->name('porteur.projects');
    Route::get('/dashboard/porteur/remboursements', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'remboursements')->name('porteur.repayments');
    Route::get('/dashboard/porteur/notifications', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'notifications')->name('porteur.notifications');
    Route::get('/dashboard/porteur/messages', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'messages')->name('porteur.messages');
    Route::get('/dashboard/porteur/conversations', [ConversationController::class, 'index'])->name('porteur.conversations');
    Route::get('/dashboard/porteur/conversations/{conversation}/messages', [ConversationController::class, 'messages'])->name('porteur.conversations.messages');
    Route::get('/dashboard/porteur/profil', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'profil')->name('porteur.profile');
    Route::get('/dashboard/porteur/securite', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'securite')->name('porteur.security');
    Route::get('/dashboard/porteur/documents', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'documents')->name('porteur.documents');
    Route::post('/dashboard/porteur/documents/upload', [DashboardActionsController::class, 'uploadDocument'])->name('porteur.documents.upload');
    Route::put('/dashboard/porteur/documents/{document}/replace', [DashboardActionsController::class, 'replaceDocument'])->name('porteur.documents.replace');
    Route::delete('/dashboard/porteur/documents/{document}', [DashboardActionsController::class, 'deleteDocument'])->name('porteur.documents.delete');
    Route::get('/dashboard/porteur/financement', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'financement')->name('porteur.funding');
    Route::get('/dashboard/porteur/echeancier', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'echeancier_remboursement')->name('porteur.schedule');
    Route::match(['get', 'post'], '/dashboard/porteur/projets/creer', [DashboardPagesController::class, 'porteurPage'])->defaults('page', 'creer_projet')->name('porteur.projects.create');
    Route::get('/dashboard/porteur/projets/{project}/details', [DashboardPagesController::class, 'projectDetails'])->name('porteur.projects.details');

    // Actions via session (utilisées par le front) pour éviter le prefixe /api public
    Route::post('/dashboard/porteur/projects/store', [DashboardActionsController::class, 'storeProject'])->name('porteur.projects.store');
    Route::post('/dashboard/porteur/projects/upload-document', [DashboardActionsController::class, 'uploadProjectDocument'])->name('porteur.projects.upload-document');
    Route::post('/dashboard/porteur/projects/{project}/submit', [ProjectController::class, 'submitDraft'])->name('porteur.projects.submit');
    Route::delete('/dashboard/porteur/projects/{project}', [ProjectController::class, 'destroy'])->name('porteur.projects.delete');
    Route::put('/dashboard/porteur/projects/{project}', [ProjectController::class, 'update'])->name('porteur.projects.update');

    // Notification actions (session auth)
    Route::post('/dashboard/porteur/notifications/read-all', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead'])->name('porteur.notifications.read-all');
    Route::post('/dashboard/porteur/notifications/{notification}/read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead'])->name('porteur.notifications.read');
    Route::post('/dashboard/porteur/notifications/{notification}/archive', [\App\Http\Controllers\Api\NotificationController::class, 'archive'])->name('porteur.notifications.archive');
    Route::delete('/dashboard/porteur/notifications/{notification}', [\App\Http\Controllers\Api\NotificationController::class, 'destroy'])->name('porteur.notifications.destroy');

    // Funding actions (session auth)
    Route::get('/dashboard/porteur/financement/data', [\App\Http\Controllers\FundingWebController::class, 'index'])->name('porteur.funding.data');
    Route::get('/dashboard/porteur/financement/stats', [\App\Http\Controllers\FundingWebController::class, 'stats'])->name('porteur.funding.stats');
    Route::get('/dashboard/porteur/financement/history', [\App\Http\Controllers\FundingWebController::class, 'history'])->name('porteur.funding.history');
    Route::get('/dashboard/porteur/financement/documents', [\App\Http\Controllers\FundingWebController::class, 'documents'])->name('porteur.funding.documents');
    Route::post('/dashboard/porteur/financement/{funding}/accept', [\App\Http\Controllers\FundingWebController::class, 'accept'])->name('porteur.funding.accept');
    Route::post('/dashboard/porteur/financement/{funding}/refuse', [\App\Http\Controllers\FundingWebController::class, 'refuse'])->name('porteur.funding.refuse');

    // Profile actions (session auth)
    Route::get('/dashboard/porteur/profil/data', [\App\Http\Controllers\ProfileWebController::class, 'show'])->name('porteur.profile.data');
    Route::post('/dashboard/porteur/profil/update', [\App\Http\Controllers\ProfileWebController::class, 'update'])->name('porteur.profile.update');
    Route::post('/dashboard/porteur/profil/email', [\App\Http\Controllers\ProfileWebController::class, 'updateEmail'])->name('porteur.profile.email');
    Route::post('/dashboard/porteur/profil/resend-verification', [\App\Http\Controllers\ProfileWebController::class, 'resendVerification'])->name('porteur.profile.resend-verification');
    Route::post('/dashboard/porteur/profil/verify-telephone', [\App\Http\Controllers\ProfileWebController::class, 'verifyTelephone'])->name('porteur.profile.verify-telephone');
    Route::post('/dashboard/porteur/profil/photo', [\App\Http\Controllers\ProfileWebController::class, 'updatePhoto'])->name('porteur.profile.photo');
    Route::get('/dashboard/porteur/profil/history', [\App\Http\Controllers\ProfileWebController::class, 'profileHistory'])->name('porteur.profile.history');

    // Security actions (session auth)
    Route::post('/dashboard/porteur/securite/password', [\App\Http\Controllers\SecurityController::class, 'updatePassword'])->name('porteur.security.password');
    Route::post('/dashboard/porteur/securite/password-strength', [\App\Http\Controllers\SecurityController::class, 'passwordStrength'])->name('porteur.security.password-strength');
    Route::get('/dashboard/porteur/securite/sessions', [\App\Http\Controllers\SecurityController::class, 'sessions'])->name('porteur.security.sessions');
    Route::delete('/dashboard/porteur/securite/sessions/{session}', [\App\Http\Controllers\SecurityController::class, 'killSession'])->name('porteur.security.sessions.kill');
    Route::get('/dashboard/porteur/securite/audit-logs', [\App\Http\Controllers\SecurityController::class, 'auditLogs'])->name('porteur.security.audit-logs');
    Route::get('/dashboard/porteur/securite/password-history', [\App\Http\Controllers\SecurityController::class, 'passwordHistory'])->name('porteur.security.password-history');
});

Route::middleware(['auth', 'role:institution'])->group(function () {
    Route::get('/dashboard/institution', [DashboardController::class, 'institution'])->name('dashboard.institution');

    Route::post('/dashboard/institution/projects/{project}/analyze', [DashboardActionsController::class, 'institutionAnalyze'])->name('institution.projects.analyze');
    Route::post('/dashboard/institution/projects/{project}/interview', [DashboardActionsController::class, 'institutionInterview'])->name('institution.projects.interview');
    Route::post('/dashboard/institution/projects/{project}/accept', [DashboardActionsController::class, 'institutionAccept'])->name('institution.projects.accept');
    Route::post('/dashboard/institution/projects/{project}/finance', [DashboardActionsController::class, 'institutionFinance'])->name('institution.projects.finance');

    Route::get('/dashboard/institution/projets', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'parcourir_projets')->name('institution.projects');
    Route::get('/dashboard/institution/remboursements', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'remboursements_institution')->name('institution.repayments');
    Route::get('/dashboard/institution/notifications', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'notifications_institution')->name('institution.notifications');
    Route::get('/dashboard/institution/messages', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'messages_institution')->name('institution.messages');
    Route::get('/dashboard/institution/profil', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'profil_institution')->name('institution.profile');
    Route::get('/dashboard/institution/securite', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'securite_institution')->name('institution.security');
    Route::get('/dashboard/institution/portfolio', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'portfolio')->name('institution.portfolio');
    Route::get('/dashboard/institution/analyse-risque', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'analyse_risque')->name('institution.risk');
    Route::get('/dashboard/institution/entretiens', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'entretiens_planifies')->name('institution.interviews');
    Route::get('/dashboard/institution/investissements', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'investissements')->name('institution.investments');
    Route::get('/dashboard/institution/validation', [DashboardPagesController::class, 'institutionPage'])->defaults('page', 'validation')->name('institution.validation');
    Route::get('/dashboard/institution/interviews/{interview}/convocation', [\App\Http\Controllers\Api\InterviewController::class, 'convocation'])->name('institution.interview.convocation');
});

// ===== Secure Document Routes (protégé contre accès direct) =====
// Pas de middleware 'auth' — le contrôleur gère l'authentification en interne
// et retourne toujours du contenu affichable (pas de 302, pas de 403 HTML)
Route::prefix('documents/secure')->name('secure.documents.')->group(function () {
    Route::get('/project/{document}/view', [SecureDocumentController::class, 'viewProjectDocument'])->name('project.view');
    Route::get('/project/{document}/serve', [SecureDocumentController::class, 'serveProjectDocument'])->name('project.serve');
    Route::get('/project/{document}/download', [SecureDocumentController::class, 'downloadProjectDocument'])->name('project.download');
    Route::get('/financement/{document}/view', [SecureDocumentController::class, 'viewFinancementDocument'])->name('financement.view');
    Route::get('/financement/{document}/serve', [SecureDocumentController::class, 'serveFinancementDocument'])->name('financement.serve');
    Route::get('/kyc/{document}/view', [SecureDocumentController::class, 'viewKycDocument'])->name('kyc.view');
    Route::get('/kyc/{document}/serve/{field}', [SecureDocumentController::class, 'serveKycDocument'])->name('kyc.serve');
});

// Serve/download routes (binary file responses) — the controller handles auth internally,
// returning 403 with an error image instead of a 302 HTML redirect that breaks the iframe
Route::prefix('documents/secure')->name('secure.documents.')->group(function () {
    Route::get('/project/{document}/serve', [SecureDocumentController::class, 'serveProjectDocument'])->name('project.serve');
    Route::get('/project/{document}/download', [SecureDocumentController::class, 'downloadProjectDocument'])->name('project.download');
    Route::get('/financement/{document}/serve', [SecureDocumentController::class, 'serveFinancementDocument'])->name('financement.serve');
    Route::get('/kyc/{document}/serve/{field}', [SecureDocumentController::class, 'serveKycDocument'])->name('kyc.serve');
});

// ============================================================================
// Contact Form
Route::post('/contact/send', [\App\Http\Controllers\Api\ContactController::class, 'send'])->name('contact.send');

// FedaPay Payment Routes
// ============================================================================

// Webhook FedaPay (PUBLIC - pas d'authentification)
Route::post('/fedapay/webhook', [PaymentController::class, 'webhook'])->name('fedapay.webhook');

// Routes de paiement (AUTHENTIFIÉ)
Route::middleware(['auth'])->group(function () {
    // Paiement projet
    Route::get('/payment/{project}', [PaymentController::class, 'showPaymentForm'])->name('payment.show');
    Route::post('/payment/{project}', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');

    // Callbacks
    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
    Route::get('/payment/failed', [PaymentController::class, 'paymentFailed'])->name('payment.failed');

    // API vérification statut
    Route::get('/payment/status/{transactionId}', [PaymentController::class, 'checkStatus'])->name('payment.status');
});
