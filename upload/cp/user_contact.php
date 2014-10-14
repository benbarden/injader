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
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
  }
  $intUserID = $CMS->FilterNumeric($_GET['id']);
  if (!$intUserID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
  }

  $strPageTitle = "Contact User";
  $strMissingSubject = "";
  $strMissingBody = "";
  $strSubject = "";
  $strBody = "";

  if ($_POST) {
    $blnSubmitForm = true;
    $strUsername = $_POST['txtUsername'];
    $strEmail    = $_POST['txtEmail'];
    $strSubject  = $CMS->StripSlashesIFW($_POST['txtSubject']);
    $strBody     = $CMS->StripSlashesIFW($_POST['txtMessageBody']);
    if (!$strUsername || !$strEmail) {
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
      $strPageTitle .= " - Confirmation";
      $CMS->AP->SetTitle($strPageTitle);
      // All's ok, so send the message
      $strAdminEmail = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      $intReturnA = $CMS->SendEmail($strEmail, $strSubject, $strBody, $strAdminEmail);
      // Confirmation email
      $strSubjectConf = "Confirmation email - $strSubject";
      $strBodyConf = "A copy of your message is displayed below.\r\n\r\n$strBody";
      $intReturnB = $CMS->SendEmail($strAdminEmail, $strSubjectConf, $strBodyConf, $strAdminEmail);
      if (($intReturnA == 1) || ($intReturnB == 1)) {
        $strHTML = "<h1>$strPageTitle</h1>\n<p>The message has been sent. <a href=\"{FN_ADM_USERS}\">View Users</a></p>";
      }
      $CMS->AP->Display($strHTML);
    }
  }
  
  $strPageTitle .= " - New Message";
  $CMS->AP->SetTitle($strPageTitle);
  
  $arrUserDetails = $CMS->US->Get($intUserID);
  if (!is_array($arrUserDetails)) {
    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "User: $intUserID");
  }
  $strUsername = $arrUserDetails['username'];
  $strEmail    = $arrUserDetails['email'];

  $strSubmitButton = $CMS->AC->Submit(M_BTN_SEND_MESSAGE);
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form action="{FN_ADM_USER_CONTACT}?id=$intUserID" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td><strong>Username:</strong></td>
    <td>
      $strUsername
      <input type="hidden" name="txtUsername" value="$strUsername" />
    </td>
  </tr>
  <tr>
    <td><strong>Email:</strong></td>
    <td>
      $strEmail
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
</div>
</form>

END;
  $CMS->AP->Display($strHTML);
?>