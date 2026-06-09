<?php

use App\Http\Controllers\Api\AdminValidationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\FundingController;
use App\Http\Controllers\Api\InstitutionAnalysisController;
use App\Http\Controllers\Api\InstitutionController;
use App\Http\Controllers\Api\InstitutionDashboardController;
use App\Http\Controllers\Api\InstitutionFundingController;
use App\Http\Controllers\Api\InstitutionMessageController;
use App\Http\Controllers\Api\InstitutionNotificationController;
use App\Http\Controllers\Api\InstitutionProfileController;
use App\Http\Controllers\Api\InstitutionProjectController;
use App\Http\Controllers\Api\InstitutionRepaymentController;
use App\Http\Controllers\Api\InstitutionSecurityController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PorteurFinancingController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ProjectAnalysisController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\RendezVousController;
use App\Http\Controllers\Api\RepaymentController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/resend-verification', [AuthController::class, 'resendVerification'])->middleware('throttle:3,1');
    Route::get('auth/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify.api');
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('contact', [ContactController::class, 'send']);

    // Public location endpoints (no auth required)
    Route::prefix('locations')->group(function () {
        Route::get('pays', [LocationController::class, 'pays']);
        Route::get('pays/{pay}/departements', [LocationController::class, 'departements']);
        Route::get('departements/{departement}/communes', [LocationController::class, 'communes']);
        Route::get('communes/{commune}/arrondissements', [LocationController::class, 'arrondissements']);
        Route::get('arrondissements/{arrondissement}/quartiers', [LocationController::class, 'quartiers']);
    });

    // Public uniqueness check for registration form
    Route::get('check-uniqueness', function (\Illuminate\Http\Request $request) {
        $field = $request->query('field');
        $value = $request->query('value');

        $allowed = ['email', 'telephone', 'name', 'prenom'];
        if (! in_array($field, $allowed) || empty($value)) {
            return response()->json(['exists' => false]);
        }

        $exists = \App\Models\User::where($field, $value)->exists();

        return response()->json(['exists' => $exists]);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);

        // Profile routes
        Route::get('profile', [ProfileController::class, 'show']);
        Route::put('profile/update', [ProfileController::class, 'update']);
        Route::put('profile/password', [ProfileController::class, 'updatePassword']);
        Route::post('profile/avatar', [ProfileController::class, 'uploadAvatar']);

        Route::apiResource('users', UserController::class)->middleware('role:admin');
        Route::apiResource('institutions', InstitutionController::class)->middleware('role:admin');

        Route::apiResource('projects', ProjectController::class);
        Route::post('projects/{project}/submit', [ProjectController::class, 'submit']);

        Route::post('projects/{project}/admin-validate', [AdminValidationController::class, 'store'])->middleware('role:admin');
        Route::post('projects/{project}/institution-analyze', [InstitutionAnalysisController::class, 'store'])->middleware('role:institution');
        Route::post('projects/{project}/institution-accept', [InstitutionAnalysisController::class, 'accept'])->middleware('role:institution');

        Route::post('projects/{project}/financements', [FundingController::class, 'store'])->middleware('role:institution');
        Route::post('projects/{project}/repayments', [RepaymentController::class, 'store'])->middleware('role:institution');

        // Nouvelles routes Institution Projets
        Route::prefix('institution')->middleware('role:institution')->group(function () {
            // Découverte
            Route::get('projets', [InstitutionProjectController::class, 'index']);
            Route::get('projets/statistiques', [InstitutionProjectController::class, 'stats']);
            Route::get('projets/filtres', [InstitutionProjectController::class, 'filters']);
            Route::get('projets/{id}', [InstitutionProjectController::class, 'show']);
            Route::post('projets/{id}/analyse', [InstitutionProjectController::class, 'analyze']);

            // Analyses métier
            Route::get('analyses', [ProjectAnalysisController::class, 'index']);
            Route::get('analyses/stats', [ProjectAnalysisController::class, 'stats']);
            Route::get('analyses/{id}', [ProjectAnalysisController::class, 'show']);
            Route::post('analyses/{id}/approve', [ProjectAnalysisController::class, 'approve']);
            Route::post('analyses/{id}/reject', [ProjectAnalysisController::class, 'reject']);
            Route::post('analyses/{id}/request-info', [ProjectAnalysisController::class, 'requestInfo']);
            Route::post('analyses/{id}/schedule-interview', [ProjectAnalysisController::class, 'scheduleInterview']);

            // Entretiens
            Route::get('entretiens', [InterviewController::class, 'index']);
            Route::get('entretiens/stats', [InterviewController::class, 'stats']);
            Route::get('entretiens/{id}', [InterviewController::class, 'show']);
            Route::post('entretiens', [InterviewController::class, 'store']);
            Route::post('entretiens/{id}/confirm', [InterviewController::class, 'confirm']);
            Route::post('entretiens/{id}/cancel', [InterviewController::class, 'cancel']);
            Route::post('entretiens/{id}/report', [InterviewController::class, 'report']);

            // Remboursements Monitoring
            Route::get('remboursements', [InstitutionRepaymentController::class, 'index']);
            Route::get('remboursements/statistiques', [InstitutionRepaymentController::class, 'stats']);
            Route::get('remboursements/projects', [InstitutionRepaymentController::class, 'projects']);
            Route::get('remboursements/project/{id}', [InstitutionRepaymentController::class, 'projectEcheances']);
            Route::get('remboursements/{id}', [InstitutionRepaymentController::class, 'show']);
            Route::post('remboursements/{id}/validate', [InstitutionRepaymentController::class, 'validateRepayment']);
            Route::post('remboursements/{id}/litige', [InstitutionRepaymentController::class, 'openDispute']);

            // Financements
            Route::get('financements', [InstitutionFundingController::class, 'index']);
            Route::get('financements/statistiques', [InstitutionFundingController::class, 'stats']);
            Route::get('financements/{id}', [InstitutionFundingController::class, 'show']);
            Route::get('financements/{id}/echeances', [InstitutionFundingController::class, 'echeances']);
            Route::post('financements', [InstitutionFundingController::class, 'store']);
            Route::post('financements/{id}/decaisser', [InstitutionFundingController::class, 'decaisser']);
            Route::post('financements/{id}/approuver-plan', [InstitutionFundingController::class, 'approvePlan']);
            Route::post('financements/{id}/rejeter-plan', [InstitutionFundingController::class, 'rejectPlan']);
            Route::post('financements/{id}/demander-revision', [InstitutionFundingController::class, 'requestRevision']);

            // Notifications
            Route::get('notifications', [InstitutionNotificationController::class, 'index']);
            Route::post('notifications/{id}/read', [InstitutionNotificationController::class, 'markAsRead']);
            Route::post('notifications/read-all', [InstitutionNotificationController::class, 'markAllRead']);
            Route::post('notifications/{id}/archive', [InstitutionNotificationController::class, 'archive']);
            Route::delete('notifications/{id}', [InstitutionNotificationController::class, 'destroy']);

            // Profil
            Route::get('profile', [InstitutionProfileController::class, 'show']);
            Route::post('profile/update', [InstitutionProfileController::class, 'update']);
            Route::post('profile/photo', [InstitutionProfileController::class, 'uploadLogo']);
            Route::get('profile/history', [InstitutionProfileController::class, 'history']);

            // Sécurité
            Route::get('security', [InstitutionSecurityController::class, 'index']);
            Route::post('security/password', [InstitutionSecurityController::class, 'updatePassword']);
            Route::get('security/password-history', [InstitutionSecurityController::class, 'passwordHistory']);
            Route::delete('security/sessions/{id}', [InstitutionSecurityController::class, 'killSession']);

            // Messagerie
            Route::get('messages', [InstitutionMessageController::class, 'index']);
            Route::get('messages/search', [InstitutionMessageController::class, 'search']);
            Route::post('messages/start', [InstitutionMessageController::class, 'start']);
            Route::get('messages/{conversation}', [InstitutionMessageController::class, 'show']);
            Route::post('messages/send', [InstitutionMessageController::class, 'send']);
            Route::post('messages/upload', [InstitutionMessageController::class, 'upload']);
            Route::put('messages/{message}', [InstitutionMessageController::class, 'editMessage']);
            Route::delete('messages/{message}', [InstitutionMessageController::class, 'deleteMessage']);
            Route::post('messages/read', [InstitutionMessageController::class, 'markRead']);

            // Dashboard Analytics (Cockpit)
            Route::get('dashboard', [InstitutionDashboardController::class, 'index']);
            Route::get('dashboard/charts', [InstitutionDashboardController::class, 'charts']);
            Route::get('dashboard/alerts', [InstitutionDashboardController::class, 'alerts']);
            Route::get('dashboard/activity', [InstitutionDashboardController::class, 'activity']);
        });

        // Routes remboursements porteur
        Route::get('porteur/remboursements', [RepaymentController::class, 'index']);
        Route::get('porteur/remboursements/{repayment}', [RepaymentController::class, 'show']);
        Route::post('porteur/remboursements/{repayment}/payer', [RepaymentController::class, 'initiatePayment']);
        Route::get('porteur/remboursements/historique', [RepaymentController::class, 'history']);

        // Routes porteur financement workflow
        Route::prefix('porteur/financements')->group(function () {
            Route::get('/', [PorteurFinancingController::class, 'mesFinancements']);
            Route::get('propositions', [PorteurFinancingController::class, 'propositions']);
            Route::get('{funding}', [PorteurFinancingController::class, 'detailFinancement']);
            Route::get('{funding}/echeances', [PorteurFinancingController::class, 'echeances']);
            Route::post('{funding}/soumettre-plan', [PorteurFinancingController::class, 'soumettrePlan']);
        });

        Route::prefix('porteur/echeances')->group(function () {
            Route::post('{echeance}/payer', [PorteurFinancingController::class, 'effectuerPaiement']);
            Route::post('{echeance}/fedapay', [PorteurFinancingController::class, 'initierPaiementFedaPay']);
        });

        Route::get('conversations', [ConversationController::class, 'index']);
        Route::get('conversations/{conversation}/messages', [ConversationController::class, 'messages']);
        Route::post('messages', [MessageController::class, 'store']);
        Route::put('messages/{message}', [MessageController::class, 'update']);
        Route::delete('messages/{message}', [MessageController::class, 'destroy']);
        Route::post('messages/{message}/read', [MessageController::class, 'markAsRead']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{notification}/read', [NotificationController::class, 'markRead']);
        Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);
        Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('notifications/{notification}/archive', [NotificationController::class, 'archive']);
        Route::post('notifications/{notification}/restore', [NotificationController::class, 'restore']);
        Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);

        // Dashboard routes
        Route::prefix('dashboard')->group(function () {
            // Admin dashboard routes
            Route::get('admin/stats', [DashboardController::class, 'getAdminStats'])->middleware('role:admin');
            Route::get('admin/recent-projects', [DashboardController::class, 'getAdminRecentProjects'])->middleware('role:admin');

            // Institution dashboard routes
            Route::get('institution/stats', [DashboardController::class, 'getInstitutionStats'])->middleware('role:institution');
            Route::get('institution/projects', [DashboardController::class, 'getInstitutionProjects'])->middleware('role:institution');
            Route::get('institution/portfolio', [DashboardController::class, 'getInstitutionPortfolio'])->middleware('role:institution');

            // Porteur dashboard routes
            Route::get('porteur/kpis', [DashboardController::class, 'getPorteurKpis']);
            Route::get('porteur/financing-stats', [DashboardController::class, 'getPorteurFinancingStats']);
            Route::get('porteur/projects', [DashboardController::class, 'getPorteurProjects']);
            Route::get('porteur/next-repayment', [DashboardController::class, 'getNextRepayment']);
            Route::get('porteur/repayment-history', [DashboardController::class, 'getRepaymentHistory']);
            Route::get('porteur/activities', [DashboardController::class, 'getRecentActivities']);

            // Routes communes
            Route::get('counts', [DashboardController::class, 'getCounts']);
        });

        // Porteur routes - institutions list (accessible by all authenticated users)
        Route::get('porteur/institutions', [\App\Http\Controllers\Api\InstitutionController::class, 'index']);
        Route::get('porteur/projects-list', [\App\Http\Controllers\Api\ProjectController::class, 'porteurProjectsList']);

        // Repayment receipts and simulation
        Route::prefix('repayments')->group(function () {
            Route::get('receipt/{repayment}', [\App\Http\Controllers\Api\RepaymentReceiptController::class, 'generateReceipt']);
            Route::get('late/porteur', [\App\Http\Controllers\Api\RepaymentReceiptController::class, 'porteurLateRepayments']);
            Route::get('late/institution', [\App\Http\Controllers\Api\RepaymentReceiptController::class, 'institutionLateRepayments'])->middleware('role:institution');
            Route::post('simulate', [\App\Http\Controllers\Api\RepaymentReceiptController::class, 'simulateRepayment']);
        });

        // Two-Factor Authentication
        Route::prefix('auth/2fa')->group(function () {
            Route::get('status', [\App\Http\Controllers\Api\TwoFactorAuthController::class, 'status']);
            Route::post('enable', [\App\Http\Controllers\Api\TwoFactorAuthController::class, 'enable']);
            Route::post('confirm', [\App\Http\Controllers\Api\TwoFactorAuthController::class, 'confirm']);
            Route::post('disable', [\App\Http\Controllers\Api\TwoFactorAuthController::class, 'disable']);
            Route::post('verify', [\App\Http\Controllers\Api\TwoFactorAuthController::class, 'verify']);
            Route::post('recovery-codes', [\App\Http\Controllers\Api\TwoFactorAuthController::class, 'recoveryCodes']);
        });

        // Rendez-vous routes
        Route::apiResource('rendez-vous', RendezVousController::class)->except(['show']);
        Route::get('rendez-vous/{id}', [RendezVousController::class, 'show']);
        Route::post('rendez-vous/{id}/accept', [RendezVousController::class, 'accept']);
        Route::post('rendez-vous/{id}/reject', [RendezVousController::class, 'reject']);

        // Admin Dashboard API
        Route::prefix('admin/dashboard')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminDashboardController::class, 'index']);
            Route::get('charts', [\App\Http\Controllers\Api\AdminDashboardController::class, 'charts']);
            Route::get('alerts', [\App\Http\Controllers\Api\AdminDashboardController::class, 'alerts']);
            Route::get('activity', [\App\Http\Controllers\Api\AdminDashboardController::class, 'activity']);
            Route::get('system', [\App\Http\Controllers\Api\AdminDashboardController::class, 'system']);
            Route::get('recent-projects', [\App\Http\Controllers\Api\AdminDashboardController::class, 'recentProjects']);
        });

        // Admin Projects Supervision
        Route::prefix('admin/projects')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminProjectController::class, 'index']);
            Route::get('statistics', [\App\Http\Controllers\Api\AdminProjectController::class, 'statistics']);
            Route::get('alerts', [\App\Http\Controllers\Api\AdminProjectController::class, 'alerts']);
            Route::get('{id}', [\App\Http\Controllers\Api\AdminProjectController::class, 'show']);
            Route::post('{id}/validate', [\App\Http\Controllers\Api\AdminProjectController::class, 'validateProject']);
            Route::post('{id}/reject', [\App\Http\Controllers\Api\AdminProjectController::class, 'rejectProject']);
            Route::post('{id}/suspend', [\App\Http\Controllers\Api\AdminProjectController::class, 'suspendProject']);
        });

        // Admin Echeances
        Route::prefix('admin/echeances')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminEcheanceController::class, 'index']);
            Route::get('projects', [\App\Http\Controllers\Api\AdminEcheanceController::class, 'projects']);
            Route::get('project/{id}', [\App\Http\Controllers\Api\AdminEcheanceController::class, 'projectEcheances']);
        });

        // Admin Remboursements
        Route::prefix('admin/remboursements')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminRepaymentController::class, 'index']);
            Route::get('table', [\App\Http\Controllers\Api\AdminFinanceController::class, 'repaymentTable']);
        });

        // Admin Finance (vue financière globale)
        Route::prefix('admin/finance')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('overview', [\App\Http\Controllers\Api\AdminFinanceController::class, 'overview']);
            Route::get('fundings', [\App\Http\Controllers\Api\AdminFinanceController::class, 'fundingTable']);
        });

        // Admin Export
        Route::prefix('admin/export')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('csv', [\App\Http\Controllers\Api\ExportController::class, 'exportCsv']);
            Route::get('json', [\App\Http\Controllers\Api\ExportController::class, 'exportJson']);
        });

        // Admin Financements
        Route::prefix('admin/financements')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminFinancementController::class, 'index']);
            Route::get('statistics', [\App\Http\Controllers\Api\AdminFinancementController::class, 'statistics']);
            Route::get('{id}', [\App\Http\Controllers\Api\AdminFinancementController::class, 'show'])->whereNumber('id');
        });

        // Admin Audit Logs
        Route::prefix('admin/audit-logs')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminAuditLogController::class, 'index']);
        });

        // Admin Users
        Route::prefix('admin/users')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('statistics', [\App\Http\Controllers\Api\AdminUserController::class, 'statistics']);
            Route::get('/', [\App\Http\Controllers\Api\AdminUserController::class, 'index']);
            Route::get('{id}', [\App\Http\Controllers\Api\AdminUserController::class, 'show']);
            Route::post('{id}/{action}', [\App\Http\Controllers\Api\AdminUserController::class, 'action']);
        });

        // Admin Messages
        Route::prefix('admin/messages')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('conversations', [\App\Http\Controllers\Api\AdminMessageController::class, 'conversations']);
            Route::post('start', [\App\Http\Controllers\Api\AdminMessageController::class, 'start']);
            Route::get('{id}', [\App\Http\Controllers\Api\AdminMessageController::class, 'messages']);
            Route::post('{id}/reply', [\App\Http\Controllers\Api\AdminMessageController::class, 'reply']);
            Route::post('{id}/archive', [\App\Http\Controllers\Api\AdminMessageController::class, 'archive']);
            Route::put('message/{message}', [\App\Http\Controllers\Api\AdminMessageController::class, 'editMessage']);
            Route::delete('message/{message}', [\App\Http\Controllers\Api\AdminMessageController::class, 'deleteMessage']);
        });

        // Admin Notifications
        Route::prefix('admin/notifications')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\NotificationController::class, 'adminIndex']);
            Route::post('mark-all-read', [\App\Http\Controllers\Api\NotificationController::class, 'adminMarkAllRead']);
            Route::post('{notification}/read', [\App\Http\Controllers\Api\NotificationController::class, 'adminMarkRead']);
            Route::post('{notification}/archive', [\App\Http\Controllers\Api\NotificationController::class, 'adminArchive']);
            Route::delete('{notification}', [\App\Http\Controllers\Api\NotificationController::class, 'adminDestroy']);
        });

        // Admin Security
        Route::prefix('admin/security')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminSecurityController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\AdminSecurityController::class, 'update']);
        });

        // Admin Settings
        Route::prefix('admin/settings')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminSettingsController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\AdminSettingsController::class, 'update']);
        });

        // Admin Secteurs
        Route::prefix('admin/secteurs')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminSecteurController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\AdminSecteurController::class, 'store']);
            Route::get('/active', [\App\Http\Controllers\Api\AdminSecteurController::class, 'active']);
            Route::get('/{secteur}', [\App\Http\Controllers\Api\AdminSecteurController::class, 'show']);
            Route::put('/{secteur}', [\App\Http\Controllers\Api\AdminSecteurController::class, 'update']);
            Route::delete('/{secteur}', [\App\Http\Controllers\Api\AdminSecteurController::class, 'destroy']);
        });

        // Admin Document Rules
        Route::prefix('admin/document-rules')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminDocumentRuleController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\AdminDocumentRuleController::class, 'store']);
            Route::get('/{documentRule}', [\App\Http\Controllers\Api\AdminDocumentRuleController::class, 'show']);
            Route::put('/{documentRule}', [\App\Http\Controllers\Api\AdminDocumentRuleController::class, 'update']);
            Route::delete('/{documentRule}', [\App\Http\Controllers\Api\AdminDocumentRuleController::class, 'destroy']);
        });

        // Admin Documents (validation workflow)
        Route::prefix('admin/documents')->middleware(['role:admin', 'audit'])->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\AdminDocumentController::class, 'index']);
            Route::post('{id}/validate', [\App\Http\Controllers\Api\AdminDocumentController::class, 'validateDocument']);
            Route::post('{id}/reject', [\App\Http\Controllers\Api\AdminDocumentController::class, 'rejectDocument']);
            Route::get('checklists', [\App\Http\Controllers\Api\AdminDocumentController::class, 'checklists']);
            Route::get('project/{projectId}/checklist', [\App\Http\Controllers\Api\AdminDocumentController::class, 'projectChecklist']);
        });
    });
});

