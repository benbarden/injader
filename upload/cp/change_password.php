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
  $intUserID = $CMS->RES->GetCurrentUserID();
  $strPageTitle = "Change Password";

  if ($CMS->SYS->GetSysPref(C_PREF_USER_CHANGE_PASS) != "Y") {
    $CMS->Err_MFail(M_ERR_CHANGEPASS_DISABLED, "");
  }
  
  $strMissingPassword = "";
  $strDifferentPasswords = "";

  if ($_POST) {
    $blnSubmitForm  = true;
    $blnMissingPW   = false;
    $blnDifferentPW = false;
    $strOldPass  = empty($_POST['txtOldPass']) ? "" : $_POST['txtOldPass'];
    $strNewPass1 = $CMS->AddSlashesIFW($_POST['txtNewPass1']);
    $strNewPass2 = $CMS->AddSlashesIFW($_POST['txtNewPass2']);
    if ((!$strNewPass1) || (!$strNewPass2) || (!$strOldPass)) {
      $strMissingPassword = $CMS->AC->InvalidFormData(M_ERR_ENTER_PW_TWICE);
      $blnSubmitForm = false;
    } elseif ($strNewPass1 != $strNewPass2) {
      $strDifferentPasswords = $CMS->AC->InvalidFormData(M_ERR_DIFF_PASSWORDS);
      $blnSubmitForm = false;
    } else {
      // Validate old password.
      // ** Build Query ** //
      $strQuery = sprintf("SELECT count(*) AS count FROM {IFW_TBL_USERS} WHERE id = %s AND userpass = md5('%s')",
        $CMS->RES->GetCurrentUserID(),
        mysql_real_escape_string($strOldPass)
      );
      // ** Process query ** //
      $arrUserData = $CMS->ResultQuery($strQuery, basename(__FILE__), __LINE__);
      $blnOldPWIsOK = false;
      if (is_array($arrUserData)) {
        if ($arrUserData[0]['count'] == "1") {
          $blnOldPWIsOK = true;
        }
      }
      if (!$blnOldPWIsOK) {
        $strDifferentPasswords = $CMS->AC->InvalidFormData(M_ERR_OLD_PW_WRONG);
        $blnSubmitForm = false;
      }
    }
    if ($blnSubmitForm) {
      $strPageTitle .= " - Results";
      $CMS->AP->SetTitle($strPageTitle);
      $CMS->US->EditPassword($intUserID, $strNewPass1);
      $strHTML = "<h1>$strPageTitle</h1>\n<p>Password was updated successfully. <a href=\"{FN_ADM_INDEX}\">Control Panel</a></p>";
      $CMS->AP->Display($strHTML);
    }
  }
  $CMS->AP->SetTitle($strPageTitle);

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strMissingPassword
$strDifferentPasswords
<form action="{FN_ADM_CHANGE_PASSWORD}" method="post">
<div class="table-responsive">
<table class="table table-striped" style="width: 400px;">
  <tr>
    <td><label for="txtOldPass">Old Password:</label></td>
    <td><input type="password" id="txtOldPass" name="txtOldPass" maxlength="45" size="25" /></td>
  </tr>
  <tr>
    <td><label for="txtNewPass1">New Password:</label></td>
    <td><input type="password" id="txtNewPass1" name="txtNewPass1" maxlength="45" size="25" /></td>
  </tr>
  <tr>
    <td><label for="txtNewPass2">Reenter New Password:</label></td>
    <td><input type="password" id="txtNewPass2" name="txtNewPass2" maxlength="45" size="25" /></td>
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