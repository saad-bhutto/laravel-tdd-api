<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Contracts\ModControllerInterface;
use App\Http\Requests\ModRequest;
use App\Models\Game;
use App\Models\Mod;
use App\Services\ModService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
     *
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
            return response()->json(
                $this->modService->paginate(
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
    public function create(ModRequest $request, Game $game): JsonResponse
    {
        try {
            return response()->json(
                $this->modService->create([
                    'name' => $request->get('name'),
                    'game_id' => $game->id,
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
    public function read(Request $request, Game $game, Mod $mod): JsonResponse
    {
        try {
            return response()->json($mod);
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
    public function update(ModRequest $request, Game $game, Mod $mod): JsonResponse
    {
        try {
            return response()->json(
                $this->modService->update([
                    'name' => $request->get('name'),
                ], $mod->id),
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
    public function delete(Request $request, Game $game, Mod $mod): JsonResponse
    {
        try {
            return response()->json(
                $this->modService->delete($mod),
                Response::HTTP_NO_CONTENT
            );
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th);
        }
    }
}
