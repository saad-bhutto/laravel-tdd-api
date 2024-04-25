<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\GameControllerInterface;
use App\Http\Requests\GameRequest;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class GameController extends Controller implements GameControllerInterface
{
    private GameService $gameService;

    public function __construct(GameService $gameService)
    {
        $this->middleware('auth:sanctum')->only(['create', 'update', 'delete']);
        $this->gameService = $gameService;
    }

    /**
     * function to browse the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        return $this->browse($request);
    }

    /**
     * function to browse the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request): JsonResponse
    {
        try {
            return response()->json(
                $this->gameService->paginate(
                    request()->get('per_page', 15),
                    request()->get('page', 1)
                )
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to create the resource.
     *
     * @param GameRequest $request
     * @return JsonResponse
     */
    public function create(GameRequest $request): JsonResponse
    {
        try {
            return response()->json(
                $this->gameService->create([
                    'name' => $request->get('name'),
                    'user_id' => auth()->user()->id
                ]),
                Response::HTTP_CREATED
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to read the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function read(Request $request, Game $game): JsonResponse
    {
        try {
            return response()->json($game);
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to update the resource.
     *
     * @param GameRequest $request
     * @return JsonResponse
     */
    public function update(GameRequest $request, Game $game): JsonResponse
    {
        try {
            return response()->json(
                $this->gameService->update([
                    'name' => $request->get('name'),
                ], $game->id),
                Response::HTTP_OK
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to delete the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request, Game $game): JsonResponse
    {
        try {
            return response()->json(
                $this->gameService->delete($game),
                Response::HTTP_NO_CONTENT
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }
}