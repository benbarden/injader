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
  $blnCreate = false;
  $blnEdit = false;
  $blnDelete = false;
  $strAction = $_GET['action'];
  if ($strAction == "create") {
    $strPageTitle = "Create User Role";
    $strFormAction = "action=create";
    $blnCreate = true;
  } elseif ($strAction == "edit") {
    $strPageTitle = "Edit User Role";
    $blnEdit = true;
  } elseif ($strAction == "delete") {
    $strPageTitle = "Delete User Role";
    $blnDelete = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }
  
  if (!$blnCreate) {
    $intGroupID = $CMS->FilterNumeric($_GET['id']);
    if (!$intGroupID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    if ($blnEdit) {
      $strFormAction = "action=edit&amp;id=$intGroupID";
    } elseif ($blnDelete) {
      $strFormAction = "action=delete&amp;id=$intGroupID";
    }
  }

  $CMS->AP->SetTitle($strPageTitle);
  
  $strName = "";
  $strMissingName = "";
  
  if ($_POST) {
    $strName  = $_POST['txtName'];
    $blnSubmitForm = true;
    if (!$strName) {
      $strMissingName = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if ($blnSubmitForm) {
      $strName = $CMS->AddSlashesIFW($strName);
      if ($blnCreate) {
        $strMsg = "created";
        $CMS->UG->Create($strName);
      } elseif ($blnEdit) {
        $strMsg = "edited";
        $CMS->UG->Edit($intGroupID, $strName);
      } elseif ($blnDelete) {
        $strMsg = "deleted";
        $CMS->UG->Delete($intGroupID, $strName);
      }
      $strHTML = "<h1>$strPageTitle</h1>\n<p>User Role was successfully $strMsg. <a href=\"{FN_ADM_USER_ROLES}\">User Roles</a></p>";
      $CMS->AP->Display($strHTML);
    }
  } else {
    if (!$blnCreate) {
      $strName = $CMS->UG->GetName($intGroupID);
      if (!$strName) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "ID: $intGroupID");
      }
    }
  }
  
  $strFormTag = "<form id=\"frmMajesticForm\" action=\"{FN_ADM_USER_ROLE}?$strFormAction\" method=\"post\">\n";
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = "<h1>$strPageTitle</h1>\n$strFormTag";

  if ($blnDelete) {
    $strHTML .= <<<Delete
<p>You are about to delete the User Role: $strName</p>
<p>$strSubmitButton $strCancelButton</p>
<input type="hidden" id="txtName" name="txtName" maxlength="100" size="30" value="$strName" />
  
Delete;

  } else {

    $strHTML .= <<<CreateEdit
<table class="DefaultTable MediumTable FixedTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td class="HeadColour SpanCell" colspan="2">
      <b>Role Details</b>
    </td>
  </tr>
  <tr>
    <td><label for="txtName">Name:</label></td>
    <td>
      $strMissingName
      <input type="text" id="txtName" name="txtName" maxlength="100" size="30" value="$strName" /></td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>

CreateEdit;

  }
  
  $strHTML .= "</form>";

  $CMS->AP->Display($strHTML);  
?>