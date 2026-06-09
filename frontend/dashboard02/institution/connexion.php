<?php
/**
 * Institution Connexion - Redirects to the main login page
 * Standalone login page removed in favor of centralized Laravel authentication.
 * All institution logins go through /login with role-based redirection.
 */
header('Location: /login');
exit;
