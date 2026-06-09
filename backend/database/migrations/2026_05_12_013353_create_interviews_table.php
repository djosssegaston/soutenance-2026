<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('porteur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('analyste_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('type_entretien'); // physique, visio, téléphonique, terrain
            $table->date('date_entretien');
            $table->time('heure_entretien');
            $table->string('lieu')->nullable();
            $table->string('statut')->default('programme'); // programme, confirme, reporte, annule, termine, absent
            $table->string('convocation_pdf')->nullable();
            $table->text('compte_rendu')->nullable();
            $table->string('decision_preliminaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interviews');
    }
};
