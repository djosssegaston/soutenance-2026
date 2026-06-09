<?php

namespace App\Http\Controllers;

use App\Models\DocumentRule;
use App\Models\KycDocument;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    public function showForm()
    {
        return view('auth.inscription');
    }

    public function register(Request $request)
    {
        $porteurType = $request->input('porteur_type', 'personnel');

        $baseRules = [
            'porteur_type' => ['required', Rule::in(['personnel', 'entreprise'])],

            // Step 1 - Account (common)
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'telephone' => ['required', 'string', 'max:20', 'unique:users', 'regex:/^\+[1-9]\d{6,15}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:porteur'],

            // Step 2 - Location
            'pays_id' => ['required', 'integer', 'exists:pays,id'],
            'departement_id' => ['required', 'integer', 'exists:departements,id'],
            'commune_id' => ['required', 'integer', 'exists:communes,id'],
            'arrondissement_id' => ['nullable', 'integer', 'exists:arrondissements,id'],
            'arrondissement_nom' => ['nullable', 'string', 'max:255'],
            'quartier_id' => ['nullable', 'integer', 'exists:quartiers,id'],
            'quartier_nom' => ['nullable', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:500'],

            // Step 3 - KYC identity (contact person ID)
            'type_piece' => ['required', 'string', 'in:cni,passeport,cip'],
            'numero_piece' => ['required', 'string', 'max:50', 'unique:kyc_documents,numero_piece'],
            'date_expiration' => ['required', 'date', 'after:today'],
        ];

        if ($porteurType === 'entreprise') {
            $baseRules['entreprise_nom'] = ['required', 'string', 'max:255'];
            $baseRules['entreprise_secteur'] = ['required', 'string', 'max:255'];
            $baseRules['activite'] = ['required', 'string', 'max:255'];
            $baseRules['name'] = ['required', 'string', 'max:255'];
        } else {
            $baseRules['prenom'] = ['required', 'string', 'max:255'];
            $baseRules['name'] = ['required', 'string', 'max:255'];
            $baseRules['sexe'] = ['nullable', 'string', 'in:homme,femme'];
            $baseRules['date_naissance'] = ['nullable', 'date', 'before:today'];
        }

        $baseMessages = [
            'date_expiration.after' => 'La piece doit etre en cours de validite.',
            'numero_piece.unique' => 'Ce numéro de pièce d\'identité est déjà utilisé par un autre compte.',
        ];

        // Dynamic document rules based on porteur type
        $acteur = $porteurType === 'entreprise' ? 'porteur_entreprise' : 'porteur';
        $docRules = DocumentRule::active()->forContext('registration')->where('acteur', $acteur)->get();
        $docValidations = [];
        $docMessages = [];
        foreach ($docRules as $rule) {
            $fieldRules = ['required', 'file'];
            if ($rule->types_mime) {
                $mimes = array_map('trim', explode(',', $rule->types_mime));
                $fieldRules[] = 'mimes:'.implode(',', $mimes);
                $docMessages[$rule->slug.'.mimes'] = 'Le fichier '.$rule->label.' doit être au format : '.implode(', ', $mimes).'.';
            }
            if ($rule->max_size) {
                $fieldRules[] = 'max:'.($rule->max_size * 1024);
                $docMessages[$rule->slug.'.max'] = 'Le fichier '.$rule->label.' ne doit pas dépasser '.$rule->max_size.' Mo.';
            }
            $docMessages[$rule->slug.'.required'] = 'Le fichier '.$rule->label.' est obligatoire.';
            $docValidations[$rule->slug] = $fieldRules;
        }

        $validated = $request->validate(
            array_merge($baseRules, $docValidations),
            array_merge($baseMessages, $docMessages)
        );

        // Ensure at least one of id/nom is provided for arrondissement and quartier
        $errors = [];
        if (! $request->filled('arrondissement_id') && ! $request->filled('arrondissement_nom')) {
            $errors['arrondissement_id'] = 'Veuillez sélectionner ou saisir un arrondissement.';
        }
        if (! $request->filled('quartier_id') && ! $request->filled('quartier_nom')) {
            $errors['quartier_id'] = 'Veuillez sélectionner ou saisir un quartier.';
        }
        if (! empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Resolve free-text arrondissement/quartier to IDs
        if ($request->filled('arrondissement_nom') && ! $request->filled('arrondissement_id')) {
            $arrondissement = \App\Models\Arrondissement::firstOrCreate(
                ['nom' => $request->arrondissement_nom, 'commune_id' => $validated['commune_id']],
                ['nom' => $request->arrondissement_nom, 'commune_id' => $validated['commune_id']]
            );
            $validated['arrondissement_id'] = $arrondissement->id;
        }
        if ($request->filled('quartier_nom') && ! $request->filled('quartier_id')) {
            $arrondissementId = $validated['arrondissement_id'] ?? null;
            $quartier = \App\Models\Quartier::firstOrCreate(
                ['nom' => $request->quartier_nom, 'arrondissement_id' => $arrondissementId],
                ['nom' => $request->quartier_nom, 'arrondissement_id' => $arrondissementId]
            );
            $validated['quartier_id'] = $quartier->id;
        }

        try {
            DB::beginTransaction();

            $userData = [
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'password' => Hash::make($validated['password']),
                'role' => 'porteur',
                'porteur_type' => $porteurType,
                'statut' => 'actif',
                'pays_id' => $validated['pays_id'],
                'departement_id' => $validated['departement_id'],
                'commune_id' => $validated['commune_id'],
                'arrondissement_id' => $validated['arrondissement_id'],
                'quartier_id' => $validated['quartier_id'],
                'adresse' => $validated['adresse'],
            ];

            if ($porteurType === 'entreprise') {
                $userData['name'] = $validated['name'];
                $userData['entreprise_nom'] = $validated['entreprise_nom'];
                $userData['entreprise_secteur'] = $validated['entreprise_secteur'];
                $userData['activite'] = $validated['activite'];
            } else {
                $userData['prenom'] = $validated['prenom'];
                $userData['name'] = $validated['name'];
                $userData['sexe'] = $validated['sexe'] ?? null;
                $userData['date_naissance'] = $validated['date_naissance'] ?? null;
            }

            $user = User::create($userData);

            $docPaths = [];
            foreach ($docRules as $rule) {
                if ($request->hasFile($rule->slug)) {
                    $docPaths[$rule->slug] = $request->file($rule->slug)->store('kyc/'.$user->id, 'secure_documents');
                }
            }

            KycDocument::create([
                'user_id' => $user->id,
                'type_piece' => $validated['type_piece'],
                'numero_piece' => $validated['numero_piece'],
                'date_expiration' => $validated['date_expiration'],
                'recto_path' => $docPaths['recto'] ?? null,
                'verso_path' => $docPaths['verso'] ?? null,
                'selfie_path' => $docPaths['selfie'] ?? null,
                'statut' => 'en_attente',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Registration failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la creation du compte. Veuillez reessayer.']);
        }

        event(new Registered($user));

        return redirect()->route('login')
            ->with('status', 'Compte cree avec succes ! Verifiez votre email pour confirmer votre inscription.');
    }
}
