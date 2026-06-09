<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('financements', function (Blueprint $table) {
            $table->decimal('montant_mensuel', 15, 2)->default(0)->after('commentaires');
            $table->unsignedTinyInteger('jour_remboursement')->nullable()->after('montant_mensuel');
            $table->text('conditions')->nullable()->after('jour_remboursement');
            $table->text('frais')->nullable()->after('conditions');
            $table->text('commentaire_plan')->nullable()->after('frais');
            $table->unsignedBigInteger('validated_by_porteur_id')->nullable()->after('commentaire_plan');
            $table->timestamp('date_acceptation_porteur')->nullable()->after('validated_by_porteur_id');
            $table->timestamp('date_approbation_imf')->nullable()->after('date_acceptation_porteur');
            $table->timestamp('date_rejet_imf')->nullable()->after('date_approbation_imf');
            $table->text('motif_rejet_imf')->nullable()->after('date_rejet_imf');
            $table->unsignedBigInteger('echeances_generees')->default(0)->after('motif_rejet_imf');
            $table->timestamp('date_cloture')->nullable()->after('echeances_generees');
        });
    }

    public function down(): void
    {
        Schema::table('financements', function (Blueprint $table) {
            $table->dropColumn([
                'montant_mensuel', 'jour_remboursement', 'conditions', 'frais',
                'commentaire_plan', 'validated_by_porteur_id', 'date_acceptation_porteur',
                'date_approbation_imf', 'date_rejet_imf', 'motif_rejet_imf',
                'echeances_generees', 'date_cloture',
            ]);
        });
    }
};
