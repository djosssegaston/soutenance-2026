<?php
require_once __DIR__ . '/header.php';

$notifications = $notifications ?? collect();
$notificationStats = $notificationStats ?? [
    'unread' => 0,
    'read' => 0,
    'archived' => 0,
    'total' => 0,
];
$activeTab = $_GET['tab'] ?? 'all';
?>

<div class="main-content app-content mt-0">
    <div class="side-app">
        <div class="main-container container-fluid">
            <div class="page-header">
                <h1 class="page-title"><strong>NOTIFICATIONS</strong></h1>
                <div>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="index.php">ACCEUIL</a></li>
                        <li class="breadcrumb-item active" aria-current="page">NOTIFICATIONS</li>
                    </ol>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">NON LUES</span>
                                    <h2 class="mb-0 mt-1"><?php echo $notificationStats['unread']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-warning">
                                        <i class="bi bi-bell fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">LUES</span>
                                    <h2 class="mb-0 mt-1"><?php echo $notificationStats['read']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-success">
                                        <i class="bi bi-check-circle fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">ARCHIVÉES</span>
                                    <h2 class="mb-0 mt-1"><?php echo $notificationStats['archived']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-info">
                                        <i class="bi bi-archive fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="media align-items-center flex-wrap">
                                <div class="media-body">
                                    <span class="text-muted text-uppercase fs-12 fw-bold">TOTAL</span>
                                    <h2 class="mb-0 mt-1"><?php echo $notificationStats['total']; ?></h2>
                                </div>
                                <div class="align-self-center">
                                    <span class="avatar avatar-md brround bg-primary">
                                        <i class="bi bi-bell-fill fs-20"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-12">
                    <div class="card">
                        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2 py-3">
                            <h4 class="card-title mb-0">Mes Notifications</h4>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <ul class="nav nav-tabs border-0" id="notificationTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $activeTab === 'all' ? 'active' : ''; ?>" href="?tab=all">Non archivées</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $activeTab === 'unread' ? 'active' : ''; ?>" href="?tab=unread">Non lues</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?php echo $activeTab === 'archived' ? 'active' : ''; ?>" href="?tab=archived">Archivées</a>
                                    </li>
                                </ul>
                                <?php if ($activeTab !== 'archived'): ?>
                                <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn">
                                    <i class="fe fe-check-circle"></i> Tout marquer lu
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table text-nowrap mb-0">
                                    <thead>
                                        <tr>
                                            <th scope="col">N°</th>
                                            <th scope="col">TYPE</th>
                                            <th scope="col">TITRE</th>
                                            <th scope="col">CONTENU</th>
                                            <th scope="col">STATUT</th>
                                            <th scope="col">DATE</th>
                                            <th scope="col">ACTIONS</th>
                                        </tr>
                                    </thead>
                                    <tbody id="notificationsBody">
                                        <?php
                                        $filtered = $activeTab === 'archived'
                                            ? $notifications->whereNotNull('archived_at')
                                            : ($activeTab === 'unread'
                                                ? $notifications->where('is_read', false)->whereNull('archived_at')
                                                : $notifications->whereNull('archived_at'));

                                        $index = 0;
                                        foreach ($filtered as $notification):
                                            $index++;
                                            $typeIcon = match ($notification->type) {
                                                'info' => 'bi bi-info-circle text-info',
                                                'success' => 'bi bi-check-circle text-success',
                                                'warning' => 'bi bi-exclamation-triangle text-warning',
                                                'danger' => 'bi bi-x-octagon text-danger',
                                                default => 'bi bi-bell text-primary',
                                            };
                                            $isUnread = !$notification->is_read;
                                            $isArchived = $notification->archived_at !== null;
                                        ?>
                                        <tr data-id="<?php echo $notification->id; ?>"
                                            data-type="<?php echo $notification->type; ?>"
                                            data-title="<?php echo htmlspecialchars($notification->title ?? 'Sans titre', ENT_QUOTES, 'UTF-8'); ?>"
                                            data-content="<?php echo htmlspecialchars($notification->content, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-date="<?php echo $notification->created_at ? $notification->created_at->format('d M Y H:i') : ''; ?>"
                                            data-status="<?php echo $isArchived ? 'archived' : ($isUnread ? 'unread' : 'read'); ?>"
                                            class="<?php echo $isUnread && !$isArchived ? 'fw-bold' : ''; ?>">
                                            <td><?php echo $index; ?></td>
                                            <td>
                                                <span class="avatar avatar-md rounded-circle bg-light d-flex align-items-center justify-content-center">
                                                    <i class="<?php echo $typeIcon; ?> fs-18"></i>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="lh-1"><?php echo htmlspecialchars($notification->title ?? 'Sans titre', ENT_QUOTES, 'UTF-8'); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?php echo htmlspecialchars(mb_substr($notification->content, 0, 80), ENT_QUOTES, 'UTF-8'); ?><?php echo mb_strlen($notification->content) > 80 ? '...' : ''; ?></span>
                                            </td>
                                            <td>
                                                <?php if ($isArchived): ?>
                                                    <span class="badge bg-info-transparent rounded-pill text-info p-2 px-3">Archivée</span>
                                                <?php elseif ($isUnread): ?>
                                                    <span class="badge bg-warning-transparent rounded-pill text-warning p-2 px-3">Non lue</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-transparent rounded-pill text-success p-2 px-3">Lue</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?php echo $notification->created_at ? $notification->created_at->format('d M Y H:i') : ''; ?></small>
                                            </td>
                                            <td>
                                                <div class="g-1 d-flex gap-1 flex-nowrap">
                                                    <button class="btn btn-sm btn-outline-info btn-view-notif" data-id="<?php echo $notification->id; ?>" data-bs-toggle="tooltip" title="Voir">
                                                        <i class="fe fe-eye fs-14"></i>
                                                    </button>
                                                    <?php if (!$isArchived): ?>
                                                    <button class="btn btn-sm btn-outline-warning btn-archive-notif" data-id="<?php echo $notification->id; ?>" data-bs-toggle="tooltip" title="Archiver">
                                                        <i class="fe fe-folder fs-14"></i>
                                                    </button>
                                                    <?php endif; ?>
                                                    <button class="btn btn-sm btn-outline-danger btn-delete-notif" data-id="<?php echo $notification->id; ?>" data-bs-toggle="tooltip" title="Supprimer">
                                                        <i class="fe fe-trash-2 fs-14"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php if ($filtered->isEmpty()): ?>
                                        <tr>
                                            <td colspan="7" class="text-center py-5">
                                                <i class="bi bi-bell-slash fs-1 text-muted"></i>
                                                <h5 class="mt-3 text-muted">Aucune notification</h5>
                                                <p class="text-muted">Vous n'avez aucune notification pour le moment.</p>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3" id="notificationsPagination"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="viewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détail de la notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <div class="text-center py-3">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle"><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Confirmer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                <p id="confirmModalMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn" id="confirmModalBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<script>
