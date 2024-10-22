<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the RolesPermissions seeder first
        $this->call(RolesPermissions::class);

        // Create the user
        $user = User::create([
            'email' => 'axel.hoffmann@ulb.be',
            "first_name" => 'Axel',
            "last_name" => "Hoffmann",
            "uid" => "ahof0006",
        ]);

        // Retrieve the role 'admin'
        $adminRole = Role::where('name', 'admin')->first();

        // Assign the 'admin' role to the user
        $user->assignRole($adminRole);
    }
}
