<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_status_histories', function (Blueprint $table) {
            // Renommer les anciennes colonnes
            if (Schema::hasColumn('project_status_histories', 'statut')) {
                $table->renameColumn('statut', 'old_status');
            }

            if (Schema::hasColumn('project_status_histories', 'source')) {
                $table->renameColumn('source', 'reason');
            }

            // Ajouter les nouvelles colonnes
            if (! Schema::hasColumn('project_status_histories', 'new_status')) {
                $table->string('new_status')->after('old_status');
            }

            if (! Schema::hasColumn('project_status_histories', 'actor_id')) {
                $table->foreignId('actor_id')
                    ->nullable()
                    ->after('reason')
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('project_status_histories', 'metadata')) {
                $table->json('metadata')->nullable()->after('actor_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_status_histories', function (Blueprint $table) {
            // Supprimer les colonnes ajoutées
            if (Schema::hasColumn('project_status_histories', 'metadata')) {
                $table->dropColumn('metadata');
            }

            if (Schema::hasColumn('project_status_histories', 'actor_id')) {
                $table->dropForeign(['actor_id']);
                $table->dropColumn('actor_id');
            }

            if (Schema::hasColumn('project_status_histories', 'new_status')) {
                $table->dropColumn('new_status');
            }

            // Restaurer les anciens noms
            if (Schema::hasColumn('project_status_histories', 'reason')) {
                $table->renameColumn('reason', 'source');
            }

            if (Schema::hasColumn('project_status_histories', 'old_status')) {
                $table->renameColumn('old_status', 'statut');
            }
        });
    }
};
