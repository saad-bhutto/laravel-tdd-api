<?php

namespace App\Repositories;

use App\Models\Mod;

/**
 * ModRepository to interact with the database.
 */
class ModRepository extends BaseRepository
{
    /**
     * ModRepository default constructor.
     * @param  Mod  $model
     */
    public function __construct(Mod $model)
    {
        $this->model = $model;
        $this->modelClass = Mod::class;
    }
}
