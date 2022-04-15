<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserBanService;
use App\Services\Admin\UserPardonService;
use App\Services\Admin\UserSuspensionService;
use App\Services\Admin\UserUpgradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Psy\Util\Json;

class UserAdministrationController extends Controller
{
    /**
     * @var UserUpgradeService
     */
    private $upgradeService;

    /**
     * @var UserSuspensionService
     */
    private $suspensionService;

    /**
     * @var UserBanService
     */
    private $banService;

    /**
     * @var UserPardonService
     */
    private $pardonService;

    public function __construct(
        UserUpgradeService $upgradeService,
        UserSuspensionService $suspensionService,
        UserBanService        $banService,
        UserPardonService     $pardonService
    )
    {
        $this->upgradeService = $upgradeService;
        $this->suspensionService = $suspensionService;
        $this->banService = $banService;
        $this->pardonService = $pardonService;
    }

    /**
     * Upgrade a user's account to Administrator
     *
     * @param $uuid
     * @return JsonResponse
     */
    public function upgrade($uuid): JsonResponse
    {

        $upgrade = $this->upgradeService->handle($uuid);

        if (!$upgrade['success']) {
            return new JsonResponse([
                'errors' => [
                    'suspension' => $upgrade['error']
                ]
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
            ]
        ]);
    }

    /**
     * Suspend a user's account
     *
     * @param Request $request
     * @param $uuid
     * @return JsonResponse
     */
    public function suspend(Request $request, $uuid): JsonResponse
    {

        $request->validate([
            'suspension_period' => 'required|integer'
        ]);

        $suspension = $this->suspensionService->handle($uuid, $request->input('suspension_period'));

        if (!$suspension['success']) {
            return new JsonResponse([
                'errors' => [
                    'suspension' => $suspension['error']
                ]
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
                'violation_ends_at' => $suspension['violation_ends_at']
            ]
        ]);
    }

    /**
     * Ban a user's account
     *
     * @param $uuid
     * @return JsonResponse
     */
    public function ban($uuid): JsonResponse
    {

        $ban = $this->banService->handle($uuid);

        if (!$ban['success']) {
            return new JsonResponse([
                'errors' => [
                    'suspension' => $ban['error']
                ]
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
            ]
        ]);
    }

    /**
     * Pardon a user's account
     *
     * @param $uuid
     * @return JsonResponse
     */
    public function pardon($uuid): JsonResponse
    {

        $pardon = $this->pardonService->handle($uuid);

        if (!$pardon['success']) {
            return new JsonResponse([
                'errors' => [
                    'suspension' => $pardon['error']
                ]
            ], 400);
        }

        return new JsonResponse([
            'data' => [
                'success' => true,
            ]
        ]);
    }
}
