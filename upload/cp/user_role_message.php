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
  
  $strPageTitle = "Role Messaging";

  $CMS->AP->SetTitle($strPageTitle);

  $strAdminEmail = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
  
  $strSubject = "";
  $strBody = "";
  $strMissingSubject = "";
  $strMissingBody = "";
  $intSelectedGroupID = !empty($_GET['id']) ? $CMS->FilterNumeric($_GET['id']) : "";

  if ($_POST) {
    // Store POST data
    $blnSubmitForm = true;
    $strSubject = $_POST['txtSubject'];
    $strBody    = $_POST['txtMessageBody'];
    $intGroupID = $_POST['optUserGroup'];
    if (!$strSubject) {
      $strMissingSubject = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if (!$strBody) {
      $strMissingBody = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if ($blnSubmitForm) {
      // Find users in group
      $arrUsers = $CMS->ResultQuery("SELECT id, email, user_groups FROM users ORDER BY id ASC", basename(__FILE__), __LINE__);
      for ($i=0; $i<count($arrUsers); $i++) {
        $blnMatch = $CMS->UG->GroupMatch($arrUsers[$i]['user_groups'], $intGroupID);
        // Only get users in this group
        if ($blnMatch) {
          $intCustID  = $arrUsers[$i]['id'];
          $strEmailTo = $arrUsers[$i]['email'];
          // Check they have an email address defined
          if ($strEmailTo) {
            // Send a message
            $intReturnA = $CMS->SendEmail($strEmailTo, $strSubject, $strBody, $strAdminEmail);
          }
        }
      }
      // Confirmation email
      $strSubjectConf = "Confirmation email - $strSubject";
      $strBodyConf = "A copy of your message is displayed below.\r\n\r\n$strBody";
      $intReturnB = $CMS->SendEmail($strAdminEmail, $strSubjectConf, $strBodyConf, $strAdminEmail);
      if (($intReturnA == 1) || ($intReturnB == 1)) {
        $strHTML  = "<h1>$strPageTitle</h1>\n<p>The message has been sent. <a href=\"{FN_ADM_INDEX}\">Control Panel Index</a></p>";
      } else {
        $strHTML  = "<h1>$strPageTitle</h1>\n<p>Error: The message could not be delivered. <a href=\"{FN_ADM_INDEX}\">Control Panel Index</a></p>";
      }
      $CMS->AP->Display($strHTML);
    }
    $strSubject = $CMS->StripSlashesIFW($strSubject);
    $strBody    = $CMS->StripSlashesIFW($strBody);
    $intSelectedGroupID = $intGroupID;
  }

  $strUserGroupHTML = $CMS->DD->UserGroup($intSelectedGroupID);

  $strSubmitButton = $CMS->AC->Submit(M_BTN_SEND_MESSAGE);
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form id="frmMajesticForm" action="{FN_ADM_USER_ROLE_MESSAGE}" method="post">
<p>Use this screen to contact all users with a certain role.<br />The messages will be sent from: $strAdminEmail</p>
<table class="OptionTable" cellspacing="1" style="width: 550px;">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <td><label for="optUserGroup">Send to role:</label></td>
    <td>
      <select name="optUserGroup" id="optUserGroup">
      $strUserGroupHTML
      </select>
    </td>
  </tr>
  <tr>
    <td><label for="txtSubject">Subject:</label></td>
    <td>
      $strMissingSubject
      <input type="text" id="txtSubject" name="txtSubject" maxlength="100" size="50" value="$strSubject" />
    </td>
  </tr>
  <tr>
    <td><label for="txtMessageBody">Message:</label></td>
    <td>
      $strMissingBody
      <textarea id="txtMessageBody" name="txtMessageBody" rows="10" cols="40">$strBody</textarea>
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