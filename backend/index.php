<?php

// redirect to the public directory using an absolute URL.  compute the base
// path from SCRIPT_NAME to avoid a relative redirect that could resolve back
// to `backend/index.php` when Apache rewrites missing pages.
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
// strip the filename if present
$base = rtrim(dirname($script), '/');
if ($base === '' || $base === '.') {
    $base = '';
}
header('Location: ' . $base . '/public/');
exit;
