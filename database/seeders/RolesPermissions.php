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

        if(!$adminRole){
            Role::create(['name' => 'admin']);
        }
        
        $contributorRole = Role::where('name', 'contributor')->first();

        if(!$contributorRole){
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
}
