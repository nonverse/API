<?php

namespace App\Contracts\Repository;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * @return string
     */
    public function model(): string;

    /**
     * Get all rows matching a given filter
     *
     * @return object
     */
    public function index(): object;

    /**
     * Get row by ID
     *
     * @param $id
     * @return Model
     */
    public function get($id): Model;

    /**
     * Create new row
     *
     * @param $data
     * @return Model
     */
    public function create($data): Model;

    /**
     * Update row by ID
     *
     * @param $id
     * @param $data
     * @return Model
     */
    public function update($id, $data): Model;

    /**
     * Delete row
     *
     * @param $id
     * @return bool
     */
    public function delete($id): bool;
}
