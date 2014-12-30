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

  // File extensions
  define('F_EXT_PHP', '.php');
  define('F_EXT_JS',  '.js');
  // Paths
  define('URL_CP_ROOT',   URL_ROOT.'cp/');
  define('URL_INFO_ROOT', URL_ROOT.'info/');
  // Root
  define('FN_COMMENT',        URL_ROOT.'comment'.F_EXT_PHP);
  define('FN_FEEDS',          URL_ROOT.'feeds'.F_EXT_PHP);
  define('FN_FILE_DOWNLOAD',  URL_ROOT.'file_download'.F_EXT_PHP);
  define('FN_FORGOT_PW',      URL_ROOT.'forgot_pw'.F_EXT_PHP);
  define('FN_INDEX',          URL_ROOT.'index'.F_EXT_PHP);
  define('FN_LOGIN',          URL_ROOT.'login'.F_EXT_PHP);
  define('FN_LOGOUT',         URL_ROOT.'logout'.F_EXT_PHP);
  define('FN_PLEASE_WAIT',    URL_ROOT.'please_wait'.F_EXT_PHP);
  define('FN_REGISTER',       URL_ROOT.'register'.F_EXT_PHP);
  define('FN_RESET_PW',       URL_ROOT.'reset_pw'.F_EXT_PHP);
  define('FN_SEARCH',         URL_ROOT.'search'.F_EXT_PHP);
  define('FN_SITEMAP',        URL_ROOT.'sitemap'.F_EXT_PHP);
  define('FN_SITEMAPINDEX',   URL_ROOT.'sitemapindex'.F_EXT_PHP);
  define('FN_SUBSCRIBE',      URL_ROOT.'subscribe'.F_EXT_PHP);
  define('FN_TAGMAP',         URL_ROOT.'tagmap'.F_EXT_PHP);
  define('FN_VIEW',           URL_ROOT.'view'.F_EXT_PHP);
  // Admin
  define('FN_ADM_ACCESS_LOG',          URL_CP_ROOT.'access_log'.F_EXT_PHP);
  define('FN_ADM_AREAS',               URL_CP_ROOT.'areas'.F_EXT_PHP);
  define('FN_ADM_AREA',                URL_CP_ROOT.'area'.F_EXT_PHP);
  define('FN_ADM_CHANGE_PASSWORD',     URL_CP_ROOT.'change_password'.F_EXT_PHP);
  define('FN_ADM_COMMENTS',            URL_CP_ROOT.'comments'.F_EXT_PHP);
  define('FN_ADM_COMMENTS_BULK',       URL_CP_ROOT.'comments_bulk'.F_EXT_PHP);
  define('FN_ADM_CONTENT_BULK',        URL_CP_ROOT.'content_bulk'.F_EXT_PHP);
  define('FN_ADM_CONTENT_EDITTAGS',    URL_CP_ROOT.'content_edittags'.F_EXT_PHP);
  define('FN_ADM_CONTENT_MANAGE',      URL_CP_ROOT.'content_manage'.F_EXT_PHP);
  define('FN_ADM_CONTENT_SETTINGS',    URL_CP_ROOT.'content_settings'.F_EXT_PHP);
  define('FN_ADM_EDIT_PROFILE',        URL_CP_ROOT.'edit_profile'.F_EXT_PHP);
  define('FN_ADM_ERROR_LOG',           URL_CP_ROOT.'error_log'.F_EXT_PHP);
  define('FN_ADM_ERROR_LOG_FILE',      URL_CP_ROOT.'error_log_file'.F_EXT_PHP);
  define('FN_ADM_FILES',               URL_CP_ROOT.'files'.F_EXT_PHP);
  define('FN_ADM_FILES_SETTINGS',      URL_CP_ROOT.'files_settings'.F_EXT_PHP);
  define('FN_ADM_FILES_SITE_UPLOAD',   URL_CP_ROOT.'files_site_upload'.F_EXT_PHP);
  define('FN_ADM_GENERAL_SETTINGS',    URL_CP_ROOT.'general_settings'.F_EXT_PHP);
  define('FN_ADM_MANAGE_AVATARS',      URL_CP_ROOT.'manage_avatars'.F_EXT_PHP);
  define('FN_ADM_MY_SETTINGS',         URL_CP_ROOT.'my_settings'.F_EXT_PHP);
  define('FN_ADM_PERMISSION',          URL_CP_ROOT.'permission'.F_EXT_PHP);
  define('FN_ADM_PERMISSIONS',         URL_CP_ROOT.'permissions'.F_EXT_PHP);
  define('FN_ADM_SPAM_RULE',           URL_CP_ROOT.'spam_rule'.F_EXT_PHP);
  define('FN_ADM_SPAM_RULES',          URL_CP_ROOT.'spam_rules'.F_EXT_PHP);
  define('FN_ADM_THEMES',              URL_CP_ROOT.'themes'.F_EXT_PHP);
  define('FN_ADM_TOOLS',               URL_CP_ROOT.'tools'.F_EXT_PHP);
  define('FN_ADM_TOOLS_SPAM_STATS',    URL_CP_ROOT.'tools_spam_stats'.F_EXT_PHP);
  define('FN_ADM_TOOLS_USER_SESSIONS', URL_CP_ROOT.'tools_user_sessions'.F_EXT_PHP);
  define('FN_ADM_UPLOAD_AVATAR',       URL_CP_ROOT.'upload_avatar'.F_EXT_PHP);
  define('FN_ADM_URL_SETTINGS',        URL_CP_ROOT.'url_settings'.F_EXT_PHP);
  define('FN_ADM_USER',                URL_CP_ROOT.'user'.F_EXT_PHP);
  define('FN_ADM_USER_CONTACT',        URL_CP_ROOT.'user_contact'.F_EXT_PHP);
  define('FN_ADM_USER_EDIT_PASSWORD',  URL_CP_ROOT.'user_edit_password'.F_EXT_PHP);
  define('FN_ADM_USER_ROLE',           URL_CP_ROOT.'user_role'.F_EXT_PHP);
  define('FN_ADM_USER_ROLE_MESSAGE',   URL_CP_ROOT.'user_role_message'.F_EXT_PHP);
  define('FN_ADM_USER_ROLE_USAGE',     URL_CP_ROOT.'user_role_usage'.F_EXT_PHP);
  define('FN_ADM_USER_ROLES',          URL_CP_ROOT.'user_roles'.F_EXT_PHP);
  define('FN_ADM_USERS',               URL_CP_ROOT.'users'.F_EXT_PHP);
  define('FN_ADM_WRITE',               URL_CP_ROOT.'write'.F_EXT_PHP);
  define('FN_ADM_INDEX',               URL_CP_ROOT.'index'.F_EXT_PHP);
  define('FN_ADMIN_TOOLS',             URL_CP_ROOT.'admin_tools'.F_EXT_PHP);
  define('FN_USER_TOOLS',              URL_CP_ROOT.'user_tools'.F_EXT_PHP);
  // Info
  define('FN_INF_CMS_CODES',     URL_INFO_ROOT.'cms_codes'.F_EXT_PHP);
  define('FN_INF_COOKIES',       URL_INFO_ROOT.'cookies'.F_EXT_PHP);
  // Special
  define('FN_SYS_CHLOADER',      URL_SYS_IFW.'chloader'.F_EXT_PHP);
