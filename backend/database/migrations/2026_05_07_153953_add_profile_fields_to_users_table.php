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
        Schema::table('users', function (Blueprint $table) {
            $table->string('prenom')->nullable()->after('name');
            $table->string('avatar_url')->nullable()->after('email_verified_at');
            $table->text('adresse')->nullable()->after('telephone');
            $table->string('activite')->nullable()->after('adresse');
            $table->string('entreprise_nom')->nullable()->after('activite');
            $table->string('entreprise_secteur')->nullable()->after('entreprise_nom');
            $table->timestamp('telephone_verified_at')->nullable()->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'prenom',
                'avatar_url',
                'adresse',
                'activite',
                'entreprise_nom',
                'entreprise_secteur',
                'telephone_verified_at',
            ]);
        });
    }
};
