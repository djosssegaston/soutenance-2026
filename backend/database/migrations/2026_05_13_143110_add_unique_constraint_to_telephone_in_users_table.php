<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE users AS u
            JOIN (
                SELECT MIN(id) AS id, telephone
                FROM users
                WHERE telephone IS NOT NULL
                GROUP BY telephone
                HAVING COUNT(*) > 1
            ) AS dup ON u.telephone = dup.telephone AND u.id != dup.id
            SET u.telephone = NULL
        ');

        Schema::table('users', function (Blueprint $table) {
            $table->unique('telephone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['telephone']);
        });
    }
};
