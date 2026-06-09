<?php
/**
 * Partial partagé : en-tête <head> pour les dashboards
 * Charte graphique unifiée (porteur orange)
 */
?>
<head>
    <!-- DONNÉES META -->
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo csrf_token(); ?>">

    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="Alogoto – Plateforme de microfinance">
    <meta name="author" content="Alogoto">
    <meta name="keywords" content="microfinance, tableau de bord, Alogoto">

    <!-- FAVICON -->
    <link rel="shortcut icon" type="image/x-icon" href="../../asset/images/brand/favicon.ico">

    <!-- TITRE -->
    <title>Alogoto</title>

    <!-- CSS BOOTSTRAP -->
    <link id="style" href="../../asset/css/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- CSS DE STYLE -->
    <link href="../../asset/css/style.css" rel="stylesheet">

    <!-- CSS des plugins -->
    <link href="../../asset/css/plugins.css" rel="stylesheet">

    <!--- CSS DES ICÔNES DE POLICE -->
    <link href="../../asset/css/icons.css" rel="stylesheet">

    <!-- CSS du sélecteur interne -->
    <link href="../../asset/switcher/css/switcher.css" rel="stylesheet">
    <link href="../../asset/switcher/demo.css" rel="stylesheet">

    <!-- THEME UNIFIÉ (charte graphique porteur orange) -->
    <link href="../../asset/css/porteur-theme.css" rel="stylesheet">

<?php
    $cssDir = dirname($_SERVER['SCRIPT_FILENAME']).'/css';
    $customV = is_file($cssDir.'/custom-dashboard.css') ? filemtime($cssDir.'/custom-dashboard.css') : 1;
    $headerV = is_file($cssDir.'/header.css') ? filemtime($cssDir.'/header.css') : 1;
    ?>
    <!-- CUSTOM CSS -->
    <link href="css/custom-dashboard.css?v=<?= $customV ?>" rel="stylesheet">

    <!-- HEADER CSS -->
    <link href="css/header.css?v=<?= $headerV ?>" rel="stylesheet">

    <!-- PROJECTION OVERRIDE CSS (lisibilité grand écran / soutenance) -->
    <link href="../shared/css/dashboard-projection.css?v=<?= filemtime(__DIR__.'/css/dashboard-projection.css') ?: 1 ?>" rel="stylesheet">

    <!-- DYNAMIC PUSHER + ECHO (chargés à la demande par realtime-init.js) -->

    <!-- GLOBAL FETCH WRAPPER: auto-include session cookies and AJAX headers -->
    <script>
    (function() {
        const origFetch = window.fetch;
        window.fetch = function(url, opts) {
            opts = opts || {};
            opts.credentials = opts.credentials || 'include';
            opts.headers = opts.headers || {};
            if (typeof opts.headers === 'object' && !Array.isArray(opts.headers) && !(opts.headers instanceof Headers)) {
                if (!opts.headers['X-Requested-With']) {
                    opts.headers['X-Requested-With'] = 'XMLHttpRequest';
                }
                if (!opts.headers['Accept']) {
                    opts.headers['Accept'] = 'application/json';
                }
            }
            return origFetch.call(this, url, opts);
        };
    })();
    </script>
</head>
