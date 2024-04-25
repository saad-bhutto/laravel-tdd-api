<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Game;
use App\Models\Mod;
use App\Models\User;
use Database\Seeders\GameTableSeeder;
use Database\Seeders\ModsTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Http\Response;
use Tests\TestCase;

class ModTest extends TestCase
{

    use RefreshDatabase;
    public function testBrowseSucceeds(): void
    {
        $this->seed(ModsTableSeeder::class);
        //  in order for this test to pass, you will need to seed at least 1 game
        //  and 1 mod
        $game = Game::inRandomOrder()->first();
        $mod = Mod::query()->where('game_id', '=', $game->id)->first();

        $this
            ->get('api/games/' . $game->id . '/mods')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'user_id',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'path',
                    'per_page',
                    'to',
                    'total',
                    "from",
                    "last_page",
                ]
            ])
            ->assertJsonFragment([
                'id' => $mod->id,
                'game_id' => $game->id,
                'user_id' => User::admin()->first()->id,
            ]);
    }

    public function testCreateFailedWhileValidation(): void
    {

        $this->seed(GameTableSeeder::class);
        $this->actingAs(User::admin()->first());

        $game = Game::inRandomOrder()->first();
        //   below to include the required header or URL parameter to achieve that
        $this
            ->post('api/games/' . $game->id . '/mods', [
                'name' => \Str::random(1000),
            ])
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonStructure([
                'errors',
                'message',
            ])
            ->assertJsonFragment([
                "message" => "The name field must not be greater than 255 characters."
            ]);

    }

    public function testCreateSucceedsWhileAuthenticated(): void
    {

        $this->seed(GameTableSeeder::class);
        $this->actingAs(User::admin()->first());

        $game = Game::inRandomOrder()->first();

        //   below to include the required header or URL parameter to achieve that
        $this
            ->post('api/games/' . $game->id . '/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Lightsaber',
                'game_id' => $game->id,
                'user_id' => User::admin()->first()->id,
            ]);
    }

    public function testReadSucceeds(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());

        $response = $this->post('api/games', [
            'name' => 'Rogue Knight'
        ])->assertStatus(Response::HTTP_CREATED);

        $game_id = $response->json('id');
        $response = $this->post('api/games/' . $game_id . '/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // view the mod
        $this
            ->get('api/games/' . $game_id . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Lightsaber',
                'game_id' => $game_id,
                'user_id' => User::admin()->first()->id,
            ]);
    }

    public function testCreateFailsWhileUnauthenticated(): void
    {
        $this->seed(GameTableSeeder::class);

        $game = Game::inRandomOrder()->first();

        $this
            ->post('api/games/' . $game->id . '/mods', [
                'name' => 'Lightsaber'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated(): void
    {

        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());


        $response = $this->post('api/games', [
            'name' => 'Rogue Knight'
        ])->assertStatus(Response::HTTP_CREATED);

        $game_id = $response->json('id');


        $response = $this->post('api/games/' . $game_id . '/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        $this
            ->put('api/games/' . $game_id . '/mods/' . $response->json('id'), [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'game_id',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Lightsabers (Full set)',
                'game_id' => $game_id,
                'user_id' => User::admin()->first()->id,
            ]);
    }
    public function testUpdateFailedWhileOtherUser(): void
    {

        $this->seed(UsersTableSeeder::class);

        $this->actingAs(User::admin()->first());
        $response = $this->post('api/games', ['name' => 'Rogue Knight by Admin'])->assertStatus(Response::HTTP_CREATED);
        $admin_game_id = $response->json('id');
        $response = $this->post('api/games/' . $admin_game_id . '/mods', ['name' => 'Lightsaber admin'])->assertStatus(Response::HTTP_CREATED);
        $admin_mod_id = $response->json('id');


        $this->actingAs(User::latest()->first());
        $response = $this->post('api/games', ['name' => 'Rogue Knight by other user'])->assertStatus(Response::HTTP_CREATED);
        $game_id = $response->json('id');
        $response = $this->post('api/games/' . $game_id . '/mods', ['name' => 'Lightsaber other'])->assertStatus(Response::HTTP_CREATED);

        $this
            ->put('api/games/' . $admin_game_id . '/mods/' . $response->json('id'), [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJsonStructure([
                'error',
            ])
            ->assertJsonFragment([
                'error' => 'Not Found',
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {

        $this->seed(ModsTableSeeder::class);
        $game = Game::first();
        $mod = Mod::first();

        // this however should fail with 401 Unauthorized, as expected
        $this
            ->put('api/games/' . $game->id . '/mods/' . $mod->id, [
                'name' => 'Lightsabers (Full set)'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {

        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());
        $response = $this->post('api/games', [
            'name' => 'Rogue Knight'
        ]);

        $gameId = $response->json('id');

        $response = $this->post('api/games/' . $gameId . '/mods', [
            'name' => 'Lightsaber'
        ])->assertStatus(Response::HTTP_CREATED);

        // and just for sanity we make sure it actually got created
        $this
            ->get('api/games/' . $gameId . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('api/games/' . $gameId . '/mods/' . $response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {

        $this->seed(ModsTableSeeder::class);
        $game = Game::first();
        $mod = Mod::first();
        $gameId = $game->id;

        // then we finally attempt to delete it without authentication present
        $this
            ->delete('api/games/' . $gameId . '/mods/' . $mod->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
