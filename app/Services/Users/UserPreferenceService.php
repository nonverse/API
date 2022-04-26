<?php

namespace App\Services\Users;

use App\Repositories\SettingRepository;

class UserPreferenceService
{
    /**
     * @var SettingRepository
     */
    private $repository;

    public function __construct(
        SettingRepository $repository
    )
    {
        $this->repository = $repository;
    }

    /**
     * Update a user's setting and persist to database
     *
     * @param $uuid
     * @param $key
     * @param $value
     * @return bool[]
     */
    public function handle($uuid, $key, $value): array
    {

        /*
         * Attempt to update a user's setting on the database
         */
        if ($this->repository->update($uuid, $key, $value)) {
            return [
                'success' => true
            ];
        }
        // TODO - Fix error where updating one setting will update all settings to that value

        /*
         * If the setting does not exists, create it and persist
         * to database
         */
        if ($this->repository->create($uuid, $key, $value)) {
            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'error' => 'Something went wrong'
        ];
    }
}
