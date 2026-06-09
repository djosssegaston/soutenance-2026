<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ALOGOTO2 - Accès Dashboards</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .dashboard-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.3);
        }
        .card-header-custom {
            padding: 2rem;
            color: white;
            text-align: center;
        }
        .admin-header { background: linear-gradient(135deg, #dc3545, #c82333); }
        .porteur-header { background: linear-gradient(135deg, #007bff, #0056b3); }
        .institution-header { background: linear-gradient(135deg, #28a745, #1e7e34); }
        .card-body {
            padding: 2rem;
        }
        .credential-box {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
        }
        .btn-access {
            width: 100%;
            padding: 0.75rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .icon-large {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-5">
            <h1 class="text-white display-4 fw-bold">
                <i class="bi bi-speedometer2"></i>
                ALOGOTO2
            </h1>
            <p class="text-white-50 fs-4">Portail d'Accès aux Dashboards</p>
        </div>

        <div class="row g-4">
            <!-- Admin Dashboard -->
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-header-custom admin-header">
                        <i class="bi bi-shield-lock icon-large"></i>
                        <h3>Administrateur</h3>
                        <p class="mb-0">Gestion complète de la plateforme</p>
                    </div>
                    <div class="card-body">
                        <div class="credential-box">
                            <strong><i class="bi bi-person-circle"></i> Email:</strong><br>
                            <code>admin@alogoto.bj</code>
                        </div>
                        <div class="credential-box">
                            <strong><i class="bi bi-key"></i> Rôle:</strong><br>
                            <span class="badge bg-danger">admin</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li><i class="bi bi-check-circle text-success"></i> Validation projets</li>
                            <li><i class="bi bi-check-circle text-success"></i> Gestion utilisateurs</li>
                            <li><i class="bi bi-check-circle text-success"></i> Audit & Sécurité</li>
                            <li><i class="bi bi-check-circle text-success"></i> Rapports finance</li>
                        </ul>
                        <a href="/login?email=admin@alogoto.bj&role=admin" class="btn btn-danger btn-access">
                            <i class="bi bi-box-arrow-in-right"></i> Accéder Admin
                        </a>
                    </div>
                </div>
            </div>

            <!-- Porteur Dashboard -->
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-header-custom porteur-header">
                        <i class="bi bi-briefcase icon-large"></i>
                        <h3>Porteur de Projet</h3>
                        <p class="mb-0">Gestion de vos projets</p>
                    </div>
                    <div class="card-body">
                        <div class="credential-box">
                            <strong><i class="bi bi-person-circle"></i> Email:</strong><br>
                            <code>porteur1@alogoto.bj</code>
                        </div>
                        <div class="credential-box">
                            <strong><i class="bi bi-key"></i> Rôle:</strong><br>
                            <span class="badge bg-primary">porteur</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li><i class="bi bi-check-circle text-success"></i> Créer projets</li>
                            <li><i class="bi bi-check-circle text-success"></i> Suivi statut</li>
                            <li><i class="bi bi-check-circle text-success"></i> Paiement FedaPay</li>
                            <li><i class="bi bi-check-circle text-success"></i> Messagerie</li>
                        </ul>
                        <a href="/login?email=porteur1@alogoto.bj&role=porteur" class="btn btn-primary btn-access">
                            <i class="bi bi-box-arrow-in-right"></i> Accéder Porteur
                        </a>
                    </div>
                </div>
            </div>

            <!-- Institution Dashboard -->
            <div class="col-md-4">
                <div class="dashboard-card h-100">
                    <div class="card-header-custom institution-header">
                        <i class="bi bi-bank icon-large"></i>
                        <h3>Institution Financière</h3>
                        <p class="mb-0">Analyse & Financement</p>
                    </div>
                    <div class="card-body">
                        <div class="credential-box">
                            <strong><i class="bi bi-person-circle"></i> Email:</strong><br>
                            <code>institution1@alogoto.bj</code>
                        </div>
                        <div class="credential-box">
                            <strong><i class="bi bi-key"></i> Rôle:</strong><br>
                            <span class="badge bg-success">institution</span>
                        </div>
                        <ul class="list-unstyled mb-4">
                            <li><i class="bi bi-check-circle text-success"></i> Analyser projets</li>
                            <li><i class="bi bi-check-circle text-success"></i> Planifier entretiens</li>
                            <li><i class="bi bi-check-circle text-success"></i> Financer projets</li>
                            <li><i class="bi bi-check-circle text-success"></i> Suivi remboursements</li>
                        </ul>
                        <a href="/login?email=institution1@alogoto.bj&role=institution" class="btn btn-success btn-access">
                            <i class="bi bi-box-arrow-in-right"></i> Accéder Institution
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-5">
            <div class="alert alert-light d-inline-block">
                <i class="bi bi-info-circle text-primary"></i>
                <strong>Note:</strong> Cliquez sur un bouton pour vous connecter automatiquement avec le compte de démonstration.
            </div>
        </div>
    </div>
</body>
</html>
