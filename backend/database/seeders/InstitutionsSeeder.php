<?php

namespace Database\Seeders;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Database\Seeder;

class InstitutionsSeeder extends Seeder
{
    public function run(): void
    {
        $institutionUsers = User::where('role', 'institution')->get();

        foreach ($institutionUsers as $user) {
            Institution::factory()->create([
                'user_id' => $user->id,
                'nom' => $user->name,
                'email' => $user->email,
            ]);
        }
    }
}
