<!-- Modal Helper Global — charte graphique unifiée -->
<!-- Document Viewer Modal -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentViewerTitle"><i class="fe fe-file-text me-2"></i>Documents</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" id="documentViewerBody">
                <div class="text-center py-5 text-muted" id="documentViewerLoading">
                    <div class="spinner-border mb-3" role="status"></div>
                    <p>Chargement des documents...</p>
                </div>
                <div id="documentViewerContent" class="d-none"></div>
            </div>
            <div class="modal-footer" id="documentViewerFooter">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<style>
#documentViewerModal .modal-body {
    max-height: 75vh;
    overflow-y: auto;
}
#documentViewerModal .doc-item {
    transition: background 0.2s ease;
}
#documentViewerModal .doc-item:hover {
    background: #f8fafc;
}
#documentViewerModal .doc-preview {
    width: 100%;
    height: 60vh;
    border: none;
    border-radius: 8px;
}
#documentViewerModal .doc-preview-img {
    max-height: 60vh;
    object-fit: contain;
    border-radius: 8px;
    pointer-events: none;
    -webkit-user-drag: none;
    user-select: none;
}
#documentViewerModal .modal-body {
    -webkit-user-select: none;
    user-select: none;
}
#documentViewerModal .doc-preview {
    -webkit-user-select: none;
    user-select: none;
}
@media print {
    #documentViewerModal { display: none !important; }
}
@media (max-width: 768px) {
    #documentViewerModal .modal-dialog {
        margin: 0;
        max-width: 100%;
    }
    #documentViewerModal .modal-content {
        border-radius: 0;
        min-height: 100vh;
    }
    #documentViewerModal .modal-body {
        max-height: calc(100vh - 120px);
    }
    #documentViewerModal .doc-preview {
        height: 50vh;
    }
    #documentViewerModal .doc-preview-img {
        max-height: 50vh;
    }
}
</style>

