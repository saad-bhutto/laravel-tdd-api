<?php

namespace App\Services;

use App\Repositories\ModRepository;

/**
 * ModService
 */
class ModService extends ServiceManager
{
    /**
     * default constructor
     *
     * @param ModRepository $modRepository
     */
    public function __construct(
        ModRepository $modRepository
    ) {
        $this->repository = $modRepository;
    }
}
