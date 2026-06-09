<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financements', function (Blueprint $table) {
            $table->foreignId('porteur_id')->nullable()->after('institution_id')->constrained('users')->nullOnDelete();
            $table->decimal('montant_demande', 15, 2)->default(0)->after('porteur_id');
            $table->decimal('montant_propose', 15, 2)->default(0)->after('montant_demande');
            $table->decimal('montant_valide', 15, 2)->default(0)->after('montant_propose');
            $table->decimal('montant_decaisse', 15, 2)->default(0)->after('montant_valide');
            $table->decimal('taux_interet', 5, 2)->default(0)->after('montant_decaisse');
            $table->integer('duree')->default(12)->after('taux_interet'); // en mois
            $table->timestamp('date_validation')->nullable()->after('statut');
            $table->timestamp('date_decaissement')->nullable()->after('date_validation');
            $table->text('commentaires')->nullable()->after('date_decaissement');

            // On peut supprimer l'ancien champ 'montant' si on veut, mais restons prudents.
            // $table->dropColumn('montant');
        });
    }

    public function down(): void
    {
        Schema::table('financements', function (Blueprint $table) {
            $table->dropForeign(['porteur_id']);
            $table->dropColumn([
                'porteur_id', 'montant_demande', 'montant_propose', 'montant_valide',
                'montant_decaisse', 'taux_interet', 'duree', 'date_validation',
                'date_decaissement', 'commentaires',
            ]);
        });
    }
};
