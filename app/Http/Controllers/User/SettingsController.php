<?php

namespace App\Http\Controllers\User;

use App\Contracts\Repository\SettingsRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Services\User\UpdateUserSettingsService;
use Exception;
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
     * @return JsonResponse
     */
    public function get(Request $request): JsonResponse
    {
        $settings = $this->settingsRepository->getUserSettings($request->user()->uuid);
        $response = [];
        foreach ($settings as $setting) {
            $response[$setting['key']] = $setting['value'];
        }

        return new JsonResponse([
            'data' => $response
        ]);
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
        try {
            $this->updateUserSettingsService->handle($request->user()->uuid, $request->input('settings'));
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false
            ]);
        }

        /**
         * Get updated user settings
         */
        foreach ($this->settingsRepository->getUserSettings($request->user()->uuid) as $setting) {
            $settings[$setting['key']] = $setting['value'];
        }

        $cookie = cookie('settings', json_encode([
            'theme' => array_key_exists('theme', $settings) ? $settings['theme'] : 'system',
            'language' => array_key_exists('language', $settings) ? $settings['language'] : 'en-AU'
        ]), 43800, null, env('SESSION_PARENT_DOMAIN'), false, false);

        return response()->json([
            'success' => true
        ])->withCookie($cookie);
    }
}
