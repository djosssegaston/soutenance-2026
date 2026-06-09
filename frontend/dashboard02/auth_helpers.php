<?php
/**
 * Authentication helpers for dashboard02/
 * Supports both Laravel auth and legacy PHP session auth
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('dashboard02_auth_check')) {
    /**
     * Check if user is authenticated (Laravel or legacy session)
     */
    function dashboard02_auth_check(): bool
    {
        // First check Laravel auth (when running through Laravel)
        if (function_exists('auth') && auth()->check()) {
            return true;
        }
        // Fallback to legacy session check
        return isset($_SESSION['dashboard02_user']) && !empty($_SESSION['dashboard02_user']);
    }
}

if (!function_exists('dashboard02_user')) {
    /**
     * Get current authenticated user (Laravel or legacy session)
     */
    function dashboard02_user()
    {
        // First try Laravel auth
        if (function_exists('auth') && auth()->check()) {
            $user = auth()->user();
            return [
                'id' => $user->id,
                'name' => $user->name,
                'prenom' => $user->prenom ?? '',
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'telephone_verified_at' => $user->telephone_verified_at,
                'role' => $user->role,
                'telephone' => $user->telephone ?? '',
                'adresse' => $user->adresse ?? '',
                'activite' => $user->activite ?? '',
                'avatar_url' => $user->avatar_url ?? '',
                'entreprise_nom' => $user->entreprise_nom ?? '',
                'entreprise_secteur' => $user->entreprise_secteur ?? '',
                'statut' => $user->statut ?? 'actif',
            ];
        }
        // Fallback to legacy session
        return $_SESSION['dashboard02_user'] ?? null;
    }
}

if (!function_exists('dashboard02_login')) {
    /**
     * Login user and store in session
     */
    function dashboard02_login(array $user): void
    {
        $_SESSION['dashboard02_user'] = $user;
    }
}

if (!function_exists('dashboard02_logout')) {
    /**
     * Logout user from session
     */
    function dashboard02_logout(): void
    {
        unset($_SESSION['dashboard02_user']);
        if (function_exists('auth') && auth()->check()) {
            auth()->logout();
        }
    }
}
