<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Secteur;
use Illuminate\Http\Request;

class AdminSecteurController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'secteurs' => Secteur::ordered()->get(),
        ]);
    }

    public function active()
    {
        return response()->json([
            'success' => true,
            'secteurs' => Secteur::active()->ordered()->get(['id', 'nom', 'description', 'icone']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom' => 'required|string|max:255|unique:secteurs,nom',
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'order_column' => 'nullable|integer|min:0',
        ]);

        $secteur = Secteur::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Secteur créé.',
            'secteur' => $secteur,
        ], 201);
    }

    public function show(Secteur $secteur)
    {
        return response()->json([
            'success' => true,
            'secteur' => $secteur->load('documentRules'),
        ]);
    }

    public function update(Request $request, Secteur $secteur)
    {
        $data = $request->validate([
            'nom' => 'sometimes|required|string|max:255|unique:secteurs,nom,'.$secteur->id,
            'description' => 'nullable|string',
            'icone' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'order_column' => 'nullable|integer|min:0',
        ]);

        $secteur->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Secteur mis à jour.',
            'secteur' => $secteur,
        ]);
    }

    public function destroy(Secteur $secteur)
    {
        $secteur->delete();

        return response()->json([
            'success' => true,
            'message' => 'Secteur supprimé.',
        ]);
    }
}
