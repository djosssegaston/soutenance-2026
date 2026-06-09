<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financement_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financement_id')->constrained('financements')->cascadeOnDelete();
            $table->string('action'); // proposition, validation, decaissement, refus, suspension
            $table->string('auteur');
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financement_histories');
    }
};
