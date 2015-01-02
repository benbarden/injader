<?php


namespace Cms\Data\Setting;


class Setting
{
    private $settingId;
    private $preference;
    private $content;

    public function __construct($settingData)
    {
        $this->settingId  = $settingData['id'];
        $this->preference = $settingData['preference'];
        $this->content    = $settingData['content'];
    }

    public function getSettingId()
    {
        return $this->settingId;
    }

    public function getName()
    {
        return $this->preference;
    }

    public function getValue()
    {
        return $this->content;
    }

    // *** Setting names *** //
    // System (non-editable)
    const SETTING_CMS_VERSION = 'prefCMSVersion';

    // General
    const SETTING_SITE_TITLE = 'prefSiteTitle';
    const SETTING_SITE_DESC = 'prefSiteDescription';
    const SETTING_SITE_KEYWORDS = 'prefSiteKeywords';
    const SETTING_SITE_EMAIL = 'prefSiteEmail';
    const SETTING_SITE_HEADER = 'prefSiteHeader';
    const SETTING_DATE_FORMAT = 'prefDateFormat';
    const SETTING_TIME_FORMAT = 'prefTimeFormat';
    const SETTING_SERVER_TIME_OFFSET = 'prefServerTimeOffset';
    const SETTING_USER_REGISTRATION = 'prefUserRegistration';
    const SETTING_COOKIE_DAYS = 'prefCookieDays';
    const SETTING_DEFAULT_THEME = 'prefDefaultTheme';

    // Content
    const SETTING_TAG_THRESHOLD = 'prefTagThreshold';
    const SETTING_RSS_COUNT = 'prefRSSCount';
    const SETTING_DISQUS_ID = 'prefDisqusId';

    // Notifications
    const SETTING_ARTICLE_NOTIFY_ADMIN = 'prefArticleNotifyAdmin';
    const SETTING_ARTICLE_REVIEW_EMAIL = 'prefArticleReviewEmail';

    // Files
    const SETTING_THUMB_SMALL = 'prefThumbSmall';
    const SETTING_THUMB_MEDIUM = 'prefThumbMedium';
    const SETTING_THUMB_LARGE = 'prefThumbLarge';
    const SETTING_THUMB_KEEPASPECT = 'prefThumbKeepAspect';
    const SETTING_ATTACH_MAX_SIZE = 'prefAttachMaxSize';
    const SETTING_AVATARS_PER_USER = 'prefAvatarsPerUser';
    const SETTING_AVATAR_SIZE = 'prefAvatarSize';
    const SETTING_AVATAR_MAX_SIZE = 'prefAvatarMaxSize';
    const SETTING_DIR_AVATARS = 'prefDirAvatars';
    const SETTING_DIR_SITE_IMAGES = 'prefDirSiteImages';
    const SETTING_DIR_MISC = 'prefDirMisc';

    // Links
    const SETTING_LINK_STYLE = 'prefLinkStyle';
}