<script>
window.DocumentViewer = {
    _docType: 'financement',
    open: function(documents, title, docType) {
        if (docType) this._docType = docType;
        var modalEl = document.getElementById('documentViewerModal');
        if (!modalEl) return;
        var titleEl = document.getElementById('documentViewerTitle');
        var loadingEl = document.getElementById('documentViewerLoading');
        var contentEl = document.getElementById('documentViewerContent');

        titleEl.innerHTML = '<i class="fe fe-file-text me-2"></i>' + (title || 'Documents');
        loadingEl.classList.remove('d-none');
        contentEl.classList.add('d-none');
        contentEl.innerHTML = '';

        var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static', keyboard: false });
        bsModal.show();

        // Process and render documents
        var activeDocType = this._docType || 'financement';
        setTimeout(function() {
            if (!documents || documents.length === 0) {
                contentEl.innerHTML = '<div class="text-center py-5 text-muted"><i class="fe fe-file fs-1 d-block mb-3"></i><p>Aucun document disponible.</p></div>';
            } else {
                var html = '<div class="list-group list-group-flush">';
                documents.forEach(function(doc) {
                    var fichier = doc.fichier || doc.url || doc.path || '';
                    var nom = doc.nom || doc.type_document || doc.type || 'Document';
                    var ext = fichier.split('.').pop().toLowerCase();
                    var isImage = ['jpg','jpeg','png','gif','svg','webp'].includes(ext);
                    var isPdf = ext === 'pdf';
                    var docId = doc.id || '';
                    var url = '/documents/secure/' + activeDocType + '/' + docId + '/view';

                    var icone = 'fe fe-file-text';
                    if (isImage) icone = 'fe fe-image';
                    else if (isPdf) icone = 'fe fe-file';

                    html += '<div class="list-group-item doc-item border-0 border-bottom py-3">';
                    html += '<div class="d-flex align-items-center gap-3">';
                    html += '<span class="avatar avatar-md bg-light rounded-circle flex-shrink-0"><i class="' + icone + ' fs-5 text-muted"></i></span>';
                    html += '<div class="flex-grow-1 min-w-0">';
                    html += '<h6 class="mb-0 text-truncate">' + escHtml(nom) + '</h6>';
                    html += '<small class="text-muted">' + ext.toUpperCase() + '</small>';
                    html += '</div>';
                    html += '<div class="d-flex gap-1 flex-shrink-0">';
                    if (isImage || isPdf) {
                        html += '<button class="btn btn-sm btn-outline-primary" onclick="DocumentViewer.preview(\'/documents/secure/' + activeDocType + '/' + docId + '/serve\', \'' + escHtml(nom).replace(/'/g, "\\'") + '\', \'' + (isImage ? 'img' : 'pdf') + '\')"><i class="fe fe-eye"></i></button>';
                    }
                    html += '<a href="/documents/secure/' + activeDocType + '/' + docId + '/view" class="btn btn-sm btn-outline-info" title="Voir"><i class="fe fe-folder-open"></i></a>';
                    html += '</div></div></div>';
                });
                html += '</div>';
                contentEl.innerHTML = html;
            }
            loadingEl.classList.add('d-none');
            contentEl.classList.remove('d-none');
        }, 100);
    },

    preview: function(url, nom, type) {
        var contentEl = document.getElementById('documentViewerContent');
        var loadingEl = document.getElementById('documentViewerLoading');

        loadingEl.classList.remove('d-none');
        contentEl.classList.add('d-none');

        setTimeout(function() {
            var html = '<div class="p-3">';
            html += '<div class="d-flex align-items-center justify-content-between mb-3">';
            html += '<h6 class="mb-0"><i class="fe fe-eye me-2"></i>' + escHtml(nom) + '</h6>';
            html += '<button class="btn btn-sm btn-outline-secondary" onclick="DocumentViewer.back()"><i class="fe fe-arrow-left"></i> Retour</button>';
            html += '</div>';
            if (type === 'img') {
                html += '<img src="' + url + '" class="doc-preview-img w-100" alt="' + escHtml(nom) + '" onerror="this.parentElement.innerHTML=\'<div class=\\\'alert alert-danger\\\'>Impossible d\\\'afficher cette image.</div>\'">';
            } else if (type === 'pdf') {
                html += '<iframe src="' + url + '" class="doc-preview" title="' + escHtml(nom) + '"></iframe>';
            }
            html += '</div>';

            contentEl.innerHTML = html;
            loadingEl.classList.add('d-none');
            contentEl.classList.remove('d-none');
        }, 100);
    },

    back: function() {
        // Re-render the document list without hiding/re-showing the modal
        if (this._lastDocs) {
            var documents = this._lastDocs;
            var title = this._lastTitle;
            var titleEl = document.getElementById('documentViewerTitle');
            var loadingEl = document.getElementById('documentViewerLoading');
            var contentEl = document.getElementById('documentViewerContent');
            if (!contentEl) return;

            titleEl.innerHTML = '<i class="fe fe-file-text me-2"></i>' + (title || 'Documents');
            loadingEl.classList.remove('d-none');
            contentEl.classList.add('d-none');

            var activeDocType = this._docType || 'financement';
            setTimeout(function() {
                if (!documents || documents.length === 0) {
                    contentEl.innerHTML = '<div class="text-center py-5 text-muted"><i class="fe fe-file fs-1 d-block mb-3"></i><p>Aucun document disponible.</p></div>';
                } else {
                    var html = '<div class="list-group list-group-flush">';
                    documents.forEach(function(doc) {
                        var fichier = doc.fichier || doc.url || doc.path || '';
                        var nom = doc.nom || doc.type_document || doc.type || 'Document';
                        var ext = fichier.split('.').pop().toLowerCase();
                        var isImage = ['jpg','jpeg','png','gif','svg','webp'].includes(ext);
                        var isPdf = ext === 'pdf';
                        var docId = doc.id || '';
                        var url = '/documents/secure/' + activeDocType + '/' + docId + '/view';

                        var icone = 'fe fe-file-text';
                        if (isImage) icone = 'fe fe-image';
                        else if (isPdf) icone = 'fe fe-file';

                        html += '<div class="list-group-item doc-item border-0 border-bottom py-3">';
                        html += '<div class="d-flex align-items-center gap-3">';
                        html += '<span class="avatar avatar-md bg-light rounded-circle flex-shrink-0"><i class="' + icone + ' fs-5 text-muted"></i></span>';
                        html += '<div class="flex-grow-1 min-w-0">';
                        html += '<h6 class="mb-0 text-truncate">' + escHtml(nom) + '</h6>';
                        html += '<small class="text-muted">' + ext.toUpperCase() + '</small>';
                        html += '</div>';
                        html += '<div class="d-flex gap-1 flex-shrink-0">';
                        if (isImage || isPdf) {
                            html += '<button class="btn btn-sm btn-outline-primary" onclick="DocumentViewer.preview(\'/documents/secure/' + activeDocType + '/' + docId + '/serve\', \'' + escHtml(nom).replace(/'/g, "\\'") + '\', \'' + (isImage ? 'img' : 'pdf') + '\')"><i class="fe fe-eye"></i></button>';
                        }
                        html += '<a href="/documents/secure/' + activeDocType + '/' + docId + '/view" class="btn btn-sm btn-outline-info" title="Voir"><i class="fe fe-folder-open"></i></a>';
                        html += '</div></div></div>';
                    });
                    html += '</div>';
                    contentEl.innerHTML = html;
                }
                loadingEl.classList.add('d-none');
                contentEl.classList.remove('d-none');
            }, 100);
        }
    },

    _lastDocs: null,
    _lastTitle: null,

    // Override open to persist docs for back navigation
    _origOpen: null
};

// Patch open to persist last state
(function() {
    var origOpen = window.DocumentViewer.open;
    window.DocumentViewer.open = function(documents, title, docType) {
        window.DocumentViewer._lastDocs = documents;
        window.DocumentViewer._lastTitle = title;
        origOpen.call(window.DocumentViewer, documents, title, docType);
    };
})();

// Anti-screenshot protection for DocumentViewer modal
(function() {
    function preventEvent(e) { e.preventDefault(); }
    function preventKeydown(e) {
        if (
            e.ctrlKey && ['s','p','u','S','P','U','c','C'].includes(e.key) ||
            e.key === 'F12' ||
            (e.ctrlKey && e.shiftKey && ['i','I','j','J','c','C'].includes(e.key))
        ) {
            e.preventDefault();
        }
    }
    var viewerModal = document.getElementById('documentViewerModal');
    if (viewerModal) {
        viewerModal.addEventListener('show.bs.modal', function() {
            document.addEventListener('contextmenu', preventEvent);
            document.addEventListener('keydown', preventKeydown);
            document.addEventListener('dragstart', preventEvent);
        });
        viewerModal.addEventListener('hidden.bs.modal', function() {
            document.removeEventListener('contextmenu', preventEvent);
            document.removeEventListener('keydown', preventKeydown);
            document.removeEventListener('dragstart', preventEvent);
        });
    }
})();
</script>

<!-- Modal Helper Global — charte graphique unifiée -->
<div class="modal fade" id="globalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="modal-accent-bar" id="globalModalAccent"></div>
            <div class="modal-header border-bottom-0 pb-1" id="globalModalHeader">
                <h5 class="modal-title fw-bold" id="globalModalTitle">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="globalModalCloseX"></button>
            </div>
            <div class="modal-body pt-1" id="globalModalBody">
                <p class="mb-0" id="globalModalMessage"></p>
            </div>
            <div class="modal-footer border-top-0 pt-0" id="globalModalFooter">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" id="globalModalCancelBtn">Annuler</button>
                <button type="button" class="btn btn-primary px-4" id="globalModalConfirmBtn">Confirmer</button>
            </div>
        </div>
    </div>
</div>

<style>
#globalModal .modal-content {
    overflow: hidden;
}
#globalModal .modal-accent-bar {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: #fca028;
    border-radius: 16px 16px 0 0;
    z-index: 10;
    transition: background 0.3s ease;
}
#globalModal .modal-title {
    font-size: 15px;
    color: #334155;
}
#globalModal .modal-body p {
    font-size: 14px;
    color: #64748b;
    line-height: 1.6;
}
#globalModal .btn {
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}
#globalModal .btn-outline-secondary {
    color: #64748b;
    border-color: #e2e8f0;
}
#globalModal .btn-outline-secondary:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #334155;
    transform: translateY(-1px);
}
</style>

