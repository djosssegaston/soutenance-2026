<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\InstitutionAnalysis;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Database\Seeder;

class MessagesSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::with('owner')->take(4)->get();

        foreach ($projects as $project) {
            $analysis = InstitutionAnalysis::where('project_id', $project->id)->first();
            if (! $analysis) {
                continue;
            }

            $institutionUser = $analysis->institution->user;
            $porteur = $project->owner;

            if (! $institutionUser || ! $porteur) {
                continue;
            }

            $conversation = Conversation::factory()->create([
                'project_id' => $project->id,
                'type' => 'porteur_institution',
            ]);

            ConversationParticipant::factory()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $porteur->id,
                'role' => 'porteur',
            ]);

            ConversationParticipant::factory()->create([
                'conversation_id' => $conversation->id,
                'user_id' => $institutionUser->id,
                'role' => 'institution',
            ]);

            $messages = [
                'Bonjour, pouvons-nous planifier un entretien la semaine prochaine ?',
                'Oui, le dossier est complet. Nous proposons mardi 10h.',
                'Merci, je confirme pour mardi 10h.',
            ];

            foreach ($messages as $index => $text) {
                $sender = $index % 2 === 0 ? $porteur : $institutionUser;
                $receiver = $index % 2 === 0 ? $institutionUser : $porteur;
                Message::factory()->create([
                    'conversation_id' => $conversation->id,
                    'sender_id' => $sender->id,
                    'receiver_id' => $receiver->id,
                    'message' => $text,
                ]);
            }
        }
    }
}
