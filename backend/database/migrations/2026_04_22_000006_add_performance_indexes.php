<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajout d'index pour optimiser les performances
     * Nécessaire pour supporter 100+ utilisateurs/jour
     */
    public function up(): void
    {
        // Index sur projects pour les recherches fréquentes
        Schema::table('projects', function (Blueprint $table) {
            // Recherche par utilisateur
            if (! $this->hasIndex('projects', 'idx_projects_user_id')) {
                $table->index('user_id', 'idx_projects_user_id');
            }

            // Filtrage par statut
            if (! $this->hasIndex('projects', 'idx_projects_statut')) {
                $table->index('statut', 'idx_projects_statut');
            }

            // Recherche par secteur
            if (! $this->hasIndex('projects', 'idx_projects_secteur')) {
                $table->index('secteur', 'idx_projects_secteur');
            }

            // Tri par date
            if (! $this->hasIndex('projects', 'idx_projects_created_at')) {
                $table->index('created_at', 'idx_projects_created_at');
            }
        });

        // Index sur financements
        Schema::table('financements', function (Blueprint $table) {
            if (! $this->hasIndex('financements', 'idx_financements_project_id')) {
                $table->index('project_id', 'idx_financements_project_id');
            }

            if (! $this->hasIndex('financements', 'idx_financements_institution_id')) {
                $table->index('institution_id', 'idx_financements_institution_id');
            }

            if (! $this->hasIndex('financements', 'idx_financements_statut')) {
                $table->index('statut', 'idx_financements_statut');
            }
        });

        // Index sur remboursements
        Schema::table('remboursements', function (Blueprint $table) {
            if (! $this->hasIndex('remboursements', 'idx_remboursements_project_id')) {
                $table->index('project_id', 'idx_remboursements_project_id');
            }

            if (! $this->hasIndex('remboursements', 'idx_remboursements_statut')) {
                $table->index('statut', 'idx_remboursements_statut');
            }

            if (! $this->hasIndex('remboursements', 'idx_remboursements_date_echeance')) {
                $table->index('date_echeance', 'idx_remboursements_date_echeance');
            }
        });

        // Index sur transactions
        Schema::table('transactions', function (Blueprint $table) {
            if (! $this->hasIndex('transactions', 'idx_transactions_project_id')) {
                $table->index('project_id', 'idx_transactions_project_id');
            }

            if (! $this->hasIndex('transactions', 'idx_transactions_user_id')) {
                $table->index('user_id', 'idx_transactions_user_id');
            }

            if (! $this->hasIndex('transactions', 'idx_transactions_status')) {
                $table->index('status', 'idx_transactions_status');
            }

            if (! $this->hasIndex('transactions', 'idx_transactions_type')) {
                $table->index('type', 'idx_transactions_type');
            }
        });

        // Index sur notifications
        Schema::table('notifications', function (Blueprint $table) {
            if (Schema::hasColumn('notifications', 'user_id') && Schema::hasColumn('notifications', 'lu')) {
                if (! $this->hasIndex('notifications', 'idx_notifications_user_read')) {
                    $table->index(['user_id', 'lu'], 'idx_notifications_user_read');
                }
            }

            if (! $this->hasIndex('notifications', 'idx_notifications_created')) {
                $table->index('created_at', 'idx_notifications_created');
            }
        });

        // Index sur messages
        Schema::table('messages', function (Blueprint $table) {
            if (! $this->hasIndex('messages', 'idx_messages_conversation')) {
                $table->index('conversation_id', 'idx_messages_conversation');
            }

            if (! $this->hasIndex('messages', 'idx_messages_created')) {
                $table->index('created_at', 'idx_messages_created');
            }
        });

        // Index sur audit_logs pour les rapports
        Schema::table('audit_logs', function (Blueprint $table) {
            if (! $this->hasIndex('audit_logs', 'idx_audit_user_date')) {
                $table->index(['user_id', 'created_at'], 'idx_audit_user_date');
            }

            if (! $this->hasIndex('audit_logs', 'idx_audit_action')) {
                $table->index('action', 'idx_audit_action');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $indexes = [
            'projects' => [
                'idx_projects_user_id',
                'idx_projects_statut',
                'idx_projects_secteur',
                'idx_projects_created_at',
            ],
            'financements' => [
                'idx_financements_project_id',
                'idx_financements_institution_id',
                'idx_financements_statut',
            ],
            'remboursements' => [
                'idx_remboursements_project_id',
                'idx_remboursements_statut',
                'idx_remboursements_date_echeance',
            ],
            'transactions' => [
                'idx_transactions_project_id',
                'idx_transactions_user_id',
                'idx_transactions_status',
                'idx_transactions_type',
            ],
            'notifications' => [
                'idx_notifications_user_read',
                'idx_notifications_created',
            ],
            'messages' => [
                'idx_messages_conversation',
                'idx_messages_created',
            ],
            'audit_logs' => [
                'idx_audit_user_date',
                'idx_audit_action',
            ],
        ];

        foreach ($indexes as $table => $indexList) {
            Schema::table($table, function (Blueprint $table) use ($indexList) {
                foreach ($indexList as $index) {
                    $table->dropIndex($index);
                }
            });
        }
    }

    /**
     * Vérifier si un index existe déjà
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $tableName = config('database.connections.mysql.prefix').$table;

        $indexes = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.STATISTICS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$tableName}'
            AND INDEX_NAME = '{$indexName}'
        ");

        return $indexes[0]->count > 0;
    }
};
