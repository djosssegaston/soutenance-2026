<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->unsignedBigInteger('montant');
            $table->date('date_financement')->nullable();
            $table->string('statut')->default('en_analyse');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financements');
    }
};
