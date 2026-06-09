<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DocumentRule;
use Illuminate\Http\Request;

class AdminDocumentRuleController extends Controller
{
    public function index()
    {
        $query = DocumentRule::with('secteur');

        if (request()->has('context')) {
            $query->forContext(request('context'));
        }

        if (request()->has('acteur')) {
            $query->forRole(request('acteur'));
        }

        return response()->json([
            'success' => true,
            'rules' => $query->ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'context' => 'nullable|string|in:project,registration',
            'secteur_id' => 'nullable|exists:secteurs,id',
            'label' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:document_rules,slug',
            'obligatoire' => 'nullable|boolean',
            'acteur' => 'nullable|string|in:porteur,porteur_entreprise,institution,admin',
            'types_mime' => 'nullable|string|max:500',
            'max_size' => 'nullable|integer|min:1|max:102400',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'order_column' => 'nullable|integer|min:0',
        ]);

        $rule = DocumentRule::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Règle de document créée.',
            'rule' => $rule->load('secteur'),
        ], 201);
    }

    public function show(DocumentRule $documentRule)
    {
        return response()->json([
            'success' => true,
            'rule' => $documentRule->load('secteur'),
        ]);
    }

    public function update(Request $request, DocumentRule $documentRule)
    {
        $data = $request->validate([
            'context' => 'nullable|string|in:project,registration',
            'secteur_id' => 'nullable|exists:secteurs,id',
            'label' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:document_rules,slug,'.$documentRule->id,
            'obligatoire' => 'nullable|boolean',
            'acteur' => 'nullable|string|in:porteur,porteur_entreprise,institution,admin',
            'types_mime' => 'nullable|string|max:500',
            'max_size' => 'nullable|integer|min:1|max:102400',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'order_column' => 'nullable|integer|min:0',
        ]);

        $documentRule->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Règle de document mise à jour.',
            'rule' => $documentRule->fresh()->load('secteur'),
        ]);
    }

    public function destroy(DocumentRule $documentRule)
    {
        $documentRule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Règle de document supprimée.',
        ]);
    }
}
