<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institution_analyses', function (Blueprint $table) {
            $table->foreignId('analyste_id')->nullable()->after('institution_id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('score_credibilite')->default(0)->after('risk_score');
            $table->unsignedInteger('score_solvabilite')->default(0)->after('score_credibilite');
            $table->unsignedInteger('note_globale')->default(0)->after('score_solvabilite');
            $table->text('recommandation')->nullable()->after('note_globale');
        });
    }

    public function down(): void
    {
        Schema::table('institution_analyses', function (Blueprint $table) {
            $table->dropForeign(['analyste_id']);
            $table->dropColumn(['analyste_id', 'score_credibilite', 'score_solvabilite', 'note_globale', 'recommandation']);
        });
    }
};
