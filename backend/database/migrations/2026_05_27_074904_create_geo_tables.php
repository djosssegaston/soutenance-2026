<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pays', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code', 4)->nullable();
            $table->string('indicatif', 10)->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        Schema::create('departements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pays_id')->constrained('pays')->cascadeOnDelete();
            $table->string('nom');
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departement_id')->constrained('departements')->cascadeOnDelete();
            $table->string('nom');
            $table->string('code', 10)->nullable();
            $table->timestamps();
        });

        Schema::create('arrondissements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commune_id')->constrained('communes')->cascadeOnDelete();
            $table->string('nom');
            $table->timestamps();
        });

        Schema::create('quartiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('arrondissement_id')->constrained('arrondissements')->cascadeOnDelete();
            $table->string('nom');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quartiers');
        Schema::dropIfExists('arrondissements');
        Schema::dropIfExists('communes');
        Schema::dropIfExists('departements');
        Schema::dropIfExists('pays');
    }
};
