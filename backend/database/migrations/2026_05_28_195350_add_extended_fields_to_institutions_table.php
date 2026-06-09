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
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('sigle')->nullable()->after('nom');
            $table->string('type_institution')->nullable()->after('sigle');
            $table->string('rccm')->nullable()->after('type_institution');
            $table->string('ifu')->nullable()->after('rccm');
            $table->date('date_creation')->nullable()->after('ifu');
            $table->string('agrement')->nullable()->after('date_creation');
            $table->string('secteur')->nullable()->after('agrement');
            $table->string('bp')->nullable()->after('adresse');
            $table->string('telephone_secondaire')->nullable()->after('telephone');
            $table->string('pays_id')->nullable()->after('telephone_secondaire');
            $table->string('departement_id')->nullable()->after('pays_id');
            $table->string('commune_id')->nullable()->after('departement_id');
            $table->string('arrondissement_id')->nullable()->after('commune_id');
            $table->string('arrondissement_nom')->nullable()->after('arrondissement_id');
            $table->string('quartier_id')->nullable()->after('arrondissement_nom');
            $table->string('quartier_nom')->nullable()->after('quartier_id');
            $table->string('resp_prenom')->nullable()->after('quartier_nom');
            $table->string('resp_nom')->nullable()->after('resp_prenom');
            $table->string('resp_fonction')->nullable()->after('resp_nom');
            $table->string('resp_sexe')->nullable()->after('resp_fonction');
            $table->date('resp_date_naissance')->nullable()->after('resp_sexe');
            $table->string('resp_nationalite')->nullable()->after('resp_date_naissance');
            $table->string('resp_type_piece')->nullable()->after('resp_nationalite');
            $table->string('resp_numero_piece')->nullable()->after('resp_type_piece');
            $table->string('resp_telephone')->nullable()->after('resp_numero_piece');
            $table->string('resp_email')->nullable()->after('resp_telephone');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn([
                'sigle', 'type_institution', 'rccm', 'ifu', 'date_creation',
                'agrement', 'secteur', 'bp', 'telephone_secondaire',
                'pays_id', 'departement_id', 'commune_id',
                'arrondissement_id', 'arrondissement_nom',
                'quartier_id', 'quartier_nom',
                'resp_prenom', 'resp_nom', 'resp_fonction', 'resp_sexe',
                'resp_date_naissance', 'resp_nationalite',
                'resp_type_piece', 'resp_numero_piece',
                'resp_telephone', 'resp_email',
            ]);
        });
    }
};
