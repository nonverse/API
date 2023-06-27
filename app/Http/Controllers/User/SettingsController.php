<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * @var SettingsRepositoryInterface
     */
    private SettingsRepositoryInterface $settingsRepository;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository,
    )
    {
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Handle request to get user's settings
     *
     * @param Request $request
     * @return array
     */
    public function get(Request $request): array
    {
        $settings = $this->settingsRepository->getUserSettings($request->user()->uuid);
        foreach ($settings as $setting) {
            $response[$setting['key']] = $setting['value'];
        }

        return $response;
    }
}
