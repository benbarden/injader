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
  $CMS->RES->ValidateLoggedIn();
  if (!$CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_ALREADY_LOGGED_IN, "");
  }

  $strPageTitle = "Reset Password";

  $blnGet = false;
  $blnSubmitForm = false;
  $strMissingUsername = "";
  $strMissingEmail = "";
  $intUserID = "";
  $strKey = "";
  
  if ($_GET) {
    // Get activation key
    if (!empty($_GET['uid'])) {
      $intUserID = $CMS->FilterNumeric($_GET['uid']);
    } else {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    if (!empty($_GET['key'])) {
      $strKey = $CMS->FilterAlphanumeric($_GET['key'], "");
    } else {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "Key");
    }
    // Validate
    if ($CMS->US->GetActivationKey($intUserID) != $strKey) {
      $CMS->Err_MFail(M_ERR_INVALID_ACTIVATION_KEY, "");
    }
  }
  
  $strMissingPassword = "";
  $strDifferentPasswords = "";

  if ($_POST) {
    $blnSubmitForm  = true;
    $blnMissingPW   = false;
    $blnDifferentPW = false;
    $strNewPass1 = $_POST['txtNewPass1'];
    $strNewPass2 = $_POST['txtNewPass2'];
    if ((!$strNewPass1) || (!$strNewPass2)) {
      $strMissingPassword = $CMS->AC->InvalidFormData(M_ERR_ENTER_PW_TWICE);
      $blnSubmitForm = false;
    } elseif ($strNewPass1 != $strNewPass2) {
      $strDifferentPasswords = $CMS->AC->InvalidFormData(M_ERR_DIFF_PASSWORDS);
      $blnSubmitForm = false;
    }
    if ($blnSubmitForm) {
      $strPageTitle .= " - Results";
      $CMS->LP->SetTitle($strPageTitle);
      $CMS->US->EditPassword($intUserID, $strNewPass1);
      $CMS->US->ClearActivationKey($intUserID);
      // Confirmation
      $strHTML = "<h1>$strPageTitle</h1>\n<p>Password was updated successfully. <a href=\"{FN_INDEX}\">Site Index</a></p>\n";
      $CMS->LP->Display($strHTML);
    }
  }

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  $strHTML = <<<END
<h1>$strPageTitle</h1>
<p>To proceed, please enter your new password twice.</p>
$strMissingPassword
$strDifferentPasswords
<form action="{FN_RESET_PW}?uid=$intUserID&amp;key=$strKey" method="post">
<table class="OptionTable NarrowTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <td class="InfoColour"><label for="txtNewPass1">New Password:</label></td>
    <td><input type="password" id="txtNewPass1" name="txtNewPass1" maxlength="45" size="25" /></td>
  </tr>
  <tr>
    <td class="InfoColour"><label for="txtNewPass2">Reenter New Password:</label></td>
    <td><input type="password" id="txtNewPass2" name="txtNewPass2" maxlength="45" size="25" /></td>
  </tr>
  <tr>
    <td class="FootColour" colspan="2">$strSubmitButton $strCancelButton</td>
  </tr>
</table>
</form>

END;

  $CMS->LP->SetTitle($strPageTitle);
  $CMS->LP->Display($strHTML);
?>