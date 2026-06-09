<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pays_id')->nullable()->constrained('pays')->nullOnDelete();
            $table->foreignId('departement_id')->nullable()->constrained('departements')->nullOnDelete();
            $table->foreignId('commune_id')->nullable()->constrained('communes')->nullOnDelete();
            $table->foreignId('arrondissement_id')->nullable()->constrained('arrondissements')->nullOnDelete();
            $table->foreignId('quartier_id')->nullable()->constrained('quartiers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pays_id');
            $table->dropConstrainedForeignId('departement_id');
            $table->dropConstrainedForeignId('commune_id');
            $table->dropConstrainedForeignId('arrondissement_id');
            $table->dropConstrainedForeignId('quartier_id');
        });
    }
};
