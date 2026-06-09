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
        Schema::table('remboursements', function (Blueprint $table) {
            // Ajouter institution_id
            $table->foreignId('institution_id')->nullable()->after('project_id')->constrained('institutions')->nullOnDelete();

            // Renommer montant en montant_total et ajouter montant_restant
            $table->renameColumn('montant', 'montant_total');
            $table->decimal('montant_restant', 15, 2)->default(0)->after('montant_total');

            // Méthode de paiement et référence
            $table->string('methode_paiement')->nullable()->after('statut');
            $table->string('transaction_reference')->nullable()->after('methode_paiement');

            // Preuve et commentaires
            $table->string('preuve_path')->nullable()->after('transaction_reference');
            $table->text('commentaires')->nullable()->after('preuve_path');

            // Index pour performances
            $table->index('statut');
            $table->index('date_echeance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('remboursements', function (Blueprint $table) {
            $table->dropIndex(['statut']);
            $table->dropIndex(['date_echeance']);
            $table->dropColumn(['institution_id', 'montant_restant', 'methode_paiement', 'transaction_reference', 'preuve_path', 'commentaires']);
            $table->renameColumn('montant_total', 'montant');
        });
    }
};
