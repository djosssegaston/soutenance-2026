<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repayment_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repayment_id')->constrained('remboursements')->cascadeOnDelete();
            $table->foreignId('institution_id')->constrained('institutions')->cascadeOnDelete();
            $table->foreignId('porteur_id')->constrained('users')->cascadeOnDelete();
            $table->text('motif');
            $table->string('statut')->default('ouvert'); // ouvert, en_cours, resolu, ferme
            $table->text('preuves')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayment_disputes');
    }
};
