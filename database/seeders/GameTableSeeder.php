<?php

namespace Database\Seeders;

use App\Models\Game;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GameTableSeeder extends Seeder
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

        // Create games for each user
        $users = User::all();
        foreach ($users as $user) {
            Game::create([
                'name' => 'Sample Game',
                'user_id' => $user->id,
            ]);
        }
    }
}
