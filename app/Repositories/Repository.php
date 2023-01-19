<?php

namespace App\Repositories;

use App\Contracts\Repository\RepositoryInterface;
use http\Exception\InvalidArgumentException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;

abstract class Repository implements RepositoryInterface
{
    /**
     * @var Model
     */
    protected Model $model;

    protected string $primaryKey = 'id';

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
        // TODO: Implement index() method.
    }

    /**
     * @inheritDoc
     */
    public function get($id): Model
    {
        // TODO: Implement get() method.
    }

    /**v
     * @inheritDoc
     */
    public function create($data): Model
    {
        // TODO: Implement create() method.
    }

    /**
     * @inheritDoc
     */
    public function update($id, $data): Model
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function delete($id): bool
    {
        // TODO: Implement delete() method.
    }
}
