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
  if ($CMS->SYS->GetSysPref(C_PREF_ALLOW_PASSWORD_RESETS) != "Y") {
    $CMS->Err_MFail(M_ERR_RESETPW_DISABLED, "");
  }
  $CMS->RES->ValidateLoggedIn();
  if (!$CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_ALREADY_LOGGED_IN, "");
  }

  $blnGet = false;
  $blnSubmitForm = false;
  $strUsername = "";
  $strEmail = "";
  $strMissingUsername = "";
  $strMissingEmail = "";
  
  if ($_POST) {
    // Check if OK to send activation key
    $blnSubmitForm = true;
    $strUsername = $CMS->AddSlashesIFW($_POST['txtUsername']);
    $strEmail    = $CMS->AddSlashesIFW($_POST['txtEmail']);
    if (!$strUsername) {
      $blnSubmitForm = false;
      $strMissingUsername = $CMS->AC->InvalidFormData("");
    }
    if (!$strEmail) {
      $blnSubmitForm = false;
      $strMissingEmail = $CMS->AC->InvalidFormData("");
    }
    if ($blnSubmitForm) {
      // Check if user exists
      $intExistsUserID = $CMS->US->GetIDFromNameAndEmail($strUsername, $strEmail);
      $blnExists = !empty($intExistsUserID) ? true : false;
      if ($blnExists) {
        $CMS->LP->SetTitle("Forgot Password - Results");
        // Make activation key
        $strKeyData = $CMS->US->MakeActivationKey($intExistsUserID);
        // Send message
        $strEmailDomain = str_replace("www.", "", SVR_HOST);
        $strEmailFrom = "donotreply@".$strEmailDomain;
        $strAuthorName = $CMS->StripSlashesIFW($strUsername);
        $strEmailBody = "Hi $strAuthorName,\r\n\r\nWe have received a request to reset your password at $strEmailDomain.\r\n\r\nIf you requested this, please click the following link:\r\n\r\nhttp://".SVR_HOST.FN_RESET_PW."?uid=$intExistsUserID&key=$strKeyData\r\n\r\nIf you did not request this, please delete this email and do not click on the link.";
        $intSentEmail = $CMS->SendEmail($strEmail, "Password reset - $strEmailDomain", $strEmailBody, $strEmailFrom);
        // Confirmation page
        if ($intSentEmail == 1) {
          $strHTML = "<div id=\"pagecontent\">\n<h1>Forgot Password - Results</h1>\n<p>Thank you. A message has been sent to your email account with details of how to reset your password.</p>\n</div>\n";
        } else {
          $strHTML = "<div id=\"pagecontent\">\n<h1>Error</h1>\n<p>The message could not be delivered.</p>\n</div>\n";
        }
        $CMS->LP->Display($strHTML);
      } else {
        $CMS->Err_MFail(M_ERR_USERNAME_NOT_FOUND, "");
      }
    }
  }

  $strPageTitle = "Forgot Password";
  
  $strSubmitButton = $CMS->AC->ProceedButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  $strHTML = <<<END
<h1>$strPageTitle</h1>
<p>To reset your password, please enter your username and email address.</p>
<form action="{FN_FORGOT_PW}" method="post">
<table class="OptionTable NarrowTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <td class="InfoColour"><label for="txtUsername">Username:</label></td>
    <td>
      $strMissingUsername
      <input type="text" id="txtUsername" name="txtUsername" maxlength="45" size="40" value="$strUsername" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour"><label for="txtEmail">Email:</label></td>
    <td>
      $strMissingEmail
      <input type="text" id="txtEmail" name="txtEmail" maxlength="100" size="40" value="$strEmail" />
    </td>
  </tr>
  <tr>
    <td class="FootColour" colspan="2">$strSubmitButton $strCancelButton</td>
  </tr>
</table>
</form>

END;
  $CMS->LP->SetTitle("Forgot Password");
  $CMS->LP->Display($strHTML);
?>