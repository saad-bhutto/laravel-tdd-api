<?php

namespace App\Repositories;

use App\Models\Mod;

/**
 * GameRepository
 */
class ModRepository extends BaseRepository
{
    /**
     * DummyModelRepository constructor.
     * @param  Mod  $model
     */
    public function __construct(Mod $model)
    {
        $this->model = $model;
        $this->modelClass = Mod::class;
    }
}
