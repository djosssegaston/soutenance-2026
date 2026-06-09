<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Ajout des contraintes foreign key manquantes avec CASCADE
     * Assure l'intégrité référentielle de la base de données
     */
    public function up(): void
    {
        // Désactier temporairement les vérifications de FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Table project_documents
        Schema::table('project_documents', function (Blueprint $table) {
            if (! $this->hasForeignKey('project_documents', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
        });

        // Table project_validations
        Schema::table('project_validations', function (Blueprint $table) {
            if (! $this->hasForeignKey('project_validations', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('project_validations', 'admin_id')) {
                $table->foreign('admin_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            }
        });

        // Table institution_analyses
        Schema::table('institution_analyses', function (Blueprint $table) {
            if (! $this->hasForeignKey('institution_analyses', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('institution_analyses', 'institution_id')) {
                $table->foreign('institution_id')
                    ->references('id')->on('institutions')
                    ->onDelete('cascade');
            }
        });

        // Table financements
        Schema::table('financements', function (Blueprint $table) {
            if (! $this->hasForeignKey('financements', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('financements', 'institution_id')) {
                $table->foreign('institution_id')
                    ->references('id')->on('institutions')
                    ->onDelete('cascade');
            }
        });

        // Table remboursements
        Schema::table('remboursements', function (Blueprint $table) {
            if (! $this->hasForeignKey('remboursements', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
        });

        // Table remboursement_confirmations
        Schema::table('remboursement_confirmations', function (Blueprint $table) {
            if (! $this->hasForeignKey('remboursement_confirmations', 'remboursement_id')) {
                $table->foreign('remboursement_id')
                    ->references('id')->on('remboursements')
                    ->onDelete('cascade');
            }
        });

        // Table conversations
        Schema::table('conversations', function (Blueprint $table) {
            if (! $this->hasForeignKey('conversations', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
        });

        // Table conversation_participants
        Schema::table('conversation_participants', function (Blueprint $table) {
            if (! $this->hasForeignKey('conversation_participants', 'conversation_id')) {
                $table->foreign('conversation_id')
                    ->references('id')->on('conversations')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('conversation_participants', 'user_id')) {
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            }
        });

        // Table messages
        Schema::table('messages', function (Blueprint $table) {
            if (! $this->hasForeignKey('messages', 'conversation_id')) {
                $table->foreign('conversation_id')
                    ->references('id')->on('conversations')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('messages', 'sender_id')) {
                $table->foreign('sender_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            }
        });

        // Table notifications
        Schema::table('notifications', function (Blueprint $table) {
            if (! $this->hasForeignKey('notifications', 'user_id')) {
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            }
            if (Schema::hasColumn('notifications', 'project_id')) {
                if (! $this->hasForeignKey('notifications', 'project_id')) {
                    $table->foreign('project_id')
                        ->references('id')->on('projects')
                        ->onDelete('set null');
                }
            }
        });

        // Table audit_logs
        Schema::table('audit_logs', function (Blueprint $table) {
            if (! $this->hasForeignKey('audit_logs', 'user_id')) {
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('set null');
            }
        });

        // Table disputes
        Schema::table('disputes', function (Blueprint $table) {
            if (! $this->hasForeignKey('disputes', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('disputes', 'user_id')) {
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('set null');
            }
        });

        // Table project_comments
        Schema::table('project_comments', function (Blueprint $table) {
            if (! $this->hasForeignKey('project_comments', 'project_id')) {
                $table->foreign('project_id')
                    ->references('id')->on('projects')
                    ->onDelete('cascade');
            }
            if (! $this->hasForeignKey('project_comments', 'user_id')) {
                $table->foreign('user_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade');
            }
        });

        // Réactiver les vérifications de FK
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tables = [
            'project_documents' => ['project_id'],
            'project_validations' => ['project_id', 'admin_id'],
            'institution_analyses' => ['project_id', 'institution_id'],
            'financements' => ['project_id', 'institution_id'],
            'remboursements' => ['project_id'],
            'remboursement_confirmations' => ['remboursement_id'],
            'conversations' => ['project_id'],
            'conversation_participants' => ['conversation_id', 'user_id'],
            'messages' => ['conversation_id', 'sender_id'],
            'notifications' => ['user_id', 'project_id'],
            'audit_logs' => ['user_id'],
            'disputes' => ['project_id', 'user_id'],
            'project_comments' => ['project_id', 'user_id'],
        ];

        foreach ($tables as $table => $columns) {
            foreach ($columns as $column) {
                Schema::table($table, function (Blueprint $table) use ($column) {
                    $table->dropForeign([$column]);
                });
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Vérifier si une foreign key existe déjà
     */
    private function hasForeignKey(string $table, string $column): bool
    {
        $tableName = config('database.connections.mysql.prefix').$table;

        $foreignKeys = DB::select("
            SELECT COUNT(*) as count
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = '{$tableName}'
            AND COLUMN_NAME = '{$column}'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        return $foreignKeys[0]->count > 0;
    }
};
