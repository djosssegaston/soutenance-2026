<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use Illuminate\Http\Request;

class InstitutionController extends Controller
{
    public function index()
    {
        return response()->json(Institution::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|integer|exists:users,id',
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'statut' => 'nullable|string',
        ]);

        $institution = Institution::create($data);

        return response()->json($institution, 201);
    }

    public function show(Institution $institution)
    {
        return response()->json($institution);
    }

    public function update(Request $request, Institution $institution)
    {
        $data = $request->validate([
            'nom' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string|max:30',
            'adresse' => 'nullable|string|max:255',
            'statut' => 'nullable|string',
        ]);

        $institution->update($data);

        return response()->json($institution);
    }

    public function destroy(Institution $institution)
    {
        $institution->delete();

        return response()->json(['message' => 'Supprime']);
    }
}
