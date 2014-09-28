<?php


namespace Cms\Data\Setting;


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
                SELECT count(1) FROM maj_sys_preferences
                WHERE preference = :setting
            ");
            $pdoStatement->bindParam(':setting', $settingName);
            $pdoStatement->execute();
            return $pdoStatement->fetchColumn() > 0;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if row exists for '.$settingName, 0, $e);
        }
    }

    public function getSetting($settingName)
    {
        try {
            /* @var \PDOStatement $pdoStatement */
            $pdoStatement = $this->db->prepare("
                SELECT * FROM maj_sys_preferences
                WHERE preference = :setting
            ");
            $pdoStatement->bindParam(':setting', $settingName);
            $pdoStatement->execute();
            $dbData = $pdoStatement->fetch();
            $cmsSetting = new Setting($dbData);
            return $cmsSetting;
        } catch(\PDOException $e) {
            throw new \Exception('Failed to check if row exists for '. $settingName, 0, $e);
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

    public function getSettingSiteFavicon()
    {
        return $this->getSettingValue(Setting::SETTING_SITE_FAVICON);
    }

    public function getSettingRSSArticlesURL()
    {
        return $this->getSettingValue(Setting::SETTING_RSS_ARTICLES_URL);
    }

    public function getSettingSiteHeader()
    {
        return $this->getSettingValue(Setting::SETTING_SITE_HEADER);
    }

    public function saveSetting(Setting $setting)
    {

    }
} 