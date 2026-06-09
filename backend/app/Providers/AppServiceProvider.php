<?php

namespace App\Providers;

use App\Events\RepaymentRecorded;
use App\Listeners\SendRepaymentNotifications;
use App\Models\Project;
use App\Policies\ProjectPolicy;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($notifiable) {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify.api',
                now()->addMinutes(60),
                ['id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification())]
            );

            return (new MailMessage)
                ->subject('Confirmez votre compte Alogoto')
                ->view('emails.verify-email', [
                    'userName' => $notifiable->name,
                    'verificationUrl' => $verificationUrl,
                    'appName' => config('app.name'),
                    'logoUrl' => config('app.frontend_logo_url'),
                    'supportEmail' => config('mail.from.address'),
                ]);
        });

        Gate::policy(Project::class, ProjectPolicy::class);

        Event::listen(
            RepaymentRecorded::class,
            SendRepaymentNotifications::class
        );
    }
}
