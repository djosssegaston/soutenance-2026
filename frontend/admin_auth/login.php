<?php
require_once __DIR__ . '/../../includes/path_helpers.php';

$page_title = "Connexion Administrateur - ALOGOTO";
include __DIR__ . '/../public/header.php';
?>

<style>
    .admin-login-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .admin-login-badge i {
        font-size: 16px;
    }
    .admin-login-header {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        color: white;
        padding: 40px;
        border-radius: 20px 20px 0 0;
        text-align: center;
    }
    .admin-login-header h2 {
        color: white;
        margin: 0 0 10px 0;
        font-size: 32px;
    }
    .admin-login-header p {
        margin: 0;
        opacity: 0.9;
    }
    .admin-login-form {
        padding: 40px;
        background: white;
        border-radius: 0 0 20px 20px;
        box-shadow: 0 18px 46px rgba(0, 0, 0, 0.15);
    }
    .admin-security-notice {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 12px 16px;
        margin-bottom: 24px;
        border-radius: 4px;
        font-size: 14px;
    }
    .admin-security-notice i {
        color: #ffc107;
        margin-right: 8px;
    }
</style>

<section class="alogoto-login section-padding" style="background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="admin-login-badge">
                    <i class="bi bi-shield-lock"></i>
                    <span>Accès Administrateur Sécurisé</span>
                </div>

                <div class="admin-login-header">
                    <i class="bi bi-person-lock" style="font-size: 48px; margin-bottom: 16px;"></i>
                    <h2>Administration ALOGOTO</h2>
                    <p>Connectez-vous avec vos identifiants administrateur</p>
                </div>

                <div class="admin-login-form">
                    <div class="admin-security-notice">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Accès réservé:</strong> Cette zone est strictement réservée aux administrateurs de la plateforme.
                    </div>

                    <form id="adminLoginForm" method="POST" action="<?php echo backend_url('login'); ?>">
                        <?php if (function_exists('csrf_field')): ?>
                            <?php echo csrf_field(); ?>
                        <?php endif; ?>
                        
                        <input type="hidden" name="role" value="admin">

                        <div class="mb-3">
                            <label for="adminEmail" class="form-label">
                                <i class="bi bi-envelope"></i> Email administrateur
                            </label>
                            <input 
                                type="email" 
                                class="form-control form-control-lg" 
                                id="adminEmail" 
                                name="email" 
                                placeholder="admin@alogoto.bj"
                                required
                                autofocus
                            >
                        </div>

                        <div class="mb-3">
                            <label for="adminPassword" class="form-label">
                                <i class="bi bi-lock"></i> Mot de passe
                            </label>
                            <div class="input-group">
                                <input 
                                    type="password" 
                                    class="form-control form-control-lg" 
                                    id="adminPassword" 
                                    name="password" 
                                    placeholder="••••••••"
                                    required
                                >
                                <button 
                                    class="btn btn-outline-secondary" 
                                    type="button" 
                                    id="togglePassword"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Se souvenir de moi
                            </label>
                        </div>

                        <div class="mb-3 text-end">
                            <a href="<?php echo backend_url('forgot-password'); ?>" class="text-danger text-decoration-none small">
                                <i class="bi bi-key"></i> Mot de passe oublié ?
                            </a>
                        </div>

                        <button type="submit" class="btn btn-danger btn-lg w-100 mb-3">
                            <i class="bi bi-box-arrow-in-right"></i> Connexion Admin
                        </button>

                        <div class="text-center">
                            <a href="<?php echo frontend_public_url('login.php'); ?>" class="text-muted">
                                <i class="bi bi-arrow-left"></i> Retour à la connexion utilisateur
                            </a>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Compte administrateur par défaut: admin@alogoto.bj / Admin2026
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordInput = document.getElementById('adminPassword');
    const icon = this.querySelector('i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
});

// Pre-fill if coming from dashboard access page
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.has('email')) {
    document.getElementById('adminEmail').value = urlParams.get('email');
}
</script>

<?php include __DIR__ . '/../public/footer.php'; ?>
