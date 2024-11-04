<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesPermissions extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Roles

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            Role::create(['name' => 'admin']);
        }

        $contributorRole = Role::where('name', 'contributor')->first();

        if (!$contributorRole) {
            $contributor = Role::create(['name' => 'contributor']);

            $contributorPermissions = [
                Permission::create(['name' => 'create project']),
                Permission::create(['name' => 'edit own project']),
                Permission::create(['name' => 'delete own project']),
                Permission::create(['name' => 'view own project']),
                Permission::create(['name' => 'view other project']),
                Permission::create(['name' => 'edit other project']),
                Permission::create(['name' => 'delete other project']),
                Permission::create(['name' => 'edit own interests']),
                Permission::create(['name' => 'edit own draft']),
                Permission::create(['name' => 'create draft']),
                Permission::create(['name' => 'delete own draft']),
                Permission::create(['name' => 'delete other draft']),
                Permission::create(['name' => 'view own draft']),
                Permission::create(['name' => 'view other draft']),
                Permission::create(['name' => 'view archives']),
                Permission::create(['name' => 'create collection']),
                Permission::create(['name' => 'edit own collection']),
                Permission::create(['name' => 'edit other collection']),
                Permission::create(['name' => 'delete other collection']),
                Permission::create(['name' => 'delete own collection']),
            ];

            //Permissions
            Permission::create(['name' => 'edit other interests']);

            //Assignation
            $contributor->givePermissionTo($contributorPermissions);
        }

    }
}
