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

  require 'sys/header.php';

  $blnGet = false;
  $blnSubmitForm = false;
  $strMissingUsername = "";
  $strMissingPassword = "";
  $strUsername = "";
  $strPassword = "";
  $strReferrer = "";
  
  if ($_POST) {
    $blnSubmitForm = true;
    if (!empty($_POST['txtUsername'])) {
      $strUsername = $CMS->AddSlashesIFW($CMS->FilterAlphanumeric($_POST['txtUsername'], C_CHARS_USERNAME));
      $strUsername = strip_tags($strUsername);
    }
    if (!empty($_POST['txtPassword'])) {
      $strPassword = $CMS->AddSlashesIFW($CMS->FilterAlphanumeric($_POST['txtPassword'], C_CHARS_USERNAME));
      $strPassword = strip_tags($strPassword);
    }
    if (!$strUsername) {
      $blnSubmitForm = false;
      $strMissingUsername = $CMS->AC->InvalidFormData("");
    }
    if (!$strPassword) {
      $blnSubmitForm = false;
      $strMissingPassword = $CMS->AC->InvalidFormData("");
    }
    $strReferrer = empty($_POST['txtReferrer']) ? "" : $CMS->FilterAlphanumeric($_POST['txtReferrer'], "\:\/\.");
  } else {
    $strReferrer = empty($_SERVER['HTTP_REFERER']) ? "" : $CMS->FilterAlphanumeric($_SERVER['HTTP_REFERER'], "\:\/\.");
    if (strpos($strReferrer, 'install.php') !== false) {
      $strReferrer = "";
    }
  }

  $blnAlreadyLoggedIn = false;
  $CMS->RES->ValidateLoggedIn();
  if (!$CMS->RES->IsError()) {
    if (!empty($_GET['redir'])) {
      $blnAlreadyLoggedIn = true;
      $intCurrentUserID = $CMS->RES->GetCurrentUserID();
      //$strRedirectURL = "http://".SVR_HOST.URL_ROOT.$strRedir;
    } else {
      $CMS->Err_MFail(M_ERR_ALREADY_LOGGED_IN, "");
    }
  }

  if ($blnSubmitForm) {
    if (!$blnGet) {
      $strPassword = md5($strPassword);
    }
    $intUserID = $CMS->US->ValidateLogin($strUsername, $strPassword);
    if ($intUserID) {
      if ($CMS->US->IsSuspended($intUserID)) {
        $CMS->Err_MFail(M_ERR_USER_SUSPENDED, "");
      }
      if (empty($_GET['redir'])) {
        $strRedirectURL = ""; //str_replace("index".F_EXT_PHP, "", FN_INDEX);
      } else {
        $strRedirectURL = "http://".SVR_HOST.URL_ROOT.$_GET['redir'];
      }
      if ($blnAlreadyLoggedIn) {
        if ($intCurrentUserID != $intUserID) {
          $CMS->US->Login($intUserID);
        }
      } else {
        $CMS->US->Login($intUserID);
      }
      if ($strRedirectURL) {
        httpRedirect($strRedirectURL);
        exit;
      }
      // ** Go back link ** //
      if ($strReferrer) {
        // Check logged in flag doesn't already exist
        if (strpos($strReferrer, "loggedin=1") === false) {
          // Add the flag to force a recache
          if (strpos($strReferrer, "?") !== false) {
            $strReferrer .= "&amp;loggedin=1";
          } else {
            $strReferrer .= "?loggedin=1";
          }
        }
        $strGoBack = "<li><a href=\"$strReferrer\">Go back to the page you were just viewing</a></li>";
      } else {
        $strGoBack = "";
      }
      // ** Display results ** //
      $strHTML = <<<END
<h1>Login Results</h1>
<p>You have successfully logged in.</p>
<ul>
$strGoBack
<li><a href="{FN_INDEX}">Go to the home page</a></li>
<li><a href="{FN_ADM_INDEX}">View or modify your account settings</a></li>
</ul>

END;
      $CMS->LP->SetTitle("Login Results");
      $CMS->LP->Display($strHTML);
    } else {
      $CMS->SYS->CreateAccessLog("Username: $strUsername", AL_TAG_USER_LOGIN_FAIL, 0, "");
      if ($CMS->RES->IsError()) {
        $CMS->Err_MFail(M_ERR_LOGIN_FAILED, "");
      }
    }
  }

  $strPageTitle = "Please enter your login details";
  
  $strLoginButton = $CMS->AC->LoginButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  if ($CMS->SYS->GetSysPref(C_PREF_ALLOW_PASSWORD_RESETS) == "Y") {
    $strResetPWLink = "<p>Help, <a href=\"{FN_FORGOT_PW}\">I forgot my password</a>!</p>";
  } else {
    $strResetPWLink = "";
  }
  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form action="{FN_LOGIN}" method="post">
<table class="OptionTable NarrowTable" cellspacing="1">
  <colgroup>
    <col class="NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <td class="InfoColour"><label for="txtUsername">Username:</label></td>
    <td>
      <input type="hidden" id="txtReferrer" name="txtReferrer" value="$strReferrer" />
      $strMissingUsername
      <input type="text" id="txtUsername" name="txtUsername" maxlength="45" size="35" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour"><label for="txtPassword">Password:</label></td>
    <td>
      $strMissingPassword
      <input type="password" id="txtPassword" name="txtPassword" maxlength="45" size="35" />
    </td>
  </tr>
  <tr>
    <td class="FootColour" colspan="2">$strLoginButton $strCancelButton</td>
  </tr>
</table>
</form>
$strResetPWLink
<p><b>Privacy Alert</b>: By logging in you accept that a cookie will be used to remember your details. You can clear the cookie at any time by logging out. Most web browsers will allow the cookie automatically, but if you have cookies disabled, you will not be able to log in. <a href="{FN_INF_COOKIES}">How to enable cookies</a></p>

END;

  $CMS->LP->SetTitle($strPageTitle);
  $CMS->LP->Display($strHTML);
?>