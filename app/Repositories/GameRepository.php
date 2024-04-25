<?php

namespace App\Repositories;

use App\Models\Game;

/**
 * GameRepository to interact with the the Database
 */
class GameRepository extends BaseRepository
{
    /**
     * GameRepository default constructor.
     * @param  Game  $model
     */
    public function __construct(Game $model)
    {
        $this->model = $model;
        $this->modelClass = Game::class;
    }
}
