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
        Schema::table('kyc_documents', function (Blueprint $table) {
            $table->string('recto_path', 255)->nullable()->change();
            $table->string('verso_path', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('kyc_documents', function (Blueprint $table) {
            $table->string('recto_path', 255)->nullable(false)->change();
            $table->string('verso_path', 255)->nullable(false)->change();
        });
    }
};
