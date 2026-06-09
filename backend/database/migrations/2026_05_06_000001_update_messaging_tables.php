<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Update conversations table
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->nullOnDelete();
            $table->foreignId('porteur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('active'); // active, closed
            $table->string('last_message_at')->nullable();
        });

        // Update messages table
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender_role')->nullable(); // porteur, institution
            $table->string('type')->default('texte'); // texte, image, audio, document
            $table->string('file_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('created_at')->nullable()->change();
        });

        // Update notifications table
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->nullable()->after('type');
            $table->renameColumn('contenu', 'content');
            $table->renameColumn('lu', 'is_read');
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('content', 'contenu');
            $table->renameColumn('is_read', 'lu');
            $table->dropColumn('title');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['sender_role', 'type', 'file_path', 'is_read']);
            $table->timestamp('created_at')->nullable(false)->change();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
            $table->dropConstrainedForeignId('porteur_id');
            $table->dropColumn(['status', 'last_message_at']);
        });
    }
};
