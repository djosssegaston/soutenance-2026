<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        AuditLog::factory()->count(6)->create([
            'user_id' => $admin ? $admin->id : null,
        ]);

        $project = Project::first();
        if ($project && $admin) {
            AuditLog::create([
                'user_id' => $admin->id,
                'action' => 'Validation du projet '.$project->id,
                'ip' => '102.54.18.12',
                'date' => now(),
            ]);
        }
    }
}
