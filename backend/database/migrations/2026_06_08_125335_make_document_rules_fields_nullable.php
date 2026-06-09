<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_rules', function (Blueprint $table) {
            $table->string('types_mime')->nullable()->change();
            $table->unsignedInteger('max_size')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('document_rules', function (Blueprint $table) {
            $table->string('types_mime')->default('pdf,jpg,jpeg,png,doc,docx')->nullable(false)->change();
            $table->unsignedInteger('max_size')->default(10240)->nullable(false)->change();
        });
    }
};
