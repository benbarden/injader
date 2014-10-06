<?php


namespace Cms\Data\Setting;

use Cms\Exception\Data\DataException;


class MockSettingRepository implements ISettingRepository
{
    private $settingName;

    public function __construct()
    {
        $this->settingName = Setting::SETTING_SITE_TITLE;
    }

    public function settingExists($settingName)
    {
        return $settingName == $this->settingName;
    }

    public function getSetting($settingName)
    {
        if ($this->settingExists($settingName)) {
            return new Setting(array(
                'id' => 1,
                'preference' => $this->settingName,
                'content' => 'Injader Unit Test Site'
            ));
        } else {
            throw new DataException(sprintf('Setting %s does not exist.', $settingName));
        }
    }

    public function saveSetting(Setting $setting)
    {
        // @todo
    }
} 