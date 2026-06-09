<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Suppression des colonnes de statut redondantes
     * Maintenant gérées par la state machine via project.statut
     */
    public function up(): void
    {
        // Supprimer les colonnes redondantes de projects
        Schema::table('projects', function (Blueprint $table) {
            // Ces colonnes sont maintenant gérées par la state machine
            if (Schema::hasColumn('projects', 'statut_soumission')) {
                $table->dropColumn('statut_soumission');
            }

            if (Schema::hasColumn('projects', 'statut_validation_admin')) {
                $table->dropColumn('statut_validation_admin');
            }

            if (Schema::hasColumn('projects', 'statut_financement_institution')) {
                $table->dropColumn('statut_financement_institution');
            }
        });

        // NOTE: La colonne 'statut' principale est conservée
        // Elle est maintenant gérée par l'enum ProjectStatus
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (! Schema::hasColumn('projects', 'statut_soumission')) {
                $table->string('statut_soumission')->nullable()->after('statut');
            }

            if (! Schema::hasColumn('projects', 'statut_validation_admin')) {
                $table->string('statut_validation_admin')->nullable()->after('statut_soumission');
            }

            if (! Schema::hasColumn('projects', 'statut_financement_institution')) {
                $table->string('statut_financement_institution')->nullable()->after('statut_validation_admin');
            }
        });

        // Restaurer les données (si possible)
        DB::table('projects')->update([
            'statut_soumission' => DB::raw('statut'),
        ]);
    }
};
