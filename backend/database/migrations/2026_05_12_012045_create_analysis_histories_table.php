<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('analysis_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_id')->constrained('institution_analyses')->cascadeOnDelete();
            $table->string('action'); // e.g., 'created', 'updated_status', 'approved', 'rejected'
            $table->string('auteur'); // Nom de l'utilisateur ayant fait l'action
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_histories');
    }
};
