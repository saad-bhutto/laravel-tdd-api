<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Requests\GameRequest;
use App\Models\Game;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GameControllerInterface.
 */
interface GameControllerInterface
{
    /**
     * Browse Games.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request) : JsonResponse;

    /**
     * Create game.
     *
     * @param GameRequest $request
     * @return JsonResponse
     */
    public function create(GameRequest $request) : JsonResponse;

    /**
     * Read/view a game.
     *
     * @param Request $request
     * @param Game $game
     * @return JsonResponse
     */
    public function read(Request $request, Game $game) : JsonResponse;

    /**
     * Update a game.
     *
     * @param GameRequest $request
     * @param Game $game
     * @return JsonResponse
     */
    public function update(GameRequest $request, Game $game) : JsonResponse;

    /**
     * Delete a game.
     *
     * @param Request $request
     * @param Game $game
     * @return JsonResponse
     */
    public function delete(Request $request, Game $game) : JsonResponse;
}
