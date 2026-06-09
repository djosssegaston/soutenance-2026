<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('echeances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->unsignedTinyInteger('numero_echeance');
            $table->decimal('montant_capital', 15, 2)->default(0);
            $table->decimal('montant_interets', 15, 2)->default(0);
            $table->decimal('montant_frais', 15, 2)->default(0);
            $table->decimal('montant_total', 15, 2)->default(0);
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->decimal('montant_restant', 15, 2)->default(0);
            $table->date('date_echeance');
            $table->date('date_paiement')->nullable();
            $table->string('statut', 30)->default('pending');
            $table->decimal('penalites', 15, 2)->default(0);
            $table->string('methode_paiement', 50)->nullable();
            $table->string('transaction_reference', 255)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('statut');
            $table->index('date_echeance');
            $table->index(['financement_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('echeances');
    }
};
