<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Table des transactions financières (paiements FedaPay, remboursements, etc.)
     * Immutable - une fois créée, une transaction ne peut être modifiée
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Références
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();

            // Type de transaction
            $table->string('type'); // 'payment', 'refund', 'disbursement', 'fee'

            // Montant en DECIMAL pour précision financière (OHADA)
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('XOF'); // Franc CFA

            // Statut de la transaction
            $table->string('status'); // 'pending', 'completed', 'failed', 'cancelled'

            // Référence FedaPay
            $table->string('fedapay_transaction_id')->nullable()->unique();
            $table->string('fedapay_payment_method')->nullable(); // 'mobile_money', 'card'

            // Métadonnées JSON pour flexibilité
            $table->json('metadata')->nullable();

            // Dates
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            // Index pour performances
            $table->index(['type', 'status']);
            $table->index('fedapay_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
