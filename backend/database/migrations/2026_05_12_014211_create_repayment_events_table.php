<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repayment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repayment_id')->constrained('remboursements')->cascadeOnDelete();
            $table->string('type_evenement'); // paiement, retard, validation, litige, restructuration
            $table->text('details')->nullable();
            $table->string('auteur');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repayment_events');
    }
};
