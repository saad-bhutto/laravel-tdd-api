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
     *
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
     *
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
     *
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
     *
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
