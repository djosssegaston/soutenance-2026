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
            if (! Schema::hasColumn('conversations', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
        });

        // Update messages table
        Schema::table('messages', function (Blueprint $table) {
            if (! Schema::hasColumn('messages', 'receiver_id')) {
                $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('messages', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }
            if (! Schema::hasColumn('messages', 'audio_duration')) {
                $table->integer('audio_duration')->nullable();
            }
            if (! Schema::hasColumn('messages', 'is_edited')) {
                $table->boolean('is_edited')->default(false);
            }
            if (! Schema::hasColumn('messages', 'is_deleted')) {
                $table->boolean('is_deleted')->default(false);
            }
            if (! Schema::hasColumn('messages', 'reply_to')) {
                $table->foreignId('reply_to')->nullable()->constrained('messages')->nullOnDelete();
            }
        });

        // Create message_attachments table
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('mime_type');
            $table->timestamps();
        });

        // Create message_audits table
        Schema::create('message_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('messages')->cascadeOnDelete();
            $table->string('action'); // edit, delete
            $table->foreignId('performed_by')->constrained('users')->cascadeOnDelete();
            $table->text('details')->nullable();
            $table->timestamps();
        });

        // Create realtime_notifications table (extending existing if needed, but the user asked for a new one)
        // We will keep existing 'notifications' table but create this one as requested for specific realtime data
        Schema::create('realtime_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('realtime_notifications');
        Schema::dropIfExists('message_audits');
        Schema::dropIfExists('message_attachments');

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['receiver_id', 'mime_type', 'audio_duration', 'is_edited', 'is_deleted', 'reply_to']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('created_by');
        });
    }
};
