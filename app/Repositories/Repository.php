<?php

namespace App\Repositories;

use App\Contracts\Repository\RepositoryInterface;
use Exception;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    public function __construct(protected Application $app)
    {
        $this->initializeModel($this->model());
    }

    abstract public function model(): string;

    /**
     * Initialise provided model so it is available to rest of the repository
     *
     * @param string ...$model
     * @return mixed
     */
    protected function initializeModel(string ...$model): mixed
    {
        return
            match (count($model)) {
                1 => $this->model = $this->app->make($model[0]),
                2 => $this->model = call_user_func([$this->app->make($model[0]), $model[1]]),
                default => throw new InvalidArgumentException('Invalid model provided'),
            };
    }

    public function getBuilder(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * @inheritDoc
     */
    public function index(): object
    {
        return $this->getBuilder()->get();
    }

    /**
     * @inheritDoc
     */
    public function get($id): Model
    {
        try {
            $entry = $this->getBuilder()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new Exception('Record not found', 404);
        }

        return $entry;
    }

    /**v
     * @inheritDoc
     */
    public function create(array $data, bool $force = false): Model
    {
        $entry = $this->getBuilder()->newModelInstance();

        ($force) ? $entry->forceFill($data) : $entry->fill($data);
        $entry->save();

        return $entry->fresh();
    }

    /**
     * @inheritDoc
     */
    public function update($id, array $data, bool $force = false): Model
    {
        $entry = $this->get($id);

        ($force) ? $entry->forceFill($data) : $entry->fill($data);
        $entry->save();

        return $entry->fresh();
    }

    /**
     * @inheritDoc
     */
    public function delete($id, bool $destroy = false): bool
    {
        $entry = $this->get($id);

        return ($destroy) ? $entry->forceDelete() : $entry->delete();
    }
}
