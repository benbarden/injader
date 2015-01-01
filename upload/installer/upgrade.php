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
  
    // Default .htaccess data
    $strHtaccessDefault = <<<htaccess
# BEGIN Injader
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^(.*)$ index.php/$1 [QSA,L]
</IfModule>

# END Injader
htaccess;

    // Build htaccess file
    @ $strHtaccessData = file_get_contents("../.htaccess");
    if (empty($strHtaccessData)) {
        $strOutput = $strHtaccessDefault;
        $strPathInfo = "Path 1";
    } else {
        if ((strpos($strHtaccessData, "# BEGIN Injader") !== false) &&
            (strpos($strHtaccessData, "# END Injader")   !== false)) {
            $arrStart = explode("# BEGIN Injader", $strHtaccessData);
            $arrEnd   = explode("# END Injader",   $strHtaccessData);
            $strOutput = $arrStart[0].$strHtaccessDefault.$arrEnd[1];
            $strPathInfo = "Path 2";
        } else {
            $strOutput = $strHtaccessDefault.$strHtaccessData;
            $strPathInfo = "Path 3";
        }
    }
    @ $objFile = fopen("../.htaccess", "w");
    fwrite($objFile, $strOutput);
    @ fclose($objFile);
    
  // Include header here as the directory may need changing in the previous step
  require '../sys/header.php';
  
  // Clear the cache or we'll get stuck on the same version when upgrading
  $CMS->Cache->ClearAll();

  // Default title to use if no error occurred
  $strVersion = $CMS->SYS->GetSysPref(C_PREF_CMS_VERSION);
  $strMaxVersion = C_SYS_LATEST_VERSION;
  $strPageTitle = "Injader - Upgrade to version $strMaxVersion";

  // Prevent upgrade to max version
  if ($strVersion == $strMaxVersion) {
    $IJP->Display("<h1>Upgrade Error</h1>\n\n<p>The upgrade script could not start because your site is already
    using Injader $strMaxVersion.</p>\n\n
    <p><i>Source: &lt;upgrade.php, version $strMaxVersion&gt;</i></p>", "Error");
  }

  // Upgrade switcher
  $blnFile = true;
  switch ($strVersion) {
    case "2.4.4": $strUpgradeTo = "2.4.5"; $blnFile = false; break;
    case "2.4.5": $strUpgradeTo = "2.5.0"; $blnFile = false; break;
    case "2.5.0": $strUpgradeTo = "3.0.0"; $blnFile = true; break;
    default:
      // Not a supported upgrade route
      $IJP->Display("<h1>Upgrade Error</h1>
        <p>You cannot upgrade from Injader $strVersion to Injader $strMaxVersion. 
        Please contact us for help.</p>
        <p><i>Source: &lt;upgrade.php, version $strMaxVersion&gt;</i></p>", "Error");
      break;
  }
  if ($blnFile) {
    $strFile = "sql/".$strUpgradeTo.".sql";
  } else {
    $strFile = "";
  }

  //////////////////////////////////////////////////////////////////////
  
  // Update to latest version
  if ($strUpgradeTo) {
    $CMS->Query("UPDATE {IFW_TBL_SETTINGS} SET content = '$strUpgradeTo'
    WHERE preference = '{C_PREF_CMS_VERSION}'", basename(__FILE__), __LINE__);
  }

  // Run SQL file, if there is one
  if ($strFile) {
    @ $strInstallFile = file_get_contents($strFile);
    if (!$strInstallFile) {
      $IJP->Display("<h1>Upgrade Error</h1>\n\n<p>Cannot open $strFile.</p>\n\n
      <p><i>Source: &lt;upgrade.php, version $strUpgradeTo&gt;</i></p>", "Error");
    }
    $blnSuccess = $CMS->MultiQuery($strInstallFile);
  }

    //////////////////////////////////////////////////////////////////////
    
    if ($strUpgradeTo == "x.x.x") {
        
        //
        
    }
    
    //////////////////////////////////////////////////////////////////////
  
  if ($strUpgradeTo == $strMaxVersion) {
    // Complete
    $strHTML = "<h1>Upgrade Complete!</h1>\n\n
    <p>You are now running Injader $strUpgradeTo.</p>\n\n
    <p><b>IMPORTANT:</b> Delete the installer folder from your site, 
    and unlock the system.</p>\n\n
    <p><a href=\"".FN_INDEX."\">Go to your site</a>.</p>";
  } else {
    // More to do
    $strHTML = "<h1>Multi-stage upgrade still in progress</h1>\n\n
    <p>The upgrade script is attempting to upgrade your site to version $strMaxVersion. 
    So far, it's reached version $strUpgradeTo. Please refresh the page to continue.
    </p>\n";
  }
  
  $CMS->Cache->ClearAll();
  
  $IJP->Display($strHTML, $strPageTitle);

?>