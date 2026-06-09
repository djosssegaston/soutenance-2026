<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            UserNotification::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        }

        $project = Project::first();
        if ($project) {
            $admin = User::where('role', 'admin')->first();
            $porteur = $project->owner;

            if ($admin) {
                UserNotification::create([
                    'user_id' => $admin->id,
                    'type' => 'validation_projet',
                    'contenu' => 'Le projet '.$project->titre.' a ete valide.',
                    'lu' => false,
                ]);
            }

            if ($porteur) {
                UserNotification::create([
                    'user_id' => $porteur->id,
                    'type' => 'financement_recu',
                    'contenu' => 'Votre projet '.$project->titre.' a recu un financement.',
                    'lu' => false,
                ]);
            }
        }
    }
}
