<?php

namespace App\Http\Controllers\Contracts;

use App\Http\Requests\ModRequest;
use App\Models\Game;
use App\Models\Mod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * GameControllerInterface.
 */
interface ModControllerInterface
{
    /**
     * Browse Mods.
     *
     * @param Request $request
     * @param Game $game
     * @return JsonResponse
     */
    public function browse(Request $request, Game $game) : JsonResponse;

    /**
     * Create a mod.
     *
     * @param Request $request
     * @param Game $game
     * @return JsonResponse
     */
    public function create(ModRequest $request, Game $game) : JsonResponse;

    /**
     * Read/view a mod.
     *
     * @param Request $request
     * @param Game $game
     * @param Mod $mod
     * @return JsonResponse
     */
    public function read(Request $request, Game $game, Mod $mod) : JsonResponse;

    /**
     * Update a mod.
     *
     * @param Request $request
     * @param Game $game
     * @param Mod $mod
     * @return JsonResponse
     */
    public function update(ModRequest $request, Game $game, Mod $mod) : JsonResponse;

    /**
     * Delete a mod.
     *
     * @param Request $request
     * @param Game $game
     * @param Mod $mod
     * @return JsonResponse
     */
    public function delete(Request $request, Game $game, Mod $mod) : JsonResponse;
}
