<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('remboursements', function (Blueprint $table) {
            $table->foreignId('financement_id')->nullable()->after('institution_id')->constrained('financements')->nullOnDelete();
            $table->decimal('montant_rembourse', 15, 2)->default(0)->after('montant_total');
            $table->string('niveau_risque')->default('faible')->after('statut');
            $table->decimal('penalites', 15, 2)->default(0)->after('niveau_risque');
        });
    }

    public function down(): void
    {
        Schema::table('remboursements', function (Blueprint $table) {
            $table->dropForeign(['financement_id']);
            $table->dropColumn(['financement_id', 'montant_rembourse', 'niveau_risque', 'penalites']);
        });
    }
};
