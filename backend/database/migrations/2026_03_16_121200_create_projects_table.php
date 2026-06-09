<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('secteur')->nullable();
            $table->unsignedBigInteger('montant_demande')->default(0);
            $table->unsignedBigInteger('montant_finance')->default(0);
            $table->string('duree')->nullable();
            $table->string('localisation')->nullable();
            $table->string('statut')->default('soumis');
            $table->string('statut_soumission')->default('brouillon');
            $table->string('statut_validation_admin')->default('en_attente');
            $table->string('statut_financement_institution')->default('en_cours_de_traitement');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
