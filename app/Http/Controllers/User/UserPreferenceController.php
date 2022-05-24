<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\SettingRepository;
use App\Services\Users\UserPreferenceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPreferenceController extends Controller
{
    /**
     * @var SettingRepository
     */
    private $repository;

    /**
     * @var UserPreferenceService
     */
    private $preferenceService;

    public function __construct(
        SettingRepository     $repository,
        UserPreferenceService $preferenceService
    )
    {
        $this->repository = $repository;
        $this->preferenceService = $preferenceService;
    }

    /**
     * Get all of a user's settings/preferences
     *
     * @param Request $request
     * @return object
     */
    public function all(Request $request): object
    {
        return $this->repository->index($request->user()->uuid);
    }

    /**
     * Update a user's settings
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'settings' => 'required|array'
        ]);

        $update = $this->preferenceService->handle($request->user()->uuid, $request->input('settings'));

        if ($update['success']) {
            return new JsonResponse([
                'data' => [
                    'success' => true,
                    'settings_updated' => $update['keys']
                ]
            ]);
        }

        return new JsonResponse([
            'data' => [
                'success' => false
            ],
            'errors' => [
                'keys' => 'Something went wrong'
            ]
        ]);

    }
}
