<?php


namespace Cms\Data\Setting;


interface ISettingRepository
{
    public function settingExists($settingName);
    public function getSetting($settingName);
    public function saveSetting(Setting $setting);
} 