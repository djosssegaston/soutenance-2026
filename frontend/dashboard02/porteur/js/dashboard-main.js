/**
 * Dashboard Main - Porteur Cockpit
 */

document.addEventListener('DOMContentLoaded', function() {
    initTooltips();
});

function initTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}



const PAGINATION_ROWS = 10;

window.paginationGo = function(tbl, pag, page) {
    var nav = document.getElementById(pag);
    if (nav) nav.dataset.currentPage = page;
    updatePagination(tbl, pag);
    return false;
};

window.resetPagination = function(tbl, pag) {
    var nav = document.getElementById(pag);
    if (nav) nav.dataset.currentPage = 1;
    updatePagination(tbl, pag);
};

function updatePagination(tbl, pag) {
    var tbody = document.getElementById(tbl);
    var nav = document.getElementById(pag);
    if (!tbody || !nav) return;
    var rows = Array.from(tbody.children).filter(function(r) {
        if (r.dataset && r.dataset.filtered === 'true') return false;
        if (r.querySelector('td[colspan]')) return false;
        return true;
    });
    var totalPages = Math.max(1, Math.ceil(rows.length / PAGINATION_ROWS));
    var cur = parseInt(nav.dataset.currentPage || '1', 10);
    if (cur > totalPages) cur = totalPages;
    nav.dataset.currentPage = cur;
    Array.from(tbody.children).forEach(function(r) {
        if (r.dataset && r.dataset.filtered === 'true') {
            r.style.display = 'none';
        } else if (!r.querySelector('td[colspan]')) {
            var idx = rows.indexOf(r);
            if (idx !== -1) {
                r.style.display = (idx >= (cur - 1) * PAGINATION_ROWS && idx < cur * PAGINATION_ROWS) ? '' : 'none';
            }
        }
    });
    if (totalPages <= 1) { nav.innerHTML = ''; return; }
    var h = '<nav aria-label="Pagination"><ul class="pagination pagination-sm justify-content-center mb-0">';
    h += '<li class="page-item ' + (cur <= 1 ? 'disabled' : '') + '" data-page="' + (cur - 1) + '"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>';
    for (var i = 1; i <= totalPages; i++) {
        h += '<li class="page-item ' + (i === cur ? 'active' : '') + '" data-page="' + i + '"><a class="page-link" href="#">' + i + '</a></li>';
    }
    h += '<li class="page-item ' + (cur >= totalPages ? 'disabled' : '') + '" data-page="' + (cur + 1) + '"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>';
    h += '</ul></nav>';
    nav.innerHTML = h;
    nav.onclick = function(e) {
        var li = e.target.closest('[data-page]');
        if (!li || li.classList.contains('disabled')) return;
        var p = parseInt(li.getAttribute('data-page'), 10);
        if (isNaN(p)) return;
        e.preventDefault();
        paginationGo(tbl, pag, p);
    };
}
