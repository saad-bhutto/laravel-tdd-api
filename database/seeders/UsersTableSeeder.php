<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = DB::table('users')->insertGetId([
            'name' => 'Game Mod',
            'email' => User::ADMIN_EMAIL,
            'password' => Hash::make('password')
        ]);


         User::factory(10)->create();
         User::all()->map(fn($user) => $this->createToken($user->id));

    }

    private function createToken($userId): string
    {
        $token = User::find($userId)->createToken('my-app-token')->plainTextToken;
        return $token;
    }

}