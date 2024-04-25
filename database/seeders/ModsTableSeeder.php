<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // Check if users table is empty
        if (User::count() === 0) {
            $this->call(UsersTableSeeder::class); // Run the UserTableSeeder to create users
        }
        // Check if users table is empty
        if (Game::count() === 0) {
            $this->call(GameTableSeeder::class); // Run the UserTableSeeder to create users
        }

        // Create games for each user
        $users = User::all();
        $games = Game::all();

        foreach ($users as $user) {
            foreach ($games as $game) {
                Mod::create([
                    'name' => 'Sample Mod',
                    'user_id' => $user->id,
                    'game_id' => $game->id,
                ]);
            }
        }
    }
}
