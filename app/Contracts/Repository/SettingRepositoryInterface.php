<?php

namespace App\Contracts\Repository;

use App\Models\Setting;

interface SettingRepositoryInterface
{
    /**
     * @param $uuid
     * @return object
     */
    public function index($uuid): object;

//    /**
//     * @param $uuid
//     * @param $key
//     */
//    public function get($uuid, $key);

    /**
     * @param $uuid
     * @param array $data
     * @return Setting
     */
    public function create($uuid, array $data): Setting;

    /**
     * @param $uuid
     * @param $key
     * @param $value
     * @return mixed
     */
    public function update($uuid, $key, $value);
}
