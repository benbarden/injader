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
  
  // DETECT INSTALL DIRECTORY in case it has been overwritten
  $strFileURL = '../sys/SystemDirs.php';
  @ $arrFile = file($strFileURL);
  if ((count($arrFile) == 0) || (!$arrFile)) {
    $IJP->Display("<h1>Upgrade Error</h1>\n\n<p>SystemDirs.php cannot be found.</p>\n\n
      <p><em>Source: &lt;upgrade.php, version $strUpgradeTo&gt;</em></p>", "Error");
  }
  // Get current directory
  $arrInstallRelURL = explode("installer", $_SERVER['PHP_SELF']);
  $strInstallRelURL = $arrInstallRelURL[0];
  // Do stuff with the file
  $strFile = "";
  foreach ($arrFile as $strKey => $strData) {
    if (strpos($strData, "define('URL_ROOT',") > -1) {
      $strData = "define('URL_ROOT', \"$strInstallRelURL\");\r\n";
    }
    $strFile .= $strData;
  }
  // Write to file
  @ $cmsFile = fopen($strFileURL, 'w');
  if (!$cmsFile) {
    $IJP->Display("
      <h1>Upgrade Error</h1>\n\n<p>SystemDirs.php cannot be written to. 
      Please check the permissions on /sys/SystemDirs.php and try again.</p>\n\n
      <p><em>Source: &lt;upgrade.php, version $strUpgradeTo&gt;</em></p>", "Error");
  }
  fwrite($cmsFile, $strFile);
  fclose($cmsFile);
    
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
    $IJP->Display("<h1>Upgrade Error</h1>\n\n<p>The upgrade script could not start because your site is already using Injader $strMaxVersion.</p>\n\n<p><i>Source: &lt;upgrade.php, version $strMaxVersion&gt;</i></p>", "Error");
  }

  // Upgrade switcher
  $blnFile = true;
  switch ($strVersion) {
    case "2.0.0":
    case "2.0.1":
    case "2.0.2": $strUpgradeTo = "2.0.3"; break;
    case "2.0.3": $strUpgradeTo = "2.1.0"; break;
    case "2.1.0":
    case "2.1.1":
    case "2.1.2": $strUpgradeTo = "2.1.3"; break;
    case "2.1.3":
    case "2.1.4": $strUpgradeTo = "2.1.5"; break;
    case "2.1.5": $strUpgradeTo = "2.1.6"; break;
    case "2.1.6": $strUpgradeTo = "2.1.7"; break;
    case "2.1.7": $strUpgradeTo = "2.2.0"; break;
    case "2.2.0": $strUpgradeTo = "2.2.1"; break;
    case "2.2.1": $strUpgradeTo = "2.3.0"; break;
    case "2.3.0": $strUpgradeTo = "2.3.1"; break;
    case "2.3.1": $strUpgradeTo = "2.3.2"; break;
    case "2.3.2": $strUpgradeTo = "2.4";   break;
    case "2.4":   $strUpgradeTo = "2.4.1"; break;
    case "2.4.1": $strUpgradeTo = "2.4.2"; break;
    case "2.4.2": $strUpgradeTo = "2.4.3"; break;
    case "2.4.3": $strUpgradeTo = "2.4.4"; $blnFile = false; break;
    case "2.4.4": $strUpgradeTo = "2.4.5"; $blnFile = false; break;
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
    $CMS->Query("UPDATE {IFW_TBL_SYS_PREFERENCES} SET content = '$strUpgradeTo' 
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

  ///////////////////////////////////////////////////////////////
  
  if ($strUpgradeTo == "2.3.1") {
    // 1. Calculate guest email stats.
    $arrComments = $CMS->ResultQuery("SELECT guest_email, count(*) AS count 
    FROM {IFW_TBL_COMMENTS} WHERE guest_email <> '' GROUP BY guest_email
    ", basename(__FILE__), __LINE__);
    if (is_array($arrComments) && (count($arrComments) > 0)) {
      for ($i=0; $i<count($arrComments); $i++) {
        $strEmail = $arrComments[$i]['guest_email'];
        $intCount = $arrComments[$i]['count'];
        $CMS->Query("INSERT INTO {IFW_TBL_USER_STATS}(user_email, comment_count) 
        VALUES('$strEmail', $intCount)", basename(__FILE__), __LINE__);
      }
    }
    // 2. Calculate registered user stats.
    $arrComments = $CMS->ResultQuery("SELECT u.email, count(*) AS count 
    FROM ({IFW_TBL_COMMENTS} c, {IFW_TBL_USERS} u) 
    WHERE c.author_id = u.id AND author_id <> 0 AND email <> '' GROUP BY u.email
    ", basename(__FILE__), __LINE__);
    if (is_array($arrComments) && (count($arrComments) > 0)) {
      for ($i=0; $i<count($arrComments); $i++) {
        $strEmail = $arrComments[$i]['email'];
        $intCount = $arrComments[$i]['count'];
        $blnDoInsert = true;
        $arrTemp = $CMS->ResultQuery("SELECT comment_count FROM {IFW_TBL_USER_STATS} 
        WHERE user_email = '$strEmail'", basename(__FILE__), __LINE__);
        if (is_array($arrTemp) && (count($arrTemp) > 0)) {
          if ($arrTemp[0]['comment_count'] > 0) {
            $CMS->Query("UPDATE {IFW_TBL_USER_STATS} 
            SET comment_count = comment_count + $intCount 
            WHERE user_email = '$strEmail'", basename(__FILE__), __LINE__);
            $blnDoInsert = false;
          }
        }
        if ($blnDoInsert) {
          $CMS->Query("INSERT INTO {IFW_TBL_USER_STATS}(user_email, comment_count) 
          VALUES('$strEmail', $intCount)", basename(__FILE__), __LINE__);
        }
      }
    }
  }
  
    //////////////////////////////////////////////////////////////////////
    
    if ($strUpgradeTo == "2.4.2") {
        
        $arrDataWidgets = $CMS->ResultQuery("
            SELECT * FROM ".IFW_TBL_PFX."plugins
        ");
        
        for ($i=0; $i<count($arrDataWidgets); $i++) {
            
            $strQuery = sprintf("
                INSERT INTO {IFW_TBL_WIDGETS}
                (name, version, conn_id, ucp_link, acp_link,
                query_string, item_limit, widget_variable,
                widget_template, widget_type)
                VALUES('%s', '%s', %s, '%s', '%s', '%s', %s, '%s', '%s', '%s')
            ",
                mysql_escape_string($arrDataWidgets[$i]['name']),
                mysql_escape_string($arrDataWidgets[$i]['version']),
                mysql_escape_string($arrDataWidgets[$i]['conn_id']),
                mysql_escape_string($arrDataWidgets[$i]['ucp_link']),
                mysql_escape_string($arrDataWidgets[$i]['acp_link']),
                mysql_escape_string($arrDataWidgets[$i]['query_string']),
                mysql_escape_string($arrDataWidgets[$i]['item_limit']),
                mysql_escape_string($arrDataWidgets[$i]['plugin_variable']),
                mysql_escape_string($arrDataWidgets[$i]['plugin_template']),
                C_WIDGET_DATA
            );
            
            $CMS->Query($strQuery, basename(__FILE__), __LINE__);
            
        }
        
        $CMS->Query("DROP TABLE IF EXISTS ".IFW_TBL_PFX."plugins",
            basename(__FILE__), __LINE__
        );
        /*
        $CMS->Query("DROP TABLE IF EXISTS ".IFW_TBL_PFX."extensions",
            basename(__FILE__), __LINE__
        );
        */
    }
    
    //////////////////////////////////////////////////////////////////////
    
    if ($strUpgradeTo == "2.4.3") {
        
        $CMS->UM->rebuildAll();
        
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