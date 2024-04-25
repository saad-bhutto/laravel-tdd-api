<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\GameControllerInterface;
use App\Http\Requests\GameRequest;
use App\Http\Resources\GameCollection;
use App\Http\Resources\GameResource;
use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Info(title="Game API", version="0.1")
 *
 * @OA\Schema(
 *     schema="Game",
 *     title="Game",
 *     description="Game object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the game"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the game"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who created the game"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the game was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the game was last updated"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Meta",
 *     title="Meta",
 *     description="Meta information for pagination",
 *     @OA\Property(
 *         property="page",
 *         type="integer",
 *         description="page number"
 *     ),
 *     @OA\Property(
 *         property="per_page",
 *         type="integer",
 *         description="Items per page"
 *     )
 * )
 *
 */
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
     * @OA\Get(
     *     path="/api/games",
     *     operationId="getGameList",
     *     tags={"Games"},
     *     summary="Get a list of games",
     *     description="Returns a paginated list of games.",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=1
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             default=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Game")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 ref="#/components/schemas/Meta"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request): JsonResponse
    {
        try {
            $collection = new GameCollection($this->gameService->paginate(
                request()->get('per_page', 15),
                request()->get('page', 1)
            ));

            return $collection->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to create the resource.
     * @OA\Post(
     *     path="/api/games",
     *     operationId="createGame",
     *     tags={"Games"},
     *     summary="Create a new game",
     *     description="Create a new game with the provided name.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Game name",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the game"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Game created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Game"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     * @param GameRequest $request
     * @return JsonResponse
     */
    public function create(GameRequest $request): JsonResponse
    {
        try {

            $game =  $this->gameService->create([
                'name' => $request->get('name'),
                'user_id' => auth()->user()->id
            ]);

            return (new GameResource($game))->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to read the game.
     * @OA\Get(
     *     path="/api/games/{id}",
     *     operationId="getResourceById",
     *     tags={"Games"},
     *     summary="Get game by ID",
     *     description="Returns a single game",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the game to retrieve",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Game"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found"
     *     ),
     *     security={{"bearerAuth": {}}}
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function read(Request $request, Game $game): JsonResponse
    {
        try {
            return (new GameResource($game))->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to update the resource.
     * @OA\Put(
     *     path="/api/games/{id}",
     *     operationId="updateGame",
     *     tags={"Games"},
     *     summary="Update a game",
     *     description="Update an existing game by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the game to update",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Game name",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the game"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Game"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found"
     *     )
     * )
     * @param GameRequest $request
     * @return JsonResponse
     */
    public function update(GameRequest $request, Game $game): JsonResponse
    {
        try {
            $this->authorize('update', $game);

            $game =  $this->gameService->update([
                'name' => $request->get('name'),
            ], $game->id);

            return (new GameResource($game))->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to delete the resource.
     * @OA\Delete(
     *     path="/api/games/{id}",
     *     operationId="deleteGame",
     *     tags={"Games"},
     *     summary="Delete a game",
     *     description="Delete an existing game by ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the game to delete",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Game deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found"
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request, Game $game): JsonResponse
    {
        try {
            $this->authorize('delete', $game);

            return response()->json(
                $this->gameService->delete($game),
                Response::HTTP_NO_CONTENT
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }
}
