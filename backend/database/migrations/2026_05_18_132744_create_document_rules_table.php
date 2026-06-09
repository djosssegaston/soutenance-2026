<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('secteur_id')->nullable()->constrained('secteurs')->nullOnDelete();
            $table->string('label');
            $table->string('slug')->unique();
            $table->boolean('obligatoire')->default(true);
            $table->string('acteur')->default('porteur');
            $table->string('types_mime')->default('pdf,jpg,jpeg,png,doc,docx');
            $table->unsignedInteger('max_size')->default(10240);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('order_column')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_rules');
    }
};
