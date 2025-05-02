<?php
namespace App\Services;

use App\Models\Setting;

class SettingService
{

    public function getSettingById(int $id): ?Setting
    {
        return Setting::find($id);
    }

    public function createSetting(array $data): Setting
    {
        $station = Setting::create($data);
        return $station;
    }

    public function updateSetting($Setting, array $data)
    {
        $Setting->update($data);
        return $Setting;
    }


}
