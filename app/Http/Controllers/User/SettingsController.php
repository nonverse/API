<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\User\UpdateUserSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    /**
     * @var SettingsRepositoryInterface
     */
    private SettingsRepositoryInterface $settingsRepository;

    /**
     * @var UpdateUserSettingsService
     */
    private UpdateUserSettingsService $updateUserSettingsService;

    public function __construct(
        SettingsRepositoryInterface $settingsRepository,
        UpdateUserSettingsService   $updateUserSettingsService
    )
    {
        $this->settingsRepository = $settingsRepository;
        $this->updateUserSettingsService = $updateUserSettingsService;
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

    /**
     * Handle request to update user's settings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        /**
         * Validate request
         */
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array'
        ]);

        if ($validator->fails()) {
            return new JsonResponse([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        /**
         * Attempt to update user's settings
         */
        $settings = $this->updateUserSettingsService->handle($request->user()->uuid, $request->input('settings'));

        if (!$settings['success']) {
            return new JsonResponse([
                'success' => false,
            ], 400);
        }

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
