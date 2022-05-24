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
     * @param array $settings
     * @return bool[]
     */
    public function handle($uuid, array $settings): array
    {

        $keys = [];

        // TODO Fix preference persistence issue
        foreach ($settings as $key => $value) {


            // Attempt to update a user's preference on the database
            if (!$this->repository->update($uuid, $key, $value)) {

                /*
                 * If the preference setting does not exist for that user,
                 * create it and persist to database
                 */
                $this->repository->create($uuid, $key, $value);
            }

            $keys[] = $key;
        }

        if ($keys) {
            return [
                'success' => true,
                'keys' => $keys
            ];
        }

        return [
            'success' => false,
            'error' => 'Something went wrong'
        ];
    }
}
