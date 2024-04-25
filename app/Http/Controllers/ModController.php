<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\ModControllerInterface;
use App\Http\Requests\ModRequest;
use App\Http\Resources\ModCollection;
use App\Http\Resources\ModResource;
use App\Models\Game;
use App\Models\Mod;
use App\Services\ModService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @OA\Schema(
 *     schema="Mod",
 *     title="Mod",
 *     description="Mod object",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the mod"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the mod"
 *     ),
 *     @OA\Property(
 *         property="user_id",
 *         type="integer",
 *         description="ID of the user who created the mod"
 *     ),
 *     @OA\Property(
 *         property="game_id",
 *         type="integer",
 *         description="ID of the game who created the mod"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the mod was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the mod was last updated"
 *     )
 * )
 *
 */
class ModController extends Controller implements ModControllerInterface
{
    private $modService;

    public function __construct(ModService $modService)
    {
        $this->middleware('auth:sanctum')->only(['create', 'update', 'delete']);
        $this->modService = $modService;
    }


    /**
     * function to browse the resource.
     * @OA\Get(
     *     path="/api/games/{game}/mods",
     *     operationId="getModsByGame",
     *     tags={"Mods"},
     *     summary="Get mods by game",
     *     description="Get a list of mods for a specific game.",
     *     @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="ID of the game",
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
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Mod")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game not found"
     *     )
     * )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request, Game $game): JsonResponse
    {
        return $this->browse($request, $game);
    }

    /**
     * function to browse the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function browse(Request $request, Game $game): JsonResponse
    {
        try {
            $collection = new ModCollection($this->modService->paginate(
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
     *     path="/api/games/{game}/mods",
     *     operationId="createMod",
     *     tags={"Mods"},
     *     summary="Create a new mod for a game",
     *     description="Create a new mod for a specific game with the provided name.",
     *     @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="ID of the game",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Mod name",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"name"},
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="Name of the mod"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Mod created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Mod"
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
    public function create(ModRequest $request, Game $game): JsonResponse
    {
        try {
            $mod = $this->modService->create([
                'name' => $request->get('name'),
                'game_id' => $game->id,
                'user_id' => auth()->user()->id
            ]);

            return (new ModResource($mod))->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to read the resource.
     * @OA\Get(
     *     path="/api/games/{game}/mods/{mod}",
     *     operationId="getModById",
     *     tags={"Mods"},
     *     summary="Get mod by ID",
     *     description="Get details of a specific mod by ID for a game.",
     *     @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="ID of the game",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mod",
     *         in="path",
     *         description="ID of the mod",
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
     *             ref="#/components/schemas/Mod"
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mod or game not found"
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function read(Request $request, Game $game, Mod $mod): JsonResponse
    {
        try {
            return (new ModResource($mod))->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to update the resource.
     * @OA\Put(
     *     path="/api/games/{game}/mods/{mod}",
     *     operationId="updateMod",
     *     tags={"Mods"},
     *     summary="Update a mod for a game",
     *     description="Update an existing mod for a specific game by ID.",
     *     @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="ID of the game",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mod",
     *         in="path",
     *         description="ID of the mod",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated mod data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="string",
     *                     description="New name of the mod"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Mod updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             ref="#/components/schemas/Mod"
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mod or game not found"
     *     )
     * )
     * @param GameRequest $request
     * @return JsonResponse
     */
    public function update(ModRequest $request, Game $game, Mod $mod): JsonResponse
    {
        try {
            $this->authorize('update', $mod);

            $mod =  $this->modService->update([
                'name' => $request->get('name'),
            ], $mod->id);

            return (new ModResource($mod))->response();
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }

    /**
     * function to delete the resource.
     * @OA\Delete(
     *     path="/api/games/{game}/mods/{mod}",
     *     operationId="deleteMod",
     *     tags={"Mods"},
     *     summary="Delete a mod for a game",
     *     description="Delete an existing mod for a specific game by ID.",
     *     @OA\Parameter(
     *         name="game",
     *         in="path",
     *         description="ID of the game",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="mod",
     *         in="path",
     *         description="ID of the mod",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64"
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Mod deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Mod or game not found"
     *     )
     * )
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request, Game $game, Mod $mod): JsonResponse
    {
        try {
            $this->authorize('delete', $mod);

            return response()->json(
                $this->modService->delete($mod),
                Response::HTTP_NO_CONTENT
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }
}
