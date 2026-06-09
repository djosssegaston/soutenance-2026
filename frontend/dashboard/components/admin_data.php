<?php
/**
 * Fichier de données admin - DONNÉES DYNAMIQUES UNIQUEMENT
 * 
 * Ce fichier ne contient PLUS de données statiques.
 * Toutes les données sont fournies par le contrôleur DashboardPagesController
 * via la méthode buildAdminData() qui interroge la base de données.
 */

if (isset($adminData) && is_array($adminData) && !empty($adminData)) {
    return $adminData;
}

return [
    'users' => [],
    'projects' => [],
    'disputes' => [],
    'audit_logs' => [],
    'notifications' => [],
    'conversations' => [],
    'messages' => [],
    'profile' => [],
    'sessions' => [],
];

