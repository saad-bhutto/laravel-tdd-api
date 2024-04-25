<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Database\Seeders\GameTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class GameTest extends TestCase
{
    use RefreshDatabase;

    const ROUTE_BASE = 'api/games/';

    public function testBrowseSucceeds(): void
    {
        $this->seed(GameTableSeeder::class);
        //  in order for this test to pass, you will need to seed at least 1 game

        $this
            ->get(self::ROUTE_BASE)
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
            ]);
    }

    public function testCreateSucceedsWhileAuthenticated(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());

        //   below to include the required header or URL parameter to achieve that
        $this
            ->post(self::ROUTE_BASE, [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);
    }

    public function testCreateFailedWhileValidation(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());

        //   below to include the required header or URL parameter to achieve that
        $this
            ->post(self::ROUTE_BASE, [
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

    public function testReadSucceeds(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());

        $response = $this->post(self::ROUTE_BASE, [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->get('api/games/' . $response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight',
            ]);
    }

    public function testCreateFailsWhileUnauthenticated(): void
    {
        $this
            ->post(self::ROUTE_BASE, [
                'name' => 'Rogue Knight'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testUpdateSucceedsWhileAuthenticated(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());
        $response = $this->post(self::ROUTE_BASE, [
            'name' => 'Rogue Knight'
        ]);

        $this
            ->put(self::ROUTE_BASE . $response->json('id'), [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight Remastered'
            ]);
    }

    public function testUpdateFailedWhileOtherUser(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());
        $response = $this->post(self::ROUTE_BASE, [
            'name' => 'Rogue Knight By Admin'
        ]);
        $adminGameId = $response->json('id');

        $this->actingAs(User::latest()->first());
        $response = $this->post(self::ROUTE_BASE, [
            'name' => 'Rogue Knight by Another User'
        ]);

        $this
            ->put(self::ROUTE_BASE . $adminGameId, [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJsonStructure([
                'status',
                'data',
                'errors',
            ])
            ->assertJsonFragment([
                "message" => "This action is unauthorized."
            ]);
    }

    public function testUpdateFailsWhileUnauthenticated(): void
    {
        $this->seed(GameTableSeeder::class);
        $game = Game::first();

        // this however should fail with 401 Unauthorized, as expected
        $this
            ->put(self::ROUTE_BASE . $game->id, [
                'name' => 'Rogue Knight Remastered'
            ])
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDeleteSucceedsWhileAuthenticated(): void
    {
        $this->seed(UsersTableSeeder::class);
        $this->actingAs(User::admin()->first());

        $response = $this->post(self::ROUTE_BASE, [
            'name' => 'Rogue Knight'
        ]);

        // just to ensure the game actually exists
        $this
            ->get(self::ROUTE_BASE . $response->json('id'))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at'
            ])
            ->assertJsonFragment([
                'name' => 'Rogue Knight'
            ]);

        $this
            ->delete(self::ROUTE_BASE . $response->json('id'))
            ->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testDeleteFailsWhileUnauthenticated(): void
    {

        $this->seed(GameTableSeeder::class);
        $game = Game::first();

        // and just for sanity we make sure it actually got created
        $this
            ->get(self::ROUTE_BASE . $game->id)
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'id',
                'name',
                'user_id'
            ])
            ->assertJsonFragment([
                'name' => $game->name
            ]);

        // then we finally attempt to delete it without authentication present
        $this
            ->delete(self::ROUTE_BASE . $game->id)
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
