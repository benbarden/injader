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
  $intStep = isset($_GET['step']) ? $_GET['step'] : null;
  if (!$intStep) {
    $intStep = 1;
  }
  $strPageTitle = "Injader Installer - Step $intStep";
  global $blnInstalling;
  $blnInstalling = true; // Required to prevent problems with scheduling
  // Initially, some external files will be missing
  switch ($intStep) {
    case 1:
    case 2:
    case 3:
      break;
    case 4:
      require '../sys/header.php';
      break;
  }
  // Instantiate
switch ($intStep) {
    case 2:
        $strDBHost        = "";
        $strDBSchema      = "";
        $strDBAdminUser   = "";
        $strDBAdminPass   = "";
        $strMajesticUser  = "";
        $strMajesticPass  = "";
        break;
}

  // POST stuff
  if ($_POST) {
    switch ($intStep) {
      case 1:
      case 2:
        $strDBHost        = $_POST['txtDBHost'];
        $strDBSchema      = $_POST['txtDBSchema'];
        $strDBAdminUser   = $_POST['txtDBAdminUser'];
        $strDBAdminPass   = $_POST['txtDBAdminPass'];
        $strMajesticUser  = $_POST['txtMajesticUser'];
        $strMajesticPass  = $_POST['txtMajesticPass'];
        $blnSubmitForm    = true;
        $blnMissingHost   = false;
        $blnMissingSchema = false;
        $blnMissingUser   = false;
        $blnMissingPass   = false;
        $blnMissingMajesticUser = false;
        $blnMissingMajesticPass = false;
        if (!$strDBHost) {
          $blnMissingHost = true;
          $blnSubmitForm = false;
        }
        if (!$strDBSchema) {
          $blnMissingSchema = true;
          $blnSubmitForm = false;
        }
        if (!$strDBAdminUser) {
          $blnMissingUser = true;
          $blnSubmitForm = false;
        }
        //if (!$strDBAdminPass) {
        //  $blnMissingPass = true;
        //  $blnSubmitForm = false;
        //}
        if (!$strMajesticUser) {
          $blnMissingMajesticUser = true;
          $blnSubmitForm = false;
        }
        if (!$strMajesticPass) {
          $blnMissingMajesticPass = true;
          $blnSubmitForm = false;
        }
        if ($blnSubmitForm) {
          $intStep = 3; // OK to proceed
        } else {
          $intStep = 2;
        }
        break;
      case 3:
      case 4:
        break;
    }
  }
  
    // Do the dirty work!
    switch ($intStep) {
        
        case 1:
            
            // Create .htaccess
            @ $blnCreatedFile = touch("../.htaccess");
            if (!$blnCreatedFile) {
                $IJP->Display("<h1>Installation Error</h1>
                <p>Cannot write to file: .htaccess</p>
                <p>Please check the permissions on the Injader installation directory.</p>
                <p><i>Source: &lt;install.php, step $intStep&gt;</i></p>
                ", "Error");
            }
            
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
            
            // Get system constants
            $strFileURL = '../sys/SystemDirs.php';
            @ $arrFile = file($strFileURL);
            if ((count($arrFile) == 0) || (!$arrFile)) {
                $IJP->Display("<h1>Installation Error</h1>
                <p>SystemDirs.php cannot be found.</p>
                <p><i>Source: &lt;install.php, step $intStep&gt;</i></p>
                ", "Error");
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
                $IJP->Display("<h1>Installation Error</h1>
                <p>Cannot write to /sys/SystemDirs.php - 
                please check the permissions and try again.</p>
                <p><i>Source: &lt;install.php, step $intStep&gt;</i></p>
                ", "Error");
            }
            fwrite($cmsFile, $strFile);
            fclose($cmsFile);
            $IJP->Display("<h1>Installation - Step 1</h1>
            <p>The installer has detected where Injader has been uploaded.
            <a href=\"?step=2\">Proceed to step 2</a>.</p>
            ", $strPageTitle);
            break;
            
    case 2:
      // PROCESSING FOR STEP 2:
      if (!$strDBHost) {
        $strDBHost = "localhost";
      }
      $strTDStyles = "background-color: #fff; color: #000; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 100%; padding: 5px; width: 200px;";
      $strInputStyles = "font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 100%;";
      $strErrorStyles = "background-color: transparent; color: #f00; display: none; font-size: 100%; font-style: italic;";
      $strHTML = <<<STEP2
<h1>Installation - Step 2</h1>
<p>Prior to running the installer, you need to set up a MySQL database on your web site. Enter the details below. The Injader username and password will allow you to log in once Injader has been installed. Don&apos;t forget the password, as this account will give you admin access.</p>
<form name="frmInstall2" action="?step=2" method="post">
<table style="background-color: #000; border: 1px solid #000; color: #fff;" cellspacing="1">
  <tr>
    <td style="$strTDStyles">Host (<i>usually <b>localhost</b></i>):</td>
    <td style="$strTDStyles">
      <span id="strMissingHost" style="$strErrorStyles">Please enter the host name.</span>
      <input id="txtDBHost" name="txtDBHost" style="$strInputStyles" type="text" size="20" maxlength="50" value="$strDBHost" />
    </td>
  </tr>
  <tr>
    <td style="$strTDStyles">Database:</td>
    <td style="$strTDStyles">
      <span id="strMissingSchema" style="$strErrorStyles">Please enter the database name.</span>
      <input id="txtDBSchema" name="txtDBSchema" style="$strInputStyles" type="text" size="20" maxlength="50" value="$strDBSchema" />
    </td>
  </tr>
  <tr>
    <td style="$strTDStyles">Database Username:</td>
    <td style="$strTDStyles">
      <span id="strMissingUser" style="$strErrorStyles">Please enter the admin username.</span>
      <input id="txtDBAdminUser" name="txtDBAdminUser" style="$strInputStyles" type="text" size="20" maxlength="50" value="$strDBAdminUser" />
    </td>
  </tr>
  <tr>
    <td style="$strTDStyles">Database Password:</td>
    <td style="$strTDStyles">
      <span id="strMissingPass" style="$strErrorStyles">Please enter the admin password.</span>
      <input id="txtDBAdminPass" name="txtDBAdminPass" style="$strInputStyles" type="text" size="20" maxlength="50" value="$strDBAdminPass" />
    </td>
  </tr>
  <tr>
    <td style="$strTDStyles">Injader Username:</td>
    <td style="$strTDStyles">
      <span id="strMissingMajesticUser" style="$strErrorStyles">Please enter the Injader username.</span>
      <input id="txtMajesticUser" name="txtMajesticUser" style="$strInputStyles" type="text" size="20" maxlength="50" value="$strMajesticUser" />
    </td>
  </tr>
  <tr>
    <td style="$strTDStyles">Injader Password:</td>
    <td style="$strTDStyles">
      <span id="strMissingMajesticPass" style="$strErrorStyles">Please enter the Injader password.</span>
      <input id="txtMajesticPass" name="txtMajesticPass" style="$strInputStyles" type="text" size="20" maxlength="50" value="$strMajesticPass" />
    </td>
  </tr>
  <tr>
    <td style="$strTDStyles text-align: right;" colspan="2">
      <input type="submit" value="Proceed" />
    </td>
  </tr>
</table>
</form>
STEP2;
      if ($_POST) {
        $strHTML .= "<script type=\"text/javascript\">\n";
        if ($blnMissingHost) {
          $strHTML .= "  document.getElementById('strMissingHost').style.display = 'block';\n";
        }
        if ($blnMissingSchema) {
          $strHTML .= "  document.getElementById('strMissingSchema').style.display = 'block';\n";
        }
        if ($blnMissingUser) {
          $strHTML .= "  document.getElementById('strMissingUser').style.display = 'block';\n";
        }
        if ($blnMissingPass) {
          $strHTML .= "  document.getElementById('strMissingPass').style.display = 'block';\n";
        }
        if ($blnMissingMajesticUser) {
          $strHTML .= "  document.getElementById('strMissingMajesticUser').style.display = 'block';\n";
        }
        if ($blnMissingMajesticPass) {
          $strHTML .= "  document.getElementById('strMissingMajesticPass').style.display = 'block';\n";
        }
        $strHTML .= "</script>\n";
      }
      $IJP->Display($strHTML, $strPageTitle);
      break;
    case 3:
      // PROCESSING FOR STEP 3:
      // Create file
      $strFileURL = '../data/secure/db_vars.php';
      touch($strFileURL);
      // Create file data
      $strDBHost      = "'".$strDBHost."'";
      $strDBSchema    = "'".$strDBSchema."'";
      $strDBAdminUser = "'".$strDBAdminUser."'";
      $strDBAdminPass = "'".$strDBAdminPass."'";
      $strFile = <<<DBVARS
<?php
  // Database variables
  \$strDBHost       = $strDBHost;
  \$strDBSchema     = $strDBSchema;
  \$strDBAdminUser  = $strDBAdminUser;
  \$strDBAdminPass  = $strDBAdminPass;
?>
DBVARS;
      // Write to file
      @ $cmsFile = fopen($strFileURL, 'w');
      if (!$cmsFile) {
        $IJP->Display("<h1>Installation Error</h1>\n\n<p>db_vars.php cannot be written to. Please check the permissions on the /data/secure/ directory and try again.</p>\n\n<p><i>Source: &lt;install.php, step $intStep&gt;</i></p>", "Error");
      }
      fwrite($cmsFile, $strFile);
      fclose($cmsFile);
      // Test connection
      include $strFileURL;
      mysql_connect($strDBHost, $strDBAdminUser, $strDBAdminPass)
        or die($IJP->Display("<h1>Connection Error</h1>\n\n<p>Access denied for user $strDBAdminUser at host $strDBHost. Please <a href=\"javascript:history.go(-1);\">go back</a> and try again.</p>\n\n<p><i>Source: &lt;install.php, step $intStep&gt;</i></p>", "Error"));
      mysql_select_db($strDBSchema) or die($IJP->Display("<h1>Connection Error</h1>\n\n<p>Cannot select database $strDBSchema. Please <a href=\"javascript:history.go(-1);\">go back</a> and try again.</p>\n\n<p><i>Source: &lt;install.php, step $intStep&gt;</i></p>", "Error"));
      // Store username and password for next step
      $strMajesticUser = $_POST['txtMajesticUser'];
      $strMajesticPass = $_POST['txtMajesticPass'];
      // Success
      $IJP->Display("<h1>Installation - Step 3</h1>\n\n<p>The installer has successfully connected to the database.</p><form name=\"frmInstall3\" action=\"?step=4\" method=\"post\"><input type=\"hidden\" name=\"txtMajesticUser\" value=\"$strMajesticUser\" /><input type=\"hidden\" name=\"txtMajesticPass\" value=\"$strMajesticPass\" /><input type=\"submit\" value=\"Proceed\" /></form>", $strPageTitle);
      break;
    case 4:
      // PROCESSING FOR STEP 4:
      $strFile = "install_base.sql";
      @ $strInstallFile = file_get_contents($strFile);
      if (!$strInstallFile) {
        $IJP->Display("<h1>Installation Error</h1>\n\n<p>Cannot open $strFile.</p>\n\n<p><i>Source: &lt;install.php, step $intStep&gt;</i></p>", "Error");
      }
      $blnSuccess = $CMS->MultiQuery($strInstallFile);
      if (!$blnSuccess) {
        $IJP->Display("<h1>Installation Error</h1>\n\n<p>Base install failed.</p>\n\n<p><i>Source: &lt;install.php, step $intStep&gt;</i></p>", "Error");
      }
      // Store username and password
      $strMajesticUser = addslashes($_POST['txtMajesticUser']);
      $pwHash = password_hash($_POST['txtMajesticPass'], PASSWORD_BCRYPT);
      // Create user
      $intNewUserID = $CMS->Query("
        INSERT INTO {IFW_TBL_USERS}(username, userpass, forename, surname, email, location, occupation, interests, homepage_link, homepage_text, avatar_id, user_groups, user_moderate)
        VALUES ('$strMajesticUser', '$pwHash', '', '', 'admin@yoursite.com', '', '', '', '', '', 0, '1|2|3', 'N')
      ", basename(__FILE__), __LINE__);
      // Build link mapping table
      $CMS->UM->rebuildAll();
      // ** Confirm completion ** //
      $IJP->Display("<h1>Installation - Step 4</h1>\n\n<p>Installation completed successfully.</p>\n\n<p><b>IMPORTANT:</b> Please complete the following steps before continuing:</p>\n\n<ul>\n<li>CHMOD the data/secure directory to 755</li>\n<li>Delete the installer directory</li>\n</ul>\n<p>After completing these steps, <a href=\"".FN_LOGIN."\">login to start setting up your site</a>.</p>", $strPageTitle);

      break;
  } // end of switch statement
