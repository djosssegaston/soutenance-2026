<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Conversion des montants de BIGINT à DECIMAL(15,2)
     * Conforme aux standards comptables OHADA
     */
    public function up(): void
    {
        // Table projects
        Schema::table('projects', function (Blueprint $table) {
            $table->decimal('montant_demande', 15, 2)->default(0)->change();
            $table->decimal('montant_finance', 15, 2)->default(0)->change();
        });

        // Table financements
        Schema::table('financements', function (Blueprint $table) {
            $table->decimal('montant', 15, 2)->change();
        });

        // Table remboursements
        Schema::table('remboursements', function (Blueprint $table) {
            if (Schema::hasColumn('remboursements', 'montant')) {
                $table->decimal('montant', 15, 2)->change();
            }
        });

        // Table transactions (déjà en DECIMAL, mais on vérifie)
        // Pas besoin de modification - créée correctement

        Log::info('Migration amounts to DECIMAL(15,2) completed');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à BIGINT
        Schema::table('projects', function (Blueprint $table) {
            $table->bigInteger('montant_demande')->unsigned()->default(0)->change();
            $table->bigInteger('montant_finance')->unsigned()->default(0)->change();
        });

        Schema::table('financements', function (Blueprint $table) {
            $table->bigInteger('montant')->unsigned()->change();
        });

        Schema::table('remboursements', function (Blueprint $table) {
            if (Schema::hasColumn('remboursements', 'montant')) {
                $table->bigInteger('montant')->unsigned()->change();
            }
        });
    }
};
