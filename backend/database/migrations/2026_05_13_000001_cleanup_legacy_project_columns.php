<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
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
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('statut_soumission')->default('brouillon')->after('statut');
            $table->string('statut_validation_admin')->default('en_attente')->after('statut_soumission');
            $table->string('statut_financement_institution')->default('en_cours_de_traitement')->after('statut_validation_admin');
        });
    }
};