// Public sector/document rules (any authenticated role)
Route::middleware(['auth'])->group(function () {
    Route::get('secteurs/active', [\App\Http\Controllers\Api\AdminSecteurController::class, 'active']);
    Route::get('document-rules/porteur', function () {
        return response()->json([
            'success' => true,
            'rules' => \App\Models\DocumentRule::active()->forContext('project')->forRole('porteur')->ordered()->get(),
        ]);
    });

    Route::get('document-rules/registration', function (\Illuminate\Http\Request $request) {
        $role = $request->query('acteur', 'porteur');

        return response()->json([
            'success' => true,
            'rules' => \App\Models\DocumentRule::active()->forContext('registration')->forRole($role)->ordered()->get(),
        ]);
    });
});

// Non-versioned API routes for frontend AJAX calls
Route::middleware(['auth'])->group(function () {
    Route::post('projects/{id}/submit', [ProjectController::class, 'submitDraft']);
    Route::delete('projects/{project}', [ProjectController::class, 'destroy']);
    Route::put('projects/{project}', [ProjectController::class, 'update']);
    Route::post('dashboard/admin/projects/{id}/validate', [AdminValidationController::class, 'validate'])->middleware('role:admin');
    Route::post('dashboard/admin/projects/{id}/reject', [AdminValidationController::class, 'reject'])->middleware('role:admin');
});