const PAGINATION_ROWS = 10;

function updatePagination(tableBodyId, paginationId) {
    const tbody = document.getElementById(tableBodyId);
    const nav = document.getElementById(paginationId);
    if (!tbody || !nav) return;

    const rows = Array.from(tbody.children).filter(row => {
        if (row.dataset && row.dataset.filtered === 'true') return false;
        if (row.querySelector('td[colspan]')) return false;
        return true;
    });

    const totalPages = Math.max(1, Math.ceil(rows.length / PAGINATION_ROWS));
    let currentPage = parseInt(nav.dataset.currentPage || '1', 10);
    if (currentPage > totalPages) currentPage = totalPages;
    nav.dataset.currentPage = currentPage;

    Array.from(tbody.children).forEach(row => {
        if (row.dataset && row.dataset.filtered === 'true') {
            row.style.display = 'none';
        } else if (!row.querySelector('td[colspan]')) {
            const idx = rows.indexOf(row);
            if (idx !== -1) {
                row.style.display = (idx >= (currentPage - 1) * PAGINATION_ROWS && idx < currentPage * PAGINATION_ROWS) ? '' : 'none';
            }
        }
    });

    if (totalPages <= 1) { nav.innerHTML = ''; return; }

    let html = '<nav aria-label="Pagination"><ul class="pagination pagination-sm justify-content-center mb-0">';
    html += `<li class="page-item ${currentPage <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${currentPage - 1})"><i class="bi bi-chevron-left"></i></a></li>`;
    for (let i = 1; i <= totalPages; i++) {
        html += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${i})">${i}</a></li>`;
    }
    html += `<li class="page-item ${currentPage >= totalPages ? 'disabled' : ''}"><a class="page-link" href="#" onclick="return paginationGo('${tableBodyId}','${paginationId}',${currentPage + 1})"><i class="bi bi-chevron-right"></i></a></li>`;
    html += '</ul></nav>';
    nav.innerHTML = html;
}

function paginationGo(tableBodyId, paginationId, page) {
    const nav = document.getElementById(paginationId);
    if (nav) nav.dataset.currentPage = page;
    updatePagination(tableBodyId, paginationId);
    return false;
}

function resetPagination(tableBodyId, paginationId) {
    const nav = document.getElementById(paginationId);
    if (nav) nav.dataset.currentPage = 1;
    updatePagination(tableBodyId, paginationId);
}

const csrfToken = '<?php echo $csrf_token ?? ""; ?>';
var confirmModal = null;
var confirmCallback = null;

function execAction(url, method, cb) {
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(function(r) {
        return r.json().catch(function() { return {}; }).then(function(d) { return { status: r.status, data: d }; });
    })
    .then(function(resp) {
        if (resp.data && resp.data.message) {
            if (cb) cb(resp.data);
        } else if (resp.status >= 400) {
            ModalHelper.error('Erreur ' + resp.status, resp.data && resp.data.message ? resp.data.message : '');
        }
    })
    .catch(function() {
        ModalHelper.error('Erreur de connexion', 'Impossible de contacter le serveur.');
    });
}

