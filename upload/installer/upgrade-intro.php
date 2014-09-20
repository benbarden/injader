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

  require 'InjaderPage.php';
  $IJP = new InjaderPage;
  
  require '../sys/header.php';
  
  $CMS->Cache->ClearAll();
  
  $strVersion = $CMS->SYS->GetSysPref(C_PREF_CMS_VERSION);
  $strMaxVersion = C_SYS_LATEST_VERSION;
  $strPageTitle = "Injader - Upgrade to version $strMaxVersion";

  // Prevent upgrade to max version
  if ($strVersion == $strMaxVersion) {
    $IJP->Display("<h1>Upgrade Aborted</h1>\n\n<p>Your site cannot be upgraded because you're already running Injader $strMaxVersion.</p>\n\n<ul>\n<li>Target version: $strMaxVersion</li>\n<li>Your site: $strVersion</li>\n</ul>", $strPageTitle);
  } else {
    if ($strMaxVersion == "2.1.0") {
      $strWarning = "<h2>WARNING! DO NOT IGNORE THIS NOTICE!</h2>\n\n
        <p>This upgrade will permanently delete ALL of your site templates, 
        area templates, page templates, comment templates, and custom links.</p>\n\n
        <p>You are STRONGLY advised to backup your database BEFORE running this upgrade. 
        Please go to <a href=\"http://www.injader.com\">www.injader.com</a> if you need 
        help.</p>\n\n<h2>If you're absolutely sure you want to continue:</h2>\n\n";
    } else {
      $strWarning = "";
    }
    $IJP->Display("<h1>System Upgrade</h1>\n\n<p>You are about to upgrade your site from Injader $strVersion to Injader $strMaxVersion.</p>\n\n$strWarning<p><a href=\"upgrade.php\">Run the upgrade script</a>.</p>", $strPageTitle);
  }
  
?>