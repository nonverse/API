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
        return Setting::query()->where('uuid', $uuid)->get();
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
    public function create($uuid, $key, $value): Setting
    {
        $setting = new Setting;
        $setting->uuid = $uuid;
        $setting->key = $key;
        $setting->value = $value;

        $query = $setting->save();

        return $setting;
    }

    /**
     * @inheritDoc
     */
    public function update($uuid, $key, $value)
    {

        try {
            $setting = Setting::query()->where([['uuid', $uuid], ['key', $key]])->firstOrFail();
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
