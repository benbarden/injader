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

  // Server variables
  define('SVR_LOCATION',  $_SERVER['PHP_SELF']);
  define('SVR_REQUEST',   $_SERVER['REQUEST_URI']);
  define('SVR_HOST',      $_SERVER['HTTP_HOST']);
  define('SVR_WWWROOT',   $_SERVER['DOCUMENT_ROOT']);
  // Root path MUST include a trailing forward slash!
define('URL_ROOT', "/");
  define('URL_CACHE',         URL_ROOT.'data/cache/');
  define('URL_CUSTOM',        URL_ROOT.'custom/');
  define('URL_SYS_ROOT',      URL_ROOT.'sys/');
  define('URL_SYS_IMAGES',    URL_SYS_ROOT.'images/');
  define('URL_SYS_INCLUDES',  URL_SYS_ROOT.'includes/');
  define('URL_SYS_THEMES',    URL_SYS_ROOT.'themes/');
  define('URL_SYS_CONSTANTS', URL_SYS_INCLUDES.'constants/');
  define('URL_SYS_DB',        URL_SYS_INCLUDES.'db/');
  define('URL_SYS_HTML',      URL_SYS_INCLUDES.'html/');
  define('URL_SYS_IFW',       URL_SYS_INCLUDES.'ifw/');
  define('URL_SYS_JQUERY',    URL_ROOT.'assets/js/jquery/');
  define('URL_SYS_TINYMCE',   URL_ROOT.'assets/js/tinymce/');
  // This won't work in unit testing
  define('URL_HTTP',          'http://'.SVR_HOST.URL_ROOT);
  // Absolute URLs
  define('ABS_CACHE',         ABS_ROOT.'data/cache/');
  define('ABS_SYS_IMAGES',    ABS_SYS_ROOT.'images/');
  define('ABS_SYS_INCLUDES',  ABS_SYS_ROOT.'includes/');
  define('ABS_SYS_THEMES',    ABS_SYS_ROOT.'themes/');
  define('ABS_SYS_CONSTANTS', ABS_SYS_INCLUDES.'constants/');
  define('ABS_SYS_DB',        ABS_SYS_INCLUDES.'db/');
  define('ABS_SYS_HTML',      ABS_SYS_INCLUDES.'html/');
  define('ABS_SYS_IFW',       ABS_SYS_INCLUDES.'ifw/');
  define('ABS_SYS_JQUERY',    ABS_ROOT.'assets/js/jquery/');
  define('ABS_SYS_TINYMCE',   ABS_ROOT.'assets/js/tinymce/');
  // This stops template constants from being parsed
  define('ZZZ_TEMP', 'DummyTextToBeReplaced');
?>