function stripHtml(str) {
    return String(str || '').replace(/<[^>]*>/g, '');
}
function showConfirm(title, message, btnClass, btnText, callback) {
    document.getElementById('confirmModalTitle').innerHTML = title;
    document.getElementById('confirmModalMessage').textContent = stripHtml(message);
    var btn = document.getElementById('confirmModalBtn');
    btn.className = 'btn ' + btnClass;
    btn.textContent = btnText;
    confirmCallback = callback;
    confirmModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    var viewModalInstance = bootstrap.Modal.getOrCreateInstance(document.getElementById('viewModal'));
    confirmModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmModal'));

    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
        new bootstrap.Tooltip(el);
    });

    document.getElementById('confirmModalBtn').addEventListener('click', function() {
        if (confirmCallback) {
            confirmCallback();
            confirmCallback = null;
        }
        confirmModal.hide();
    });

    document.querySelectorAll('.btn-view-notif').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var row = document.querySelector('tr[data-id="' + id + '"]');
            if (!row) return;

            var title = row.dataset.title || 'Notification';
            var content = row.dataset.content || '';
            var type = row.dataset.type || 'info';
            var date = row.dataset.date || '';
            var status = row.dataset.status || 'unread';

            var typeLabels = { 'info': 'Information', 'success': 'Succès', 'warning': 'Avertissement', 'danger': 'Critique' };
            var typeColor = type === 'danger' ? 'danger' : type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'primary';
            var typeIcon = type === 'danger' ? 'x-octagon' : type === 'warning' ? 'exclamation-triangle' : type === 'success' ? 'check-circle' : 'info-circle';
            var statusLabel = status === 'archived' ? 'Archivée' : status === 'read' ? 'Lue' : 'Non lue';
            var statusColor = status === 'archived' ? 'info' : status === 'read' ? 'success' : 'warning';

            document.getElementById('viewModalBody').innerHTML =
                '<div class="mb-3">' +
                    '<span class="badge bg-' + typeColor + '-transparent text-' + typeColor + ' p-2 px-3 mb-2 d-inline-block">' +
                        '<i class="bi bi-' + typeIcon + ' me-1"></i> ' + (typeLabels[type] || type) +
                    '</span>' +
                    '<span class="badge bg-' + statusColor + '-transparent text-' + statusColor + ' p-2 px-3 mb-2 d-inline-block ms-2">' + statusLabel + '</span>' +
                '</div>' +
                '<h5 class="fw-bold mb-2">' + title.replace(/<[^>]*>/g, '') + '</h5>' +
                '<p class="text-muted mb-3">' + content.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '').replace(/on\w+\s*=\s*"[^"]*"/gi, '').replace(/on\w+\s*=\s*'[^']*'/gi, '') + '</p>' +
                '<small class="text-muted">Reçue le : ' + date + '</small>';

            viewModalInstance.show();

            execAction('/dashboard/porteur/notifications/' + id + '/read', 'POST', function() {
                var statusCell = row.querySelector('td:nth-child(5)');
                if (statusCell) {
                    statusCell.innerHTML = '<span class="badge bg-success-transparent rounded-pill text-success p-2 px-3">Lue</span>';
                }
                row.classList.remove('fw-bold');
                row.dataset.status = 'read';
            });
        });
    });

    document.querySelectorAll('.btn-archive-notif').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            showConfirm(
                '<i class="bi bi-archive me-2 text-warning"></i> Archiver',
                'Archiver cette notification ? Vous pourrez la retrouver dans l\'onglet "Archivées".',
                'btn-warning',
                'Archiver',
                function() {
                    execAction('/dashboard/porteur/notifications/' + id + '/archive', 'POST', function() {
                        location.reload();
                    });
                }
            );
        });
    });

    document.querySelectorAll('.btn-delete-notif').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            showConfirm(
                '<i class="bi bi-trash me-2 text-danger"></i> Supprimer',
                'Supprimer définitivement cette notification ? Cette action est irréversible.',
                'btn-danger',
                'Supprimer',
                function() {
                    execAction('/dashboard/porteur/notifications/' + id, 'DELETE', function() {
                        location.reload();
                    });
                }
            );
        });
    });

    updatePagination('notificationsBody', 'notificationsPagination');

    var markAllBtn = document.getElementById('markAllReadBtn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function() {
            showConfirm(
                '<i class="bi bi-check-circle me-2 text-primary"></i> Marquer tout lu',
                'Marquer toutes les notifications comme lues ?',
                'btn-primary',
                'Tout marquer lu',
                function() {
                    execAction('/dashboard/porteur/notifications/read-all', 'POST', function() {
                        location.reload();
                    });
                }
            );
        });
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
