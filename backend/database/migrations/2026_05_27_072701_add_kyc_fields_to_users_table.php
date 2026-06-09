<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sexe')->nullable()->after('prenom');
            $table->date('date_naissance')->nullable()->after('sexe');
            $table->string('pays')->nullable()->after('adresse');
            $table->string('departement')->nullable()->after('pays');
            $table->string('commune')->nullable()->after('departement');
            $table->string('arrondissement')->nullable()->after('commune');
            $table->string('quartier')->nullable()->after('arrondissement');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'sexe',
                'date_naissance',
                'pays',
                'departement',
                'commune',
                'arrondissement',
                'quartier',
            ]);
        });
    }
};
