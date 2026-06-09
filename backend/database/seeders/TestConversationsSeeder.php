<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\Institution;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Carbon;

class TestConversationsSeeder extends \Illuminate\Database\Seeder
{
    public function run(): void
    {
        $porteur = User::where('role', 'porteur')->first();
        $institutions = Institution::take(3)->get();
        $projects = Project::where('user_id', $porteur->id)->take(3)->get();

        if (! $porteur || $institutions->count() < 2 || $projects->count() < 2) {
            echo "Données insuffisantes pour créer les conversations de test.\n";

            return;
        }

        // Conversation 2 avec 2ème institution
        $inst2 = $institutions[1];
        $proj1 = $projects[0];
        $conv2 = Conversation::create([
            'project_id' => $proj1->id,
            'institution_id' => $inst2->id,
            'porteur_id' => $porteur->id,
            'type' => 'porteur_institution',
            'status' => 'active',
            'last_message_at' => Carbon::now()->subDays(2),
        ]);

        $messages2 = [
            ['sender' => $inst2->user_id, 'role' => 'institution', 'msg' => 'Bonjour, nous avons validé votre projet "'.$proj1->titre.'". Pouvons-nous échanger ?'],
            ['sender' => $porteur->id, 'role' => 'porteur', 'msg' => 'Bonjour, oui bien sûr. Qu\'aimeriez-vous savoir ?'],
            ['sender' => $inst2->user_id, 'role' => 'institution', 'msg' => 'Nous souhaiterions des précisions sur votre modèle financier.'],
            ['sender' => $porteur->id, 'role' => 'porteur', 'msg' => 'Bien sûr, le modèle repose sur 3 piliers : croissance, rentabilité et impact social.'],
        ];

        foreach ($messages2 as $m) {
            Message::create([
                'conversation_id' => $conv2->id,
                'sender_id' => $m['sender'],
                'sender_role' => $m['role'],
                'message' => $m['msg'],
                'type' => 'texte',
                'is_read' => false,
            ]);
        }

        // Conversation 3 avec 3ème institution
        if ($institutions->count() >= 3 && $projects->count() >= 3) {
            $inst3 = $institutions[2];
            $proj2 = $projects[2];
            $conv3 = Conversation::create([
                'project_id' => $proj2->id,
                'institution_id' => $inst3->id,
                'porteur_id' => $porteur->id,
                'type' => 'porteur_institution',
                'status' => 'active',
                'last_message_at' => Carbon::now()->subDays(5),
            ]);

            $messages3 = [
                ['sender' => $inst3->user_id, 'role' => 'institution', 'msg' => 'Votre projet "'.$proj2->titre.'" présente un fort potentiel.'],
                ['sender' => $porteur->id, 'role' => 'porteur', 'msg' => 'Merci beaucoup ! Nous avons hâte de collaborer avec vous.'],
                ['sender' => $inst3->user_id, 'role' => 'institution', 'msg' => 'Pourriez-vous nous envoyer le business plan complet ?'],
            ];

            foreach ($messages3 as $m) {
                Message::create([
                    'conversation_id' => $conv3->id,
                    'sender_id' => $m['sender'],
                    'sender_role' => $m['role'],
                    'message' => $m['msg'],
                    'type' => 'texte',
                    'is_read' => false,
                ]);
            }
        }

        echo "Conversations de test créées avec succès !\n";
        echo 'Total conversations: '.Conversation::count()."\n";
        echo 'Total messages: '.Message::count()."\n";
    }
}
