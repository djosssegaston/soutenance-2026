<?php
/**
 * Path helpers for dashboard02/ - Isolated from Laravel backend
 * All paths are relative to the dashboard02/ folder
 */

if (!function_exists('dashboard02_url')) {
    /**
     * Generate URL for dashboard02 pages (relative paths)
     */
    function dashboard02_url(string $path = ''): string
    {
        $cleanPath = ltrim($path, '/');
        if ($cleanPath === '') {
            return 'index.php';
        }
        return $cleanPath;
    }
}

if (!function_exists('dashboard02_asset')) {
    /**
     * Generate asset URL for dashboard02 assets (relative paths)
     */
    function dashboard02_asset(string $path = ''): string
    {
        $cleanPath = ltrim($path, '/');
        return '../../asset/' . $cleanPath;
    }
}

if (!function_exists('dashboard02_user_url')) {
    /**
     * Generate URL based on user role in dashboard02
     */
    function dashboard02_user_url(string $role = 'porteur'): string
    {
        $baseUrls = [
            'porteur' => 'porteur/',
            'institution' => 'institution/',
            'admin' => 'admin/',
        ];
        
        return $baseUrls[$role] ?? 'porteur/';
    }
}
