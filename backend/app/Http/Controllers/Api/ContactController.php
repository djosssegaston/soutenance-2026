<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $adminEmail = Setting::where('key', 'contact_email')->value('value') ?? 'contact@alogoto.bj';

        try {
            Mail::raw(
                "Nom : {$validated['name']}\n".
                "Email : {$validated['email']}\n".
                'Téléphone : '.($validated['phone'] ?? 'Non renseigné')."\n".
                'Sujet : '.($validated['subject'] ?? 'Non renseigné')."\n\n".
                "Message :\n{$validated['message']}",
                function ($message) use ($adminEmail, $validated) {
                    $message->to($adminEmail)
                        ->subject('Nouveau message de contact - '.($validated['subject'] ?? 'Sans objet'));
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Votre message a été envoyé avec succès.',
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.',
            ], 500);
        }
    }
}
