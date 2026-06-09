<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('action');
            $table->string('color')->nullable()->after('icon');
            $table->string('type')->default('info')->after('color'); // info, success, warning, danger
        });
    }

    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn(['icon', 'color', 'type']);
        });
    }
};
