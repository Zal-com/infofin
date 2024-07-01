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
        Role::create(['name' => 'admin']);
        $contributor = Role::create(['name' => 'contributor']);

        //Permissions
        $createProjects = Permission::create(['name' => 'create projects']);
        $editOwnProject = Permission::create(['name' => 'edit own project']);
        $deleteOwnProject = Permission::create(['name' => 'delete own project']);

        //Assignation
        $contributor->givePermissionTo($createProjects);
        $contributor->givePermissionTo($editOwnProject);
        $contributor->givePermissionTo($deleteOwnProject);

    }
}
