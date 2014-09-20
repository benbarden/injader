<?php
/*
  Injader - Content management for everyone
  Copyright (c) 2005-2009 Ben Barden
  Please go to http://www.injader.com if you have questions or need help.

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  // Latest version of Injader
  define('C_SYS_LATEST_VERSION', '2.4.5');

  // Product
  define('PRD_PRODUCT_NAME', 'Injader');
  define('PRD_PRODUCT_URL',  'http://www.injader.com');
  define('PRD_COMPANY_NAME', 'Ben Barden');
  define('PRD_COMPANY_URL',  'http://www.benbarden.com');

  // Cookies
  define('C_CK_LOGIN',         'IJ-Login');
  define('C_CK_COMMENT_NAME',  'IJ-CommentName');
  define('C_CK_COMMENT_URL',   'IJ-CommentURL');
  define('C_CK_COMMENT_EMAIL', 'IJ-CommentEmail');

  // Content status
  define('C_CONT_PUBLISHED', 'Published');
  define('C_CONT_DRAFT',     'Draft');
  define('C_CONT_REVIEW',    'Review');
  define('C_CONT_SCHEDULED', 'Scheduled');
  define('C_CONT_PRIVATE',   'Private');
  define('C_CONT_DELETED',   'Deleted');
  
  // Allowed file types
  define('C_ALLOWED_FILE_TYPES', 'JPG,PNG,GIF,TXT,DOC,XLS,PPT,PDF,ZIP,MP3');
  
  // Spam rule types
  define('C_SPAMRULE_EMAIL',   'Email');
  define('C_SPAMRULE_URL',     'URL');
  define('C_SPAMRULE_NAME',    'Name');
  define('C_SPAMRULE_COMMENT', 'Comment');
  define('C_SPAMRULE_IP',      'IP');
  //define('C_SPAMRULE_ANY',     'Any');
  
  // Area types
  define('C_AREA_CONTENT', 'Content');
  define('C_AREA_LINKED',  'Linked');
  define('C_AREA_SMART',   'Smart');
  
  // Navigation types
  define('C_NAV_PRIMARY',   'Primary');
  define('C_NAV_SECONDARY', 'Secondary');
  define('C_NAV_TERTIARY',  'Tertiary');
  
  // Widget types
  define('C_WIDGET_DATA', 'Data');

  // Theme files
  define('C_TH_HEADER',     'header.php');
  define('C_TH_FOOTER',     'footer.php');
  define('C_TH_INDEX',      'index.php');
  define('C_TH_PAGE',       'page.php');
  define('C_TH_PROFILE',    'profile.php');
  define('C_TH_SETTINGS',   'settings.txt');
  define('C_TH_STYLESHEET', 'stylesheet.css');
  
  // Theme locations
  define('C_TL_INDEX',   'ThemeLocationIndex');
  define('C_TL_PAGE',    'ThemeLocationPage');
  define('C_TL_PROFILE', 'ThemeLocationProfile');
  define('C_TL_DEFAULT', 'ThemeLocationDefault');
  
  // System Settings (non-editable)
  define('C_PREF_CMS_VERSION', 'prefCMSVersion');

  // General Settings
  define('C_PREF_SITE_TITLE',            'prefSiteTitle');
  define('C_PREF_SITE_DESCRIPTION',      'prefSiteDescription');
  define('C_PREF_SITE_KEYWORDS',         'prefSiteKeywords');
  define('C_PREF_SITE_EMAIL',            'prefSiteEmail');
  define('C_PREF_SITE_HEADER',           'prefSiteHeader');
  define('C_PREF_RSS_ARTICLES_URL',      'prefRSSArticlesURL');
  define('C_PREF_SITE_FAVICON',          'prefSiteFavicon');
  define('C_PREF_SYSTEM_PAGE_COUNT',     'prefSystemPageCount');
  define('C_PREF_MAX_LOG_ENTRIES',       'prefMaxLogEntries');
  define('C_PREF_SYSTEM_LOCK',           'prefSystemLock');
  define('C_PREF_DATE_FORMAT',           'prefDateFormat');
  define('C_PREF_TIME_FORMAT',           'prefTimeFormat');
  define('C_PREF_SERVER_TIME_OFFSET',    'prefServerTimeOffset');
  define('C_PREF_USER_REGISTRATION',     'prefUserRegistration');
  define('C_PREF_USER_CHANGE_PASS',      'prefUserChangePass');
  define('C_PREF_ALLOW_PASSWORD_RESETS', 'prefAllowPasswordResets');
  define('C_PREF_COOKIE_DAYS',           'prefCookieDays');
  define('C_PREF_DEFAULT_THEME',         'prefDefaultTheme');

  // Content Settings
  define('C_PREF_TAG_THRESHOLD',        'prefTagThreshold');
  define('C_PREF_RSS_COUNT',            'prefRSSCount');

  // Comment Settings
  define('C_PREF_COMMENT_CAPTCHA',        'prefCommentCAPTCHA');
  define('C_PREF_COMMENT_USE_NOFOLLOW',   'prefCommentUseNoFollow');
  define('C_PREF_COMMENT_NOFOLLOW_LIMIT', 'prefCommentNoFollowLimit');
  
  // Notification Settings
  define('C_PREF_ARTICLE_NOTIFY_ADMIN',   'prefArticleNotifyAdmin');
  define('C_PREF_ARTICLE_REVIEW_EMAIL',   'prefArticleReviewEmail');
  define('C_PREF_COMMENT_REVIEW_EMAIL',   'prefCommentReviewEmail');
  define('C_PREF_COMMENT_NOTIFICATION',   'prefCommentNotification');
  define('C_PREF_COMMENT_NOTIFY_AUTHOR',  'prefCommentNotifyAuthor');

  // File Settings
  define('C_PREF_THUMB_SMALL',      'prefThumbSmall');
  define('C_PREF_THUMB_MEDIUM',     'prefThumbMedium');
  define('C_PREF_THUMB_LARGE',      'prefThumbLarge');
  define('C_PREF_THUMB_KEEPASPECT', 'prefThumbKeepAspect');
  define('C_PREF_ATTACH_MAX_SIZE',  'prefAttachMaxSize');
  define('C_PREF_AVATARS_PER_USER', 'prefAvatarsPerUser');
  define('C_PREF_AVATAR_SIZE',      'prefAvatarSize');
  define('C_PREF_AVATAR_MAX_SIZE',  'prefAvatarMaxSize');
  define('C_PREF_DIR_AVATARS',      'prefDirAvatars');
  define('C_PREF_DIR_SITE_IMAGES',  'prefDirSiteImages');
  define('C_PREF_DIR_MISC',         'prefDirMisc');

  // Link Settings
  define('C_PREF_LINK_STYLE',       'prefLinkStyle');

?>