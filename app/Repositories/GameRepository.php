<?php

namespace App\Repositories;

use App\Models\Game;

/**
 * GameRepository
 *
 */
class GameRepository extends BaseRepository
{
    /**
     * DummyModelRepository constructor.
     * @param  Game  $model
     */
    public function __construct(Game $model)
    {
        $this->model = $model;
        $this->modelClass = Game::class;
    }
}
