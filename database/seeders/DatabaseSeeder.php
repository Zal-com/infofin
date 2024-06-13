<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\RolesPermissions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesPermissions::class,
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'email' => 'axel.hoffmann@ulb.be',
            'password' => "Test123*",
        ]);

        // Récupérer le rôle 'contributor'
        $contributorRole = Role::where('name', 'admin')->first();

        // Assigner le rôle 'contributor' à l'utilisateur
        $user->assignRole($contributorRole);
    }
}
