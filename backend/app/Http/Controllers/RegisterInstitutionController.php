<?php

namespace App\Http\Controllers;

use App\Models\DocumentRule;
use App\Models\Institution;
use App\Models\KycDocument;
use App\Models\Pay;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterInstitutionController extends Controller
{
    public function showForm()
    {
        $phoneCountries = Pay::all();

        return view('auth.inscription-imf', compact('phoneCountries'));
    }

    public function register(Request $request)
    {
        $baseRules = [
            // Step 1 - General info
            'nom_officiel' => ['required', 'string', 'max:255'],
            'sigle' => ['nullable', 'string', 'max:50'],
            'type_institution' => ['required', 'string', 'in:association,mutuelle,cooperative,ong,fondation,autre'],
            'rccm' => ['required', 'string', 'max:50'],
            'ifu' => ['required', 'string', 'max:50'],
            'date_creation' => ['required', 'date', 'before:today'],
            'agrement' => ['required', 'string', 'max:50'],
            'secteur' => ['required', 'string', 'in:microfinance,finance,agriculture,education,sante,social,environnement,autre'],
            'description' => ['required', 'string', 'max:2000'],

            // Step 2 - Location
            'pays_id' => ['required', 'integer', 'exists:pays,id'],
            'departement_id' => ['required', 'integer', 'exists:departements,id'],
            'commune_id' => ['required', 'integer', 'exists:communes,id'],
            'arrondissement_id' => ['nullable', 'integer', 'exists:arrondissements,id'],
            'arrondissement_nom' => ['nullable', 'string', 'max:255'],
            'quartier_id' => ['nullable', 'integer', 'exists:quartiers,id'],
            'quartier_nom' => ['nullable', 'string', 'max:255'],
            'adresse' => ['required', 'string', 'max:500'],

            // Step 3 - Contacts
            'email_officiel' => ['required', 'email', 'max:255'],
            'telephone' => ['required', 'string', 'max:20', 'regex:/^\+[1-9]\d{6,15}$/'],
            'telephone_secondaire' => ['nullable', 'string', 'max:20'],
            'site_web' => ['nullable', 'url', 'max:255'],
            'bp' => ['nullable', 'string', 'max:50'],

            // Step 4 - Responsible
            'resp_prenom' => ['required', 'string', 'max:255'],
            'resp_nom' => ['required', 'string', 'max:255'],
            'resp_fonction' => ['required', 'string', 'max:255'],
            'resp_sexe' => ['required', 'string', 'in:M,F'],
            'resp_date_naissance' => ['required', 'date', 'before:today'],
            'resp_nationalite' => ['required', 'string', 'max:100'],
            'resp_type_piece' => ['required', 'string', 'in:cnib,passeport,permis,autre'],
            'resp_numero_piece' => ['required', 'string', 'max:50', 'unique:kyc_documents,numero_piece'],
            'resp_telephone' => ['required', 'string', 'max:20', 'regex:/^\+[1-9]\d{6,15}$/'],
            'resp_email' => ['required', 'email', 'max:255'],

            // Step 6 - Account
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:institution'],

            // Step 7 - Acceptance
            'accept_cgu' => ['required', 'accepted'],
            'accept_confidentialite' => ['required', 'accepted'],
            'confirm_exactitude' => ['required', 'accepted'],
        ];

        $baseMessages = [
            'date_creation.before' => 'La date de création doit être dans le passé.',
            'resp_date_naissance.before' => 'La date de naissance doit être dans le passé.',
            'accept_cgu.accepted' => 'Vous devez accepter les conditions générales.',
            'accept_confidentialite.accepted' => 'Vous devez accepter la politique de confidentialité.',
            'confirm_exactitude.accepted' => 'Vous devez confirmer l\'exactitude des informations.',
            'resp_numero_piece.unique' => 'Ce numéro de pièce d\'identité est déjà utilisé par un autre compte.',
        ];

        // Step 5 - Dynamic document rules
        $docRules = DocumentRule::active()->forContext('registration')->where('acteur', 'institution')->get();
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

        // Ensure at least one of id/nom for arrondissement and quartier
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

            $user = User::create([
                'name' => $validated['name'],
                'prenom' => $validated['resp_prenom'],
                'sexe' => $validated['resp_sexe'],
                'date_naissance' => $validated['resp_date_naissance'],
                'email' => $validated['email'],
                'telephone' => $validated['resp_telephone'],
                'password' => Hash::make($validated['password']),
                'role' => 'institution',
                'statut' => 'actif',
                'pays_id' => $validated['pays_id'],
                'departement_id' => $validated['departement_id'],
                'commune_id' => $validated['commune_id'],
                'arrondissement_id' => $validated['arrondissement_id'] ?? null,
                'quartier_id' => $validated['quartier_id'] ?? null,
                'adresse' => $validated['adresse'],
            ]);

            // Store uploaded documents dynamically
            $docPaths = [];
            foreach ($docRules as $rule) {
                if ($request->hasFile($rule->slug)) {
                    $docPaths[$rule->slug] = $request->file($rule->slug)->store('institutions/'.$user->id.'/documents', 'public');
                }
            }

            $logoPath = $docPaths['logo_file'] ?? $request->file('logo_file')?->store('institutions/'.$user->id, 'public');

            $institution = Institution::create([
                'user_id' => $user->id,
                'nom' => $validated['nom_officiel'],
                'sigle' => $validated['sigle'] ?? null,
                'type_institution' => $validated['type_institution'],
                'rccm' => $validated['rccm'],
                'ifu' => $validated['ifu'],
                'date_creation' => $validated['date_creation'],
                'agrement' => $validated['agrement'],
                'secteur' => $validated['secteur'],
                'email' => $validated['email_officiel'],
                'telephone' => $validated['telephone'],
                'telephone_secondaire' => $validated['telephone_secondaire'] ?? null,
                'adresse' => $validated['adresse'],
                'bp' => $validated['bp'] ?? null,
                'description' => $validated['description'],
                'site_web' => $validated['site_web'] ?? null,
                'logo' => $logoPath,
                'statut' => 'actif',
                'pays_id' => $validated['pays_id'],
                'departement_id' => $validated['departement_id'],
                'commune_id' => $validated['commune_id'],
                'arrondissement_id' => $validated['arrondissement_id'] ?? null,
                'arrondissement_nom' => $validated['arrondissement_nom'] ?? null,
                'quartier_id' => $validated['quartier_id'] ?? null,
                'quartier_nom' => $validated['quartier_nom'] ?? null,
                'resp_prenom' => $validated['resp_prenom'],
                'resp_nom' => $validated['resp_nom'],
                'resp_fonction' => $validated['resp_fonction'],
                'resp_sexe' => $validated['resp_sexe'],
                'resp_date_naissance' => $validated['resp_date_naissance'],
                'resp_nationalite' => $validated['resp_nationalite'],
                'resp_type_piece' => $validated['resp_type_piece'],
                'resp_numero_piece' => $validated['resp_numero_piece'],
                'resp_telephone' => $validated['resp_telephone'],
                'resp_email' => $validated['resp_email'],
            ]);

            // Store as KYC documents
            KycDocument::create([
                'user_id' => $user->id,
                'type_piece' => $validated['resp_type_piece'],
                'numero_piece' => $validated['resp_numero_piece'],
                'recto_path' => $docPaths['recto_institution'] ?? null,
                'verso_path' => $docPaths['verso_institution'] ?? null,
                'statut' => 'en_attente',
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Institution registration failed: '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if (config('app.debug')) {
                throw $e;
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Une erreur est survenue lors de la création du compte. Veuillez réessayer.']);
        }

        event(new Registered($user));

        return redirect()->route('login.institution')
            ->with('status', 'Compte institution créé avec succès ! Vérifiez votre email pour confirmer votre inscription.');
    }
}
