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
    
    error_reporting(E_ALL);
    ini_set('display_errors', true);
    date_default_timezone_set('Europe/London');
    
    // ** Query Time ** //
    
    /*
        ***** Test Mode *****
        Q_ALL   - Display Query Execution Time box
        Q_DB    - Only display database queries
        <empty> - Don't display the box
    */
    define('C_TEST_MODE', "");
    
    /*
        ***** Test Threshold *****
        4 - Display queries that reach L4
        3 - Display queries that reach L3, L4
        2 - Display queries that reach L2, L3, L4
        1 - Display queries that reach L1, L2, L3, L4
        0 - Display all queries
    */
    $intMaxLevel = 0;
    
    // System variables - Do not modify
    $strExecutionTime = "";
    $intNumQueries = 0;
    
    // Current directory - /sys
    $strSysDir = str_replace("\\", "/", dirname(__FILE__)."\\");
    
    // Root directory
    $strRootDir = str_replace("/sys", "", $strSysDir);
    
    // Core constants
    define('ABS_ROOT', $strRootDir);
    define('ABS_SYS_ROOT', $strSysDir);
    require 'SystemDirs.php'; // Must be loaded first
    
    // Other constants
    require ABS_SYS_CONSTANTS.'General.php';
    require ABS_SYS_CONSTANTS.'Characters.php';
    require ABS_SYS_CONSTANTS.'Filenames.php';
    require ABS_SYS_CONSTANTS.'AccessLogTags.php';
    require ABS_SYS_CONSTANTS.'AllowedTags.php'; // Used by the editor
    require ABS_SYS_CONSTANTS.'Buttons.php'; // Language file
    require ABS_SYS_CONSTANTS.'Messages.php'; // Language file
    
    // Misc
    require ABS_SYS_ROOT.'http.php';
    
    // Variables
    require ABS_ROOT.'data/secure/db_vars.php';

    // inj Framework v3
    function injAutoloader($className)
    {
        $filePath = str_replace('\\', '/', $className);
        $fullPath = sprintf('%slib/%s.php', ABS_ROOT, $filePath);
        if (file_exists($fullPath)) {
            require_once($fullPath);
        }
    }
    spl_autoload_register('injAutoloader');

    // Twig - experimental!
    $twigEngineEnabled = 1; // 1 = enabled
    $twigCacheEnabled  = 0; // 1 = enabled; disable for dev purposes

    // Third-party
    if ($twigEngineEnabled == 1) {
        if ($twigCacheEnabled == 1) {
            $envArray = array('cache' => ABS_ROOT.'data/cache');
        } else {
            $envArray = array();
        }
        require_once ABS_ROOT.'/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();
        $twigLoader = new Twig_Loader_Filesystem(ABS_ROOT.'themes');
        $cmsTemplateEngine = new Twig_Environment($twigLoader, $envArray);
    }

    // Framework - must be loaded before any classes
    require ABS_SYS_IFW.'IFWCore.php';
    require ABS_SYS_IFW.'Helper.php';
    require ABS_SYS_IFW.'CMS.php'; // Links everything together
    require ABS_SYS_IFW.'ICache.php';
    require ABS_SYS_IFW.'ICacheFile.php';
    require ABS_SYS_IFW.'ICacheBuild.php';
    require ABS_SYS_IFW.'IPacker.php';
    require ABS_SYS_IFW.'IQuery.php';
    require ABS_SYS_IFW.'RSSParser.php';
    require ABS_SYS_IFW.'Thumb.php';
    require ABS_SYS_IFW.'Challenge.php';
    
    // Application-logic
    require ABS_SYS_INCLUDES.'Cookie.php';
    require ABS_SYS_INCLUDES.'Error.php';
    require ABS_SYS_INCLUDES.'FileUpload.php';
    require ABS_SYS_INCLUDES.'Formatting.php';
    require ABS_SYS_INCLUDES.'Messaging.php';
    require ABS_SYS_INCLUDES.'PageLink.php';
    require ABS_SYS_INCLUDES.'PageNumber.php';
    require ABS_SYS_INCLUDES.'ReplaceConstants.php';
    require ABS_SYS_INCLUDES.'Restriction.php';
    require ABS_SYS_INCLUDES.'View.php';
    
    // Pages
    require ABS_SYS_INCLUDES."pages/Archives.php";
    
    // Database
    require ABS_SYS_DB.'AccessLog.php';
    require ABS_SYS_DB.'Area.php';
    require ABS_SYS_DB.'AreaTraverse.php';
    require ABS_SYS_DB.'Article.php';
    require ABS_SYS_DB.'Comment.php';
    require ABS_SYS_DB.'Connection.php';
    require ABS_SYS_DB.'File.php';
    require ABS_SYS_DB.'FormRecipient.php';
    require ABS_SYS_DB.'PermissionProfile.php';
    require ABS_SYS_DB.'Ratings.php';
    require ABS_SYS_DB.'SpamRule.php';
    require ABS_SYS_DB.'System.php';
    require ABS_SYS_DB.'Tags.php';
    require ABS_SYS_DB.'URLMapping.php';
    require ABS_SYS_DB.'User.php';
    require ABS_SYS_DB.'UserGroup.php';
    require ABS_SYS_DB.'UserSession.php';
    require ABS_SYS_DB.'UserStats.php';
    require ABS_SYS_DB.'UserVariable.php';
    require ABS_SYS_DB.'Widget.php';
    
    // HTML
    require ABS_SYS_HTML.'AdminPage.php';
    require ABS_SYS_HTML.'AreaContent.php';
    require ABS_SYS_HTML.'Autocode.php';
    require ABS_SYS_HTML.'DropDown.php';
    require ABS_SYS_HTML.'FeedTemplate.php';
    require ABS_SYS_HTML.'LoginPage.php';
    require ABS_SYS_HTML.'PageNumberNavigation.php';
    require ABS_SYS_HTML.'PluginDisplay.php';
    require ABS_SYS_HTML.'RSSBuilder.php';
    require ABS_SYS_HTML.'Theme.php';
    require ABS_SYS_HTML.'ThemeSetting.php';
    require ABS_SYS_HTML.'UserVariableDisplay.php';

    // Instantiate core classes
    $CMS = new CMS;
    $CMS->InitClasses();
    
    // Connect to database
    $CMS->IQ->Connect($strDBHost, $strDBSchema, $strDBAdminUser, $strDBAdminPass);
?>