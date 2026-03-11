<?php

// redirect to the frontend public folder using an absolute path.  we avoid
// a bare relative URL because when the project lives in a subdirectory the
// browser may resolve it incorrectly and start bouncing between locations.
require_once __DIR__ . '/frontend/includes/path_helpers.php';

// frontend_public_url returns an absolute path (begins with '/'), so it can be
// safely sent to the client.
header('Location: ' . frontend_public_url('index.php'));
exit;
