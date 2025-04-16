<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminTeam = Team::factory(10)->create([
            'name' => 'Administradores',
        ]);

        $adminUser = User::factory()->create([
           'name' => 'Administrador',
           'email' => 'admin@admin.com',
           'password' => bcrypt('admin123'),
        ]);

        $adminUser->team()->attach($adminTeam);

        // Cria 3 times com 3 usuários cada
        Team::factory()->count(3)->create()->each(function ($team) {
            $users = User::factory()->count(3)->create();
            $team->users()->attach($users);
        });
    }
}
