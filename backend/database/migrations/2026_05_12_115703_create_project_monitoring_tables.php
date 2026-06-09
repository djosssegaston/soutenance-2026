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
        Schema::create('project_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('action'); // validation, rejection, suspension, audit_opened, etc.
            $table->foreignId('performed_by')->constrained('users');
            $table->text('details')->nullable();
            $table->timestamps();
        });

        Schema::create('project_risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('risk_level'); // faible, moyen, élevé, critique
            $table->integer('score'); // 0-100
            $table->text('analysis')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_risk_scores');
        Schema::dropIfExists('project_audits');
    }
};
