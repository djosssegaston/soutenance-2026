/**
 * Custom Switcher — Alogoto
 * Dark/Light mode, Menu, Header, RTL/LTR toggles.
 * Ne produit aucune erreur si les éléments DOM sont absents.
 */

document.addEventListener('DOMContentLoaded', function () {
    var switch2 = document.getElementById('myonoffswitch2'); // Dark mode
    var switch1 = document.getElementById('myonoffswitch1'); // Light mode
    var switch3 = document.getElementById('myonoffswitch3'); // Light menu
    var switch5 = document.getElementById('myonoffswitch5'); // Dark menu
    var switch6 = document.getElementById('myonoffswitch6'); // Header light
    var switch8 = document.getElementById('myonoffswitch8'); // Dark header
    var htmlElement = document.documentElement;

    /* ---- Restaurer les états sauvegardés ---- */

    if (localStorage.getItem('dark-mode') === 'true') {
        document.body.classList.add('dark-mode');
        document.body.classList.remove('light-mode');
        if (switch2) switch2.checked = true;
    } else {
        document.body.classList.add('light-mode');
        document.body.classList.remove('dark-mode');
        if (switch1) switch1.checked = true;
    }

    if (localStorage.getItem('dark-menu') === 'true') {
        document.body.classList.add('dark-menu');
        if (switch5) switch5.checked = true;
    } else if (localStorage.getItem('light-menu') === 'true') {
        document.body.classList.add('light-menu');
        if (switch3) switch3.checked = true;
    }

    if (localStorage.getItem('dark-header') === 'true') {
        document.body.classList.add('dark-header');
        if (switch8) switch8.checked = true;
    }

    if (localStorage.getItem('header-light') === 'true') {
        document.body.classList.add('header-light');
        if (switch6) switch6.checked = true;
    }

    /* ---- Event listeners (uniquement si l'élément existe) ---- */

    if (switch2) {
        switch2.addEventListener('change', function (event) {
            if (event.target.checked) {
                document.body.classList.add('dark-mode');
                document.body.classList.remove('light-mode');
                localStorage.setItem('dark-mode', 'true');
                if (switch1) switch1.checked = false;
                if (switch5) { switch5.checked = true; document.body.classList.add('dark-menu'); document.body.classList.remove('light-menu'); }
                localStorage.setItem('dark-menu', 'true');
                localStorage.removeItem('light-menu');
                if (switch8) { switch8.checked = true; document.body.classList.add('dark-header'); document.body.classList.remove('header-light'); }
                localStorage.setItem('dark-header', 'true');
                localStorage.removeItem('header-light');
            }
        });
    }

    if (switch1) {
        switch1.addEventListener('change', function (event) {
            if (event.target.checked) {
                document.body.classList.add('light-mode');
                document.body.classList.remove('dark-mode');
                localStorage.setItem('dark-mode', 'false');
                if (switch2) switch2.checked = false;
                if (switch3) { switch3.checked = true; document.body.classList.add('light-menu'); document.body.classList.remove('dark-menu'); }
                localStorage.setItem('light-menu', 'true');
                localStorage.removeItem('dark-menu');
                if (switch6) { switch6.checked = true; document.body.classList.add('header-light'); document.body.classList.remove('dark-header'); }
                localStorage.setItem('header-light', 'true');
                localStorage.removeItem('dark-header');
            }
        });
    }

    if (switch3) {
        switch3.addEventListener('change', function (event) {
            if (event.target.checked) {
                document.body.classList.remove('dark-menu');
                document.body.classList.add('light-menu');
                localStorage.setItem('light-menu', 'true');
                localStorage.removeItem('dark-menu');
            }
        });
    }

    if (switch5) {
        switch5.addEventListener('change', function (event) {
            if (event.target.checked) {
                document.body.classList.add('dark-menu');
                document.body.classList.remove('light-menu');
                localStorage.setItem('dark-menu', 'true');
                localStorage.removeItem('light-menu');
            }
        });
    }

    if (switch8) {
        switch8.addEventListener('change', function (event) {
            if (event.target.checked) {
                document.body.classList.add('dark-header');
                document.body.classList.remove('header-light');
                localStorage.setItem('dark-header', 'true');
                localStorage.removeItem('header-light');
            }
        });
    }

    if (switch6) {
        switch6.addEventListener('change', function (event) {
            if (event.target.checked) {
                document.body.classList.add('header-light');
                document.body.classList.remove('dark-header');
                localStorage.setItem('header-light', 'true');
                localStorage.removeItem('dark-header');
            }
        });
    }
});

/* ---- RTL/LTR (jQuery, chargé après jQuery) ---- */

(function ($) {
    'use strict';

    if (typeof $ === 'undefined') return;

    function applyRTL() {
        $('body').addClass('rtl').removeClass('ltr');
        $('html[lang="en"]').attr('dir', 'rtl');
        var styleEl = document.getElementById('style');
        if (styleEl) {
            styleEl.setAttribute('href', '../assets/css/plugins/bootstrap/css/bootstrap.rtl.min.css');
        }
        $('#myonoffswitch24').prop('checked', true);
        var carousel = $('.owl-carousel');
        $.each(carousel, function () {
            var data = $(this).data('owl.carousel');
            if (data) {
                data.settings.rtl = true;
                data.options.rtl = true;
                $(this).trigger('refresh.owl.carousel');
            }
        });
        localStorage.setItem('vistartl', 'true');
        localStorage.removeItem('vistaltr');
    }

    function applyLTR() {
        $('body').addClass('ltr').removeClass('rtl');
        $('html[lang="en"]').attr('dir', 'ltr');
        var styleEl = document.getElementById('style');
        if (styleEl) {
            styleEl.setAttribute('href', '../assets/css/plugins/bootstrap/css/bootstrap.min.css');
        }
        $('#myonoffswitch23').prop('checked', true);
        var carousel = $('.owl-carousel');
        $.each(carousel, function () {
            var data = $(this).data('owl.carousel');
            if (data) {
                data.settings.rtl = false;
                data.options.rtl = false;
                $(this).trigger('refresh.owl.carousel');
            }
        });
        localStorage.setItem('vistaltr', 'true');
        localStorage.removeItem('vistartl');
    }

    // Restaurer l'état RTL/LTR au chargement
    if (localStorage.getItem('vistartl') === 'true') {
        applyRTL();
    } else if (localStorage.getItem('vistaltr') === 'true') {
        applyLTR();
    }

    // Boutons RTL/LTR
    $(document).on('click', '#myonoffswitch24', function () {
        if (this.checked) applyRTL();
    });

    $(document).on('click', '#myonoffswitch23', function () {
        if (this.checked) applyLTR();
    });

    // Reset
    $(document).on('click', '#ThemeReset', function () {
        localStorage.clear();
        location.reload();
    });

})(typeof jQuery !== 'undefined' ? jQuery : null);
