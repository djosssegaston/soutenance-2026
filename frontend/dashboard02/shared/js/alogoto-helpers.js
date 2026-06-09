/**
 * ALOGOTO Helpers — toasts, confirmations, loader, logout
 * Chargé dans tous les dashboards via le footer
 */

window.ALOGOTO = window.ALOGOTO || {};

ALOGOTO.toast = (type, message) => {
    if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 non chargé — toast ignoré:', message);
        return;
    }
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    Toast.fire({ icon: type, title: message });
};

ALOGOTO.success = (msg) => ALOGOTO.toast('success', msg);
ALOGOTO.error = (msg) => ALOGOTO.toast('error', msg);
ALOGOTO.warning = (msg) => ALOGOTO.toast('warning', msg);
ALOGOTO.info = (msg) => ALOGOTO.toast('info', msg);

ALOGOTO.confirm = (title, text, confirmText = 'Confirmer', cancelText = 'Annuler') => {
    return Swal.fire({
        title,
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true,
    });
};

ALOGOTO.loading = (title = 'Chargement...') => {
    return Swal.fire({
        title,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });
};

ALOGOTO.logout = () => {
    Swal.fire({
        title: 'Déconnexion',
        text: 'Êtes-vous sûr de vouloir vous déconnecter ?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Se déconnecter',
        cancelButtonText: 'Annuler',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('logout-form') || document.getElementById('dashboard02-logout-form');
            if (form) form.submit();
        }
    });
};
