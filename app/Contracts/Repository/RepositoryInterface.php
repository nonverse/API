<?php

namespace App\Contracts\Repository;

use Exception;
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
     * @throws Exception
     *
     */
    public function get($id): Model;

    /**
     * Create new row
     *
     * @param array $data
     * @param bool $force
     * @return Model
     */
    public function create(array $data, bool $force): Model;

    /**
     * Update row by ID
     *
     * @param $id
     * @param array $data
     * @param bool $force
     * @return Model
     * @throws Exception
     */
    public function update($id, array $data, bool $force): Model;

    /**
     * Delete row
     *
     * @param $id
     * @param bool $destroy
     * @return bool
     * @throws Exception
     */
    public function delete($id, bool $destroy): bool;
}
