<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $adminTeam = Team::factory()->create([
            'name' => 'Administradores',
        ]);

        $adminUser = User::factory()->create([
           'name' => 'Administrador',
           'email' => 'admin@admin.com',
           'password' => bcrypt('admin123'),
            'team_id' => $adminTeam->first()->id,
        ]);

        $normalUser = User::factory()->create([
            'name' => 'User',
            'email' => 'user@user.com',
            'password' => bcrypt('user123'),
            'team_id' => $adminTeam->first()->id,
        ]);

        $adminRole = Role::factory()->create(['name' => 'admin']);
        $userRole = Role::factory()->create(['name' => 'user']);

        $managerPermission = Permission::factory()->create(['name' => 'administrator']);
        $viewPermission = Permission::factory()->create(['name' => 'viewer']);


        $adminRole->permissions()->attach($managerPermission->id);
        $userRole->permissions()->attach($viewPermission->id);

        $normalUser->roles()->attach($userRole);
        $adminUser->roles()->attach($adminRole);

        $adminTeam->permissions()->attach($managerPermission->id);

//        // Cria 3 times com 3 usuários cada
//        Team::factory()->count(3)->create()->each(function ($team) {
//            $users = User::factory()->count(3)->create();
//            $team->users()->attach($users);
//        });


    }
}
