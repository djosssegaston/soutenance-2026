<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::latest()->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:admin,porteur,institution',
            'telephone' => 'nullable|string|max:30|unique:users,telephone',
            'statut' => 'nullable|string',
        ]);

        $data['password'] = bcrypt($data['password']);

        try {
            $user = User::create($data);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Erreur lors de la création. Numéro de téléphone peut-être déjà utilisé.',
            ], 422);
        }

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'role' => 'sometimes|string|in:admin,porteur,institution',
            'telephone' => [
                'nullable', 'string', 'max:30',
                Rule::unique('users', 'telephone')->ignore($user->id),
            ],
            'statut' => 'nullable|string',
        ]);

        try {
            $user->update($data);
        } catch (\Illuminate\Database\QueryException $e) {
            return response()->json([
                'message' => 'Erreur lors de la mise à jour. Numéro de téléphone peut-être déjà utilisé.',
            ], 422);
        }

        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Supprime']);
    }
}