<script>
(function() {
    'use strict';

    var modalEl = document.getElementById('globalModal');
    if (!modalEl) return;
        var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, { backdrop: 'static' });
    var resolvePromise = null;
    var mode = 'confirm';

    function getEl(id) { return document.getElementById(id); }
    var titleEl = getEl('globalModalTitle');
    var msgEl = getEl('globalModalMessage');
    var confirmBtn = getEl('globalModalConfirmBtn');
    var cancelBtn = getEl('globalModalCancelBtn');
    var closeX = getEl('globalModalCloseX');
    var headerEl = getEl('globalModalHeader');
    var footerEl = getEl('globalModalFooter');
    var accentEl = getEl('globalModalAccent');

    var ACCENT_COLORS = {
        primary: '#fca028',
        danger: '#f35120',
        success: '#00c486',
        warning: '#fca028',
        info: '#0dcaf0',
        secondary: '#64748b',
    };

    function getAccentColor(btnClass) {
        if (!btnClass) return ACCENT_COLORS.primary;
        for (var key in ACCENT_COLORS) {
            if (btnClass.indexOf('btn-' + key) !== -1) return ACCENT_COLORS[key];
        }
        return ACCENT_COLORS.primary;
    }

    function resetUI(btnClass) {
        var color = getAccentColor(btnClass);
        if (accentEl) accentEl.style.background = color;
        confirmBtn.className = 'btn px-4 ' + (btnClass || 'btn-primary');
        confirmBtn.style.display = '';
        cancelBtn.style.display = '';
        cancelBtn.className = 'btn btn-outline-secondary px-4';
        closeX.style.display = '';
        headerEl.className = 'modal-header border-bottom-0 pb-1';
    }

    confirmBtn.addEventListener('click', function() {
        if (resolvePromise) { resolvePromise(true); resolvePromise = null; }
        bsModal.hide();
    });

    cancelBtn.addEventListener('click', function() {
        if (resolvePromise) { resolvePromise(false); resolvePromise = null; }
        bsModal.hide();
    });

    modalEl.addEventListener('hidden.bs.modal', function() {
        if (resolvePromise) { resolvePromise(false); resolvePromise = null; }
    });

    modalEl.addEventListener('shown.bs.modal', function() {
        if (mode === 'confirm') {
            cancelBtn.focus();
        } else {
            confirmBtn.focus();
        }
    });

    window.ModalHelper = {
        confirm: function(title, message, confirmText, cancelText, confirmBtnClass) {
            return new Promise(function(resolve) {
                mode = 'confirm';
                resetUI(confirmBtnClass || 'btn-primary');
                titleEl.innerHTML = title || 'Confirmation';
                msgEl.textContent = message || '';
                confirmBtn.textContent = confirmText || 'Confirmer';
                cancelBtn.textContent = cancelText || 'Annuler';
                confirmBtn.style.display = '';
                cancelBtn.style.display = '';
                closeX.style.display = '';
                resolvePromise = resolve;
                bsModal.show();
            });
        },

        alert: function(title, message, buttonText) {
            return new Promise(function(resolve) {
                mode = 'alert';
                resetUI('btn-primary');
                titleEl.innerHTML = title || 'Information';
                msgEl.textContent = message || '';
                confirmBtn.textContent = buttonText || 'OK';
                confirmBtn.style.display = '';
                cancelBtn.style.display = 'none';
                closeX.style.display = '';
                resolvePromise = function(val) { resolve(); };
                confirmBtn.onclick = function() { resolve(); bsModal.hide(); };
                bsModal.show();
            });
        },

        error: function(title, message) {
            return new Promise(function(resolve) {
                mode = 'error';
                resetUI('btn-danger');
                titleEl.innerHTML = '<i class="bi bi-x-octagon me-2 text-danger"></i>' + (title || 'Erreur');
                msgEl.textContent = message || 'Une erreur est survenue.';
                confirmBtn.textContent = 'OK';
                confirmBtn.style.display = '';
                cancelBtn.style.display = 'none';
                closeX.style.display = '';
                resolvePromise = function(val) { resolve(); };
                confirmBtn.onclick = function() { resolve(); bsModal.hide(); };
                bsModal.show();
            });
        },

        success: function(title, message) {
            return new Promise(function(resolve) {
                mode = 'success';
                resetUI('btn-success');
                titleEl.innerHTML = '<i class="bi bi-check-circle me-2 text-success"></i>' + (title || 'Succès');
                msgEl.textContent = message || 'Opération réussie.';
                confirmBtn.textContent = 'OK';
                confirmBtn.style.display = '';
                cancelBtn.style.display = 'none';
                closeX.style.display = '';
                resolvePromise = function(val) { resolve(); };
                confirmBtn.onclick = function() { resolve(); bsModal.hide(); };
                bsModal.show();
            });
        },

        hide: function() {
            bsModal.hide();
        }
    };

    // Restore original confirmBtn onclick after modal hide
    modalEl.addEventListener('hidden.bs.modal', function() {
        confirmBtn.onclick = function() {
            if (resolvePromise) { resolvePromise(true); resolvePromise = null; }
            bsModal.hide();
        };
    });

    // Auto-submit forms with data-confirm attribute
    document.addEventListener('click', function(e) {
        var btn = e.target.closest('[data-confirm]');
        if (!btn) return;
        var form = btn.closest('form');
        if (!form) return;
        e.preventDefault();
        var message = btn.getAttribute('data-confirm');
        var confirmText = btn.getAttribute('data-confirm-text') || 'Oui, confirmer';
        var btnClass = btn.getAttribute('data-confirm-class') || 'btn-danger';
        ModalHelper.confirm(
            '<i class="bi bi-exclamation-triangle me-2 text-warning"></i> Confirmation',
            message,
            confirmText,
            'Annuler',
            btnClass
        ).then(function(confirmed) {
            if (!confirmed) return;
            var methodInput = form.querySelector('input[name="_method"]');
            if (methodInput && methodInput.value === 'DELETE') {
                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')?.value || '',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(new FormData(form))
                }).then(function(r) { return r.json(); }).then(function(data) {
                    if (data && data.success) {
                        if (typeof ALOGOTO !== 'undefined') ALOGOTO.success(data.message || 'Operation reussie.');
                        setTimeout(function() { window.location.reload(); }, 800);
                    } else {
                        if (typeof ALOGOTO !== 'undefined') ALOGOTO.error((data && data.message) || 'Erreur.');
                    }
                }).catch(function() {
                    if (typeof ALOGOTO !== 'undefined') ALOGOTO.error('Erreur lors de la suppression.');
                });
            } else {
                form.submit();
            }
        });
    });
})();
</script>