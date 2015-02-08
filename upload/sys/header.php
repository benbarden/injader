<?php
/*
  Injader
  Copyright (c) 2005-2015 Ben Barden


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

    if (strpos($_SERVER['REQUEST_URI'], '/installer/install.php') !== false) {
        $isInstalling = true;
    } else {
        $isInstalling = false;
    }
    
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

    // inj Framework v3: System Constants
    $tempPath = dirname(__FILE__).'/../lib/Cms/Core/Constants';
    require $tempPath.'/System.php';

    // Other constants
    require ABS_ROOT.'sys/includes/constants/General.php';
    require ABS_ROOT.'sys/includes/constants/Characters.php';
    require ABS_ROOT.'sys/includes/constants/Filenames.php';
    require ABS_ROOT.'sys/includes/constants/AccessLogTags.php';
    require ABS_ROOT.'sys/includes/constants/AllowedTags.php'; // Used by the editor
    require ABS_ROOT.'sys/includes/constants/Buttons.php'; // Language file
    require ABS_ROOT.'sys/includes/constants/Messages.php'; // Language file
    
    // Misc
    require ABS_ROOT.'sys/http.php';
    
    // Variables
    require ABS_ROOT.'data/secure/db_vars.php';

    // inj Framework v3: Autoloader
    require ABS_ROOT.'lib/Cms/Core/Autoloader/Base.php';

    // Twig setup
    $twigCacheEnabled  = 0; // 1 = enabled; disable for dev purposes

    // Third-party
    if (!$isInstalling) {
        $config = new \Cms\Core\Di\Config(ABS_ROOT.'data/secure/config.ini');
        $factory = new \Cms\Core\Di\Factory();
        $cmsContainer = $factory->buildContainer($config);
    }

    // Passwords
    require ABS_ROOT.'lib/Password/password.php';

    // Framework - must be loaded before any classes
    require ABS_ROOT.'sys/includes/ifw/IFWCore.php';
    require ABS_ROOT.'sys/includes/ifw/Helper.php';
    require ABS_ROOT.'sys/includes/ifw/CMS.php'; // Links everything together
    require ABS_ROOT.'sys/includes/ifw/ICache.php';
    require ABS_ROOT.'sys/includes/ifw/ICacheFile.php';
    require ABS_ROOT.'sys/includes/ifw/ICacheBuild.php';
    require ABS_ROOT.'sys/includes/ifw/IQuery.php';
    require ABS_ROOT.'sys/includes/ifw/RSSParser.php';
    require ABS_ROOT.'sys/includes/ifw/Thumb.php';
    
    // Application-logic
    require ABS_ROOT.'sys/includes/Cookie.php';
    require ABS_ROOT.'sys/includes/Error.php';
    require ABS_ROOT.'sys/includes/FileUpload.php';
    require ABS_ROOT.'sys/includes/Formatting.php';
    require ABS_ROOT.'sys/includes/Messaging.php';
    require ABS_ROOT.'sys/includes/PageLink.php';
    require ABS_ROOT.'sys/includes/PageNumber.php';
    require ABS_ROOT.'sys/includes/ReplaceConstants.php';
    require ABS_ROOT.'sys/includes/Restriction.php';
    require ABS_ROOT.'sys/includes/View.php';
    // Database
    require ABS_ROOT.'sys/includes/db/AccessLog.php';
    require ABS_ROOT.'sys/includes/db/Area.php';
    require ABS_ROOT.'sys/includes/db/AreaTraverse.php';
    require ABS_ROOT.'sys/includes/db/Article.php';
    require ABS_ROOT.'sys/includes/db/Category.php';
    require ABS_ROOT.'sys/includes/db/File.php';
    require ABS_ROOT.'sys/includes/db/PermissionProfile.php';
    require ABS_ROOT.'sys/includes/db/System.php';
    require ABS_ROOT.'sys/includes/db/Tags.php';
    require ABS_ROOT.'sys/includes/db/URLMapping.php';
    require ABS_ROOT.'sys/includes/db/User.php';
    require ABS_ROOT.'sys/includes/db/UserGroup.php';
    require ABS_ROOT.'sys/includes/db/UserSession.php';
    
    // HTML
    require ABS_ROOT.'sys/includes/html/AdminPage.php';
    require ABS_ROOT.'sys/includes/html/AreaContent.php';
    require ABS_ROOT.'sys/includes/html/Autocode.php';
    require ABS_ROOT.'sys/includes/html/DropDown.php';
    require ABS_ROOT.'sys/includes/html/FeedTemplate.php';
    require ABS_ROOT.'sys/includes/html/LoginPage.php';
    require ABS_ROOT.'sys/includes/html/PageNumberNavigation.php';
    require ABS_ROOT.'sys/includes/html/RSSBuilder.php';
    require ABS_ROOT.'sys/includes/html/Theme.php';
    require ABS_ROOT.'sys/includes/html/ThemeSetting.php';

    // Instantiate core classes
    $CMS = new CMS;
    $CMS->InitClasses();
    
    // Connect to database
    $CMS->IQ->Connect($strDBHost, $strDBSchema, $strDBAdminUser, $strDBAdminPass);

    // Page functions
    function showCpErrorPage($cmsContainer, $cpBindings, $errorMsg)
    {
        $cpBindings['Error'] = $errorMsg;
        $engine = $cmsContainer->getService('Theme.EngineCPanel');
        $outputHtml = $engine->render('core/cp-error.twig', $cpBindings);
        print($outputHtml);
        exit;
    }

    // Access permissions
    if (!$isInstalling) {
        if ($cmsContainer->hasService('Auth.CurrentUser')) {
            $currentUser = $cmsContainer->getService('Auth.CurrentUser');
            $accessPermission = new \Cms\Access\Permission($cmsContainer, $currentUser);
        }

        // CPanel bindings
        $cpBindings = array();

        $cpBindings['Auth']['IsAdmin'] = $CMS->RES->IsAdmin();
        $cpBindings['Auth']['CanWriteContent'] = $CMS->RES->CanAddContent();
    }
