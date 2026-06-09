<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financement_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->cascadeOnDelete();
            $table->string('type_document'); // contrat, convention, garanties, preuve_decaissement
            $table->string('fichier');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financement_documents');
    }
};
