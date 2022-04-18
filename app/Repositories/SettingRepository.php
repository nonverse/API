<?php

namespace App\Repositories;

use App\Contracts\Repository\SettingRepositoryInterface;
use App\Models\Setting;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

class SettingRepository implements SettingRepositoryInterface
{
    public function index($uuid): object
    {
        return Setting::query()->find($uuid)->get();
    }

//    /**
//     * @inheritDoc
//     */
//    public function get($uuid, $key)
//    {
//        return Setting::query()->find($uuid)->where('key', $key)->first();
//    }

    /**
     * @inheritDoc
     */
    public function create($uuid, array $data): Setting
    {
        $setting = new Setting;
        $setting->uuid = $uuid;
        $setting->key = $data['key'];
        $setting->value = $data['value'];

        $query = $setting->save();

        return $setting;
    }

    /**
     * @inheritDoc
     */
    public function update($uuid, $key, $value)
    {

        try {
            $setting = Setting::query()->findOrFail($uuid)->where('key', $key)->first();
        } catch (ModelNotFoundException $e) {
            return false;
        }

        try {
            $setting->fill([
                'value' => $value
            ]);
            if ($setting->isDirty()) {
                $setting->save();
            }
        } catch (QueryException $e) {
            return false;
        }

        return $setting;
    }
}
