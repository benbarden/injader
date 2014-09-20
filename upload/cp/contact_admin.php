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

  require '../sys/header.php';
  $CMS->RES->ValidateLoggedIn();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_NOT_LOGGED_IN, "");
  }
  $strPageTitle = "Contact Admin";
  $CMS->AP->SetTitle($strPageTitle);

  $strMissingEmail = "";
  $strMissingSubject = "";
  $strMissingBody = "";
  $strSubject = "";
  $strBody = "";
  
  if ($_POST) {
    $blnUsernameMissing = false;
    $blnEmailMissing = false;
    $blnSubjectMissing = false;
    $blnBodyMissing = false;
    $blnSubmitForm = true;
    $strUsername = $_POST['txtUsername'];
    $strEmail    = $_POST['txtEmail'];
    $strSubject  = $_POST['txtSubject'];
    $strBody     = $CMS->StripSlashesIFW($_POST['txtMessageBody']);
    if (!$strUsername) {
      $blnSubmitForm = false;
    }
    if (!$strEmail) {
      $blnSubmitForm = false;
    }
    if (!$strSubject) {
      $strMissingSubject = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if (!$strBody) {
      $strMissingBody = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if ($blnSubmitForm) {
      // All's ok, so send the message
      $strAdminBody = "You have received an e-mail from $strUsername.\r\n\r\n$strBody";
      $strAdminEmail = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      @ $intReturnA = $CMS->SendEmail($strAdminEmail, $strSubject, $strAdminBody, $strEmail);
      // Confirmation email
      $strSubjectConf = "Confirmation email - $strSubject";
      $strBodyConf = "A copy of your message is displayed below.\r\n\r\n$strBody";
      @ $intReturnB = $CMS->SendEmail($strEmail, $strSubjectConf, $strBodyConf, $strAdminEmail);
      if (($intReturnA == 1) && ($intReturnB == 1)) {
        $strHTML = "<h1>$strPageTitle</h1>\n<p>The message has been sent. <a href=\"{FN_ADM_INDEX}\">Control Panel</a></p>";
      } else {
        $strHTML = "<h1>$strPageTitle</h1>\n<p>Error: The message could not be delivered. <a href=\"{FN_ADM_INDEX}\">Control Panel</a></p>";
      }
      $CMS->AP->Display($strHTML);
    }
  }
  
  $intUserID = $CMS->RES->GetCurrentUserID();
  $arrUserDetails = $CMS->US->Get($intUserID);
  if (count($arrUserDetails) == 0) {
    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "User: $intUserID");
  }
  $strUsername = $arrUserDetails['username'];
  $strEmail    = $arrUserDetails['email'];
  
  if (!$strEmail) {
    $CMS->Err_MFail(M_ERR_NO_EMAIL_SET, "");
  }

  $strSubmitButton = $CMS->AC->Submit(M_BTN_SEND_MESSAGE);
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form action="{FN_ADM_CONTACT_ADMIN}" method="post">
<p>Please check that your e-mail address is valid. If it is not, you will not receive a response.</p>
<table class="OptionTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <td>Your username:</td>
    <td>
      $strUsername
      <input type="hidden" name="txtUsername" value="$strUsername" />
    </td>
  </tr>
  <tr>
    <td>Your e-mail:</td>
    <td>
      $strMissingEmail
      $strEmail | <i>Wrong email? <a href="{FN_ADM_EDIT_PROFILE}">Edit your profile</a></i>
      <input type="hidden" name="txtEmail" value="$strEmail" />
    </td>
  </tr>
  <tr>
    <td><label for="txtSubject">Subject:</label></td>
    <td>
      $strMissingSubject
      <input type="text" id="txtSubject" name="txtSubject" value="$strSubject" maxlength="100" size="50" />
    </td>
  </tr>
  <tr>
    <td><label for="txtMessageBody">Message:</label></td>
    <td>
      $strMissingBody
      <textarea id="txtMessageBody" name="txtMessageBody" rows="10" cols="50">$strBody</textarea>
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</form>

END;
  $CMS->AP->Display($strHTML);
?>