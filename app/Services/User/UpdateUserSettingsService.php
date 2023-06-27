<?php

namespace App\Services\User;

use App\Contracts\Repository\SettingsRepositoryInterface;
use Exception;

class UpdateUserSettingsService
{
    /**
     * @var SettingsRepositoryInterface
     */
    private SettingsRepositoryInterface $settingsRepository;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository
    )
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Update a user's settings and persist to database
     *
     * @param string $uuid
     * @param array $data
     * @return false[]|true[]
     */
    public function handle(string $uuid, array $data): array
    {
        /**
         * Get user's current settings
         */
        $settings = [];
        foreach ($this->settingsRepository->getUserSettings($uuid) as $setting) {
            $settings[$setting['key']] = $setting['value'];
        }

        foreach ($data as $key => $value) {
            try {
                /**
                 * Check if settings key to be updated exists for the user
                 */
                if (array_key_exists($key, $settings)) {
                    /**
                     * If setting key already exists, update the corresponding value
                     */
                    $this->settingsRepository->updateByUuidAndKey($uuid, $key, $value);
                } else {
                    /**
                     * If setting key does not exist, create new setting entry
                     */
                    $this->settingsRepository->create([
                        'user_id' => $uuid,
                        'key' => $key,
                        'value' => $value
                    ], true);
                }
            } catch (Exception $e) {
                return [
                    'success' => false
                ];
            }
        }

        return [
            'success' => true
        ];
    }
}
