<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

/**
 * BaseRepository
 *
 */
abstract class BaseRepository
{
    /**
     * Model property on class instances.
     *
     * @var object<\Illuminate\Database\Eloquent\Model>
     */
    protected $model;

    /**
     * The target model class.
     *
     * @var string
     */
    protected $modelClass;

    /**
     * comparison operator.
     *
     * @var string
     */
    protected $comparisonOperator = '=';

    /**
     * Determine if array has all the keys numeric.
     *
     * @param  array $array
     * @return bool
     */
    protected function arrayHasAllNumericKeys(array $array)
    {
        return !(count(array_filter(array_keys($array), 'is_string')) > 0);
    }

    /**
     * Get the model class full namespace path.
     *
     * @return string
     */
    public function getModelClass()
    {
        if ($this->modelClass) {
            return $this->modelClass;
        }

        if (!$this->model) {
            $this->modelClass = get_class($this->model);
        }

        return $this->modelClass;
    }

    /**
     * Sanitize data list to model fillables.
     *
     * @param  array   $data
     * @return array
     */
    private function sanitizeToModelFillable(array $data)
    {
        $classModel = $this->model->getModel();
        $fillable = $classModel->getFillable();

        $fillables = !empty($fillable)
            ? $fillable
            : array_diff(
                array_diff(
                    Schema::getColumnListing($classModel->getTable()),
                    $classModel->getGuarded()
                ),
                $classModel->getHidden()
            );

        return array_intersect_key($data, array_flip($fillables));
    }


    /**
     * Does the repository model has soft deleted feature enabled;.
     *
     * @return bool
     */
    public function hasSoftDelete()
    {
        if (!$this->modelClass && !$this->getModelClass()) {
            return false;
        }

        return in_array(
            'Illuminate\Database\Eloquent\SoftDeletes',
            class_uses($this->modelClass)
        );
    }

    /**
     * custom method to bind model to repo.
     *
     * @param  object<\Illuminate\Database\Eloquent\Model>   $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Return binded model instance.
     *
     * @return object<\Illuminate\Database\Eloquent\Model>
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * Find specific model instance.
     *
     * @param  mixed  $param
     * @param  array  $withs
     * @param  bool   $allowException
     *
     * @return mixed
     */
    public function find($param, array $withs = [], bool $allowException = false)
    {
        $find = $allowException ? 'findOrFail' : 'find';
        $first = $allowException ? 'firstOrFail' : 'first';

        if (!is_array($param) || $this->arrayHasAllNumericKeys($param)) {
            return $this->model->with($withs)->{$find}($param);
        }

        return $this->where($param)->with($withs)->getModel()->{$first}();
    }

    /**
     * Model order by clause.
     *
     * @param  array   $orders
     * @return $this
     */
    public function orderBy(array $orders = ['id' => 'asc'])
    {
        $existingOrders = $this->model->getQuery()->orders ?? [];

        foreach ($orders as $column => $direction) {
            if (!in_array(compact('column', 'direction'), $existingOrders)) {
                $this->model = $this->model->orderBy($column, $direction);
            }
        }

        return $this;
    }

    /**
     * Model egear load clause.
     *
     * @param  array   $with
     * @return $this
     */
    public function with($with = [])
    {
        if (!empty($with)) {
            $this->model = $this->getModel()->with($with);
        }

        return $this;
    }

    /**
     * Model Pagination.
     *
     * @param  int     $perPage
     * @param  array   $columns
     * @param  string  $pageName
     * @param  int     $page
     *
     * @return mixed<(object|null)>
     */
    public function paginate(
        int $perPage = null,
        int $page = null,
        array $columns = ['*'],
        string $pageName = 'page'
    ) {
        return $this->orderBy()->getModel()->paginate(
            $perPage ?? 15,
            $columns,
            $pageName,
            $page ?? 1
        );
    }

    /**
     * Attach where clause.
     *
     * @param  array  $constrains
     * @return $this
     */
    private function where(array $constrains = [])
    {
        foreach ($constrains as $column => $value) {
            $this->model = $this->model->where($column, $value);
        }

        return $this;
    }

    /**
     * Get all instances of model.
     *
     * @param  array  $withs
     * @return mixed
     */
    public function all(array $withs = [])
    {
        return $this->orderBy()->getModel()->with($withs)->get();
    }

    /**
     * Create a new record in the database.
     *
     * @param  array  $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($this->sanitizeToModelFillable($data));
    }

    /**
     * Create a new record in the database.
     *
     * @param  array  $data
     * @return mixed
     */
    public function bulkCreate(array $dataset)
    {
        return $this->model->insert(array_map(fn ($data) => $this->sanitizeToModelFillable($data), $dataset));
    }

    /**
     * Update record in the database.
     *
     * @param  array    $data
     * @param  mixed    $primaryKeyValue
     *
     * @return mixed
     */
    public function update(array $data, $primaryKeyValue)
    {
        $recordToUpdate = $primaryKeyValue instanceof Model
            ? $primaryKeyValue
            : $this->find($primaryKeyValue);

        $recordToUpdate->update($this->sanitizeToModelFillable($data));

        return $recordToUpdate->fresh();
    }

    /**
     * Delete a database record.
     *
     * @param  mixed $param
     * @return mixed
     */
    public function delete($param)
    {
        $resource = $param instanceof Model ? $param : $this->find($param);

        if ($resource instanceof Collection) {
            if ($this->hasSoftDelete()) {
                $resource->toQuery()->update([
                    $this->model->getDeletedAtColumn() => now(),
                ]);
            }

            $resource->toQuery()->delete();

            return true;
        }

        return $resource->delete();
    }

    /**
     * Force Delete a database record.
     *
     * @param  mixed $param
     * @return bool
     */
    public function forceDelete($param)
    {
        $resource = $param instanceof Model ? $param : $this->find($param);

        if ($resource instanceof Collection) {
            $resource->toQuery()->delete();

            return true;
        }

        return $this->hasSoftDelete() ? $resource->forceDelete() : $resource->delete();
    }

    /**
     * Restore a soft deleted database record.
     *
     * @param  mixed $param
     * @return bool
     */
    public function restore($param)
    {
        if (!$this->hasSoftDelete()) {
            throw new \Exception('The ' . $this->getModelClass() . ' class does not use soft delete feature.');
        }

        $resource = $param instanceof Model ? $param : $this->model->onlyTrashed->findorFail($param);

        if ($resource instanceof Collection) {
            $resource->toQuery()->update([
                $this->model->getDeletedAtColumn() => null,
            ]);

            return true;
        }

        return $resource->restore();
    }
}