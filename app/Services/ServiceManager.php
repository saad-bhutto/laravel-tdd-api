<?php

namespace App\Services;

use Illuminate\Support\Traits\ForwardsCalls;

/**
 * ServiceManager class.
 *
 * @method all(array $withs = []) method to call all on a given model.
 * @method paginate() method to call paginate on a given model.
 * @method create(array $data) method to call create on a given model.
 * @method bulkCreate(array $dataset) method to call bulkCreate on a given model.
 * @method update(array $data, $prinamryKeyValue) method to call update on a given model.
 * @method delete($param) method to call delete on a given model.
 * @method forceDelete($param) method to call forceDelete on a given model.
 * @method restore($param) method to call restore on a given model.
 */
abstract class ServiceManager
{
    use ForwardsCalls;

    protected $repository;

    public function __call($method, $parameter)
    {
        return $this->forwardCallTo($this->repository, $method, $parameter);
    }
}
