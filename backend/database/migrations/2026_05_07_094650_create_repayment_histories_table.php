<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repayment_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repayment_id')->constrained('remboursements')->onDelete('cascade');
            $table->string('action'); // created, updated, paid, confirmed, rejected, etc.
            $table->string('acteur_type'); // App\Models\User or similar
            $table->unsignedBigInteger('acteur_id');
            $table->json('details')->nullable(); // Store old/new values
            $table->timestamps();

            $table->index(['acteur_type', 'acteur_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayment_histories');
    }
};
