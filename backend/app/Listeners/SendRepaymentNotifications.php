<?php

namespace App\Listeners;

use App\Events\RepaymentRecorded;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\RepaymentRecordedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendRepaymentNotifications implements ShouldQueue
{
    public function handle(RepaymentRecorded $event): void
    {
        $repayment = $event->repayment;
        $project = $repayment->project;

        if (! $project) {
            return;
        }

        $adminUsers = User::where('role', 'admin')->get();
        $porteur = $project->owner;
        $institutions = $project->financements()->with('institution.user')->get()->pluck('institution.user')->filter();

        $recipients = $adminUsers->merge($institutions);
        if ($porteur) {
            $recipients->push($porteur);
        }

        foreach ($recipients->unique('id') as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'remboursement_enregistre',
                'title' => 'Remboursement enregistré',
                'content' => 'Un remboursement a été enregistré pour le projet "'.$project->titre.'".',
                'is_read' => false,
            ]);

            if (! empty($user->email)) {
                $user->notify(new RepaymentRecordedNotification($repayment));
            }
        }
    }
}
