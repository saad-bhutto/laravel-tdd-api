<?php

namespace App\Services;

use App\Repositories\GameRepository;

/**
 * GameService
 *
 */
class GameService extends ServiceManager
{
    /**
     * default constructor
     *
     * @param GameRepository $GameRepository
     */
    public function __construct(
        GameRepository $GameRepository
    ) {
        $this->repository = $GameRepository;
    }

}
