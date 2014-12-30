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

  require '../sys/header.php';
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
  }
  $intUserID = $CMS->FilterNumeric($_GET['id']);
  if (!$intUserID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
  }
  $arrUser = $CMS->ResultQuery("SELECT username FROM {IFW_TBL_USERS} WHERE id = $intUserID", basename(__FILE__), __LINE__);
  if (count($arrUser) == 0) {
    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "User: $intUserID");
  }
  $strUserName = $arrUser[0]['username'];

  $strPageTitle = "Edit Password";
  $strMissingPassword = "";

  if ($_POST) {
    $blnSubmitForm = true;
    $strPassword = $_POST['txtEditPass'];
    if (!$strPassword) {
      $strMissingPassword = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if ($blnSubmitForm) {
      $strPageTitle .= " - Results";
      $CMS->AP->SetTitle($strPageTitle);
      $CMS->US->EditPassword($intUserID, $strPassword);
      $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Password was updated successfully. <a href=\"{FN_ADM_USERS}\">View Users</a></p>";
      $CMS->AP->Display($strHTML);
    }
  }
  $CMS->AP->SetTitle($strPageTitle);

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form action="{FN_ADM_USER_EDIT_PASSWORD}?id=$intUserID" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td><strong>Username:</strong></td>
    <td>$strUserName</td>
  </tr>
  <tr>
    <td><label for="txtEditPass">New password:</label></td>
    <td>
      $strMissingPassword
      <input type="password" id="txtEditPass" name="txtEditPass" maxlength="45" size="25" />
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
