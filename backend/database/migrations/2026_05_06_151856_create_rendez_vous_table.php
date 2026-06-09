<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendez_vous', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('set null');
            $table->foreignId('project_id')->nullable()->constrained('projects')->onDelete('set null');
            $table->dateTime('date_heure');
            $table->string('objet');
            $table->text('description')->nullable();
            $table->string('lieu')->nullable();
            $table->enum('statut', ['planifie', 'accepte', 'rejete', 'termine', 'annule'])->default('planifie');
            $table->text('notes_porteur')->nullable();
            $table->text('notes_institution')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendez_vous');
    }
};
