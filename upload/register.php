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

  require 'sys/header.php';
  if ($CMS->SYS->GetSysPref(C_PREF_USER_REGISTRATION) != "1") {
    $CMS->Err_MFail(M_ERR_REGISTRATION_DISABLED, "");
  }
  $CMS->RES->ValidateLoggedIn();
  if (!$CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_REGISTER_WHILE_LOGGED_IN, "");
  }
  $strPageTitle = "Register";

  $strMissingUsername = "";
  $strDuplicateUsername = "";
  $strUsernameTooShort = "";
  $strUsernameInvalidChars = "";
  $strUsername = "";
  $strPassword = "";
  $strMissingPassword = "";
  $strPWInvalidChars = "";
  $strForename = "";
  $strSurname = "";
  $strMissingEmail = "";
  $strEmailInUse = "";
  $strEmail = "";
  $strInvalidCAPTCHA = "";

  if ($_POST) {
    // Prevent multiple usernames being created in a row
    // Disabled for now.
    /*
    $intUserIP = $_SERVER['REMOTE_ADDR'];
    $intNewestUserIP = $CMS->US->GetNewestUserIP();
    if (($intNewestUserIP != 0) && ($intNewestUserIP != "")) {
      if ($intUserIP == $intNewestUserIP) {
        $CMS->Err_MFail(M_ERR_REGISTER_MULTIPLE, "");
      }
    }
    */
    if (!empty($_POST['txtRegisterUsername'])) {
      $strUsername = $CMS->AddSlashesIFW($CMS->FilterAlphanumeric($_POST['txtRegisterUsername'], C_CHARS_USERNAME));
    }
    if (!empty($_POST['txtRegisterPassword'])) {
      $strPassword = $CMS->AddSlashesIFW($_POST['txtRegisterPassword']);
    }
    if (!empty($_POST['txtForename'])) {
      $strForename = $CMS->AddSlashesIFW($_POST['txtForename']);
      $strForename = strip_tags($strForename);
    }
    if (!empty($_POST['txtSurname'])) {
      $strSurname = $CMS->AddSlashesIFW($_POST['txtSurname']);
      $strSurname = strip_tags($strSurname);
    }
    if (!empty($_POST['txtEmail'])) {
      $strEmail = $CMS->AddSlashesIFW($_POST['txtEmail']);
      $strEmail = strip_tags($strEmail);
    }
    $blnSubmitForm   = true;
    // Check for missing username
    if (!$strUsername) {
      $strMissingUsername = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    } else {
      // Check username doesn't already exist
      if (!$CMS->US->IsUniqueUsername($strUsername)) {
        $strDuplicateUsername = $CMS->AC->InvalidFormData(M_ERR_USERNAME_IN_USE);
        $blnSubmitForm = false;
      }
      // Check for invalid username
      if (!$CMS->US->IsUsernameLengthValid($strUsername)) {
        $strUsernameTooShort = $CMS->AC->InvalidFormData(M_ERR_USERNAME_TOO_SHORT);
        $blnSubmitForm = false;
      } else {
        $strFilteredUsername = $CMS->FilterAlphanumeric($strUsername, C_CHARS_USERNAME);
        if ($strFilteredUsername != $strUsername) {
          $strUsernameInvalidChars = $CMS->AC->InvalidFormData(M_ERR_INVALID_CHARS);
          $blnSubmitForm = false;
          $strUsername = $strFilteredUsername;
        }
      }
    }
    // Check for missing password
    if (!$strPassword) {
      $strMissingPassword = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    } else {
      // Check for invalid password characters
      $strFilteredPW = $CMS->FilterAlphanumeric($strPassword, C_CHARS_USERNAME);
      if ($strFilteredPW != $strPassword) {
        $strPWInvalidChars = $CMS->AC->InvalidFormData(M_ERR_INVALID_CHARS);
        $blnSubmitForm = false;
        $strPassword = "";
      }
    }
    // Check for missing email
    if (!$strEmail) {
      $strMissingEmail = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    } else {
      // Check e-mail address isn't already in use
      if (!$strMissingEmail) {
        if (!$CMS->US->IsUniqueEmail($strEmail)) {
          $strEmailInUse = $CMS->AC->InvalidFormData(M_ERR_EMAIL_IN_USE);
          $blnSubmitForm = false;
        }
      }
    }
    // Proceed if there were no errors
    if ($blnSubmitForm) {
      $strPageTitle .= " - Results";
      $CMS->LP->SetTitle($strPageTitle);
      $dteJoinDate = $CMS->SYS->GetCurrentDateAndTime();
      $CMS->US->Create(FN_REGISTER, $CMS->AddSlashesIFW($strUsername), $strPassword, $CMS->AddSlashesIFW($strForename), $CMS->AddSlashesIFW($strSurname), $strEmail, "", "", "", "", "", 0, $dteJoinDate, $intUserIP, "");
      $strHTML = "<h1>$strPageTitle</h1>\n<p>Registration was successful.</p>\n<ul>\n<li><a href=\"{FN_LOGIN}\">Login</a></li>\n<li><a href=\"{FN_INDEX}\">Back to the home page</a></li>\n</ul>\n";
      $CMS->LP->Display($strHTML);
    }
  }  
  
  $CMS->LP->SetTitle($strPageTitle);
  
  $strRegisterButton = $CMS->AC->RegisterButton();
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form action="{FN_REGISTER}" method="post">
<table class="OptionTable MediumForm" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour WideCell" />
  </colgroup> 
  <tr>
    <td class="InfoColour"><label for="txtRegisterUsername">Username:</label></td>
    <td>
      $strMissingUsername
      $strDuplicateUsername
      $strUsernameTooShort
      $strUsernameInvalidChars
      <input type="text" id="txtRegisterUsername" name="txtRegisterUsername" value="$strUsername" maxlength="45" size="30" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour"><label for="txtRegisterPassword">Password:</label></td>
    <td>
      $strMissingPassword
      $strPWInvalidChars
      <input type="password" id="txtRegisterPassword" name="txtRegisterPassword" value="" maxlength="45" size="30" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour">
      <label for="txtForename">Forename:</label>
      <br /><em>Optional</em>
    </td>
    <td>
      <input type="text" id="txtForename" name="txtForename" value="$strForename" maxlength="45" size="30" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour">
      <label for="txtSurname">Surname:</label>
      <br /><em>Optional</em>
    </td>
    <td>
      <input type="text" id="txtSurname" name="txtSurname" value="$strSurname" maxlength="45" size="30" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour"><label for="txtEmail">Email:</label></td>
    <td>
      $strMissingEmail
      $strEmailInUse
      <input type="text" id="txtEmail" name="txtEmail" value="$strEmail" maxlength="100" size="30" />
    </td>
  </tr>
  <tr>
    <td class="FootColour" colspan="2">$strRegisterButton $strCancelButton</td>
  </tr>
</table>
</form>

END;

  $CMS->LP->Display($strHTML);
