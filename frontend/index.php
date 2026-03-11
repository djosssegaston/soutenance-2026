<?php

// the frontend `index.php` exists solely to forward requests into the
// `public/` subdirectory.  use the URL helper so we always generate a valid
// absolute location even if the site is deployed under a path prefix.
require_once __DIR__ . '/includes/path_helpers.php';
header('Location: ' . frontend_public_url('index.php'));
exit;
