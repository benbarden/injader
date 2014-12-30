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
    $strWarning = "";
    if ($strMaxVersion == "x.x.x") {
        //
    }
    $IJP->Display("<h1>System Upgrade</h1>\n\n<p>You are about to upgrade your site from Injader $strVersion to Injader $strMaxVersion.</p>\n\n$strWarning<p><a href=\"upgrade.php\">Run the upgrade script</a>.</p>", $strPageTitle);
  }
  
?>