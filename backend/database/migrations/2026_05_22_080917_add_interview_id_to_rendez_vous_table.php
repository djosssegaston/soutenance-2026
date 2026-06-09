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
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->foreignId('interview_id')->nullable()->after('project_id')->constrained('interviews')->nullOnDelete();
            $table->boolean('from_institution')->default(false)->after('interview_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rendez_vous', function (Blueprint $table) {
            $table->dropForeign(['interview_id']);
            $table->dropColumn(['interview_id', 'from_institution']);
        });
    }
};
