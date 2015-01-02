<?php


namespace Cms\Data\Setting;

use Cms\Exception\Data\DataException;


class SettingRepository implements ISettingRepository
{
    private $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function settingExists($settingName)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT count(1) FROM Cms_Settings
                WHERE preference = :setting
            ");
            $pdoStatement->bindParam(':setting', $settingName);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if row exists for '.$settingName, 0, $e);
        }
    }

    public function getSetting($settingName)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM Cms_Settings
                WHERE preference = :setting
            ");
            $pdoStatement->bindParam(':setting', $settingName);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsSetting = new Setting($dbData);
            return $cmsSetting;
        } catch(\PDOException $e) {
            throw new DataException('Failed to check if row exists for '. $settingName, 0, $e);
        }
    }

    public function getSettingValue($settingName)
    {
        $cmsSetting = $this->getSetting($settingName);
        return $cmsSetting->getValue();
    }

    public function getSettingSiteTitle()
    {
        return $this->getSettingValue(Setting::SETTING_SITE_TITLE);
    }

    public function getSettingSiteDesc()
    {
        return $this->getSettingValue(Setting::SETTING_SITE_DESC);
    }

    public function getSettingSiteKeywords()
    {
        return $this->getSettingValue(Setting::SETTING_SITE_KEYWORDS);
    }

    public function getSettingSiteHeader()
    {
        return $this->getSettingValue(Setting::SETTING_SITE_HEADER);
    }

    public function getSettingLinkStyle()
    {
        return $this->getSettingValue(Setting::SETTING_LINK_STYLE);
    }

    public function getSettingDateFormat()
    {
        return $this->getSettingValue(Setting::SETTING_DATE_FORMAT);
    }

    public function getSettingTimeFormat()
    {
        return $this->getSettingValue(Setting::SETTING_TIME_FORMAT);
    }

    public function getSettingDisqusId()
    {
        return $this->getSettingValue(Setting::SETTING_DISQUS_ID);
    }

    public function getDateFormat()
    {
        $settingDateFormat = $this->getSettingDateFormat();
        if (!$settingDateFormat) $settingDateFormat = 1;
        $settingTimeFormat = $this->getSettingTimeFormat();
        if (!$settingTimeFormat) $settingTimeFormat = 0;

        switch ($settingDateFormat) {
            case 1: $dateFormat = "F j, Y"; break; // September 16, 2007
            case 2: $dateFormat = "j F, Y"; break; // 16 September, 2007
            case 3: $dateFormat = "d/m/Y";  break; // 16/09/2007
            case 4: $dateFormat = "m/d/Y";  break; // 09/16/2007
            case 5: $dateFormat = "Y/m/d";  break; // 2007/09/16
            case 6: $dateFormat = "Y-m-d";  break; // 2007-09-16
            case 7: $dateFormat = "Y/d/m";  break; // 2007/16/09
            case 8: $dateFormat = "Y-d-m";  break; // 2007-16-09
        }

        switch ($settingTimeFormat) {
            case 1: $timeFormat = "H:i";    break; // 24H
            case 2: $timeFormat = "H:i:s"; break; // 24H with seconds
            case 3: $timeFormat = "g:i A"; break; // 12H followed by AM or PM
            default: $timeFormat = "";        break; // Do not display time
        }

        $combinedFormat = $dateFormat;
        if ($timeFormat) {
            $combinedFormat .= ' '.$timeFormat;
        }
        return $combinedFormat;
    }

    public function saveSetting(Setting $setting)
    {

    }
} 