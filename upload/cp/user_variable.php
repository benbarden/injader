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
  $intParentID = "";
  $strAction = $_GET['action'];
  $blnCreate = false; $blnEdit = false; $blnDelete = false;
  $blnCheckID = false;
  if ($strAction == "create") {
    $intVariableID = "";
    $strPageTitle = "Create User Variable";
    $strFormAction = "action=create";
    $blnCreate = true;
  } elseif ($strAction == "edit") {
    $blnCheckID = true;
    $strPageTitle = "Edit User Variable";
    $blnEdit = true;
  } elseif ($strAction == "delete") {
    $blnCheckID = true;
    $strPageTitle = "Delete User Variable";
    $blnDelete = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }
  
  if ($blnCheckID) {
    $intVariableID = $CMS->FilterNumeric($_GET['id']);
    if (!$intVariableID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    if ($blnEdit) {
      $strFormAction = "action=edit&amp;id=$intVariableID";
    } elseif ($blnDelete) {
      $strFormAction = "action=delete&amp;id=$intVariableID";
    }
  }
  
  $strName = "";
  $strMissingName = "";
  $strContent = "";
  $strMissingContent = "";
  $strVariable = "";
  $strMissingVariable = "";

  if ($_POST) {
  
    if (!$blnDelete) {
      $strName     = $_POST['txtName'];
      $strVariable = $_POST['txtVariable'];
      $strContent  = $_POST['txtContent'];
      $blnSubmitForm = true;
      if (!$strName) {
        $strMissingName = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      }
      if (!$strVariable) {
        $strMissingVariable = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      } else {
        if ($CMS->UV->VarExists($strVariable, $intVariableID)) {
          $strMissingVariable = $CMS->AC->InvalidFormData("A User Variable already exists with the same variable name. Variable names must be unique.");
          $blnSubmitForm = false;
        } elseif (substr($strVariable, 0, 4) != '$usr') {
          $strMissingVariable = $CMS->AC->InvalidFormData("User Variables must start with \$usr to ensure they do not clash with internal system variables.");
          $blnSubmitForm = false;
        }
      }
      if (!$strContent) {
        $strMissingContent = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      }
    } elseif ($blnDelete) {
      $blnSubmitForm = true;
    }
    if ($blnSubmitForm) {
      $strName    = $CMS->AddSlashesIFW($strName);
      $strContent = $CMS->AddSlashesIFW($strContent);
      // ** WRITE TO DATABASE ** //
      if ($blnCreate) {
        $intVariableID = $CMS->UV->Create($strName, $strContent, $strVariable);
        $strMsg = "created";
      } elseif ($blnEdit) {
        $CMS->UV->Edit($intVariableID, $strName, $strContent, $strVariable);
        $strMsg = "edited";
      } elseif ($blnDelete) {
        $CMS->UV->Delete($intVariableID);
        $strMsg = "deleted";
    	}
      $strPageTitle .= " - Results";
      $strHTML = <<<ConfPage
<h1>$strPageTitle</h1>
<p>Variable was successfully $strMsg. <a href="{FN_ADM_USER_VARIABLES}">User Variables</a></p>

ConfPage;
      $CMS->AP->SetTitle($strPageTitle);
      $CMS->AP->Display($strHTML);
    }
  }

  // ** NO POST ** //

  $CMS->AP->SetTitle($strPageTitle);

  if ($_POST) {
    $strName     = $CMS->StripSlashesIFW($_POST['txtName']);
    $strContent  = $CMS->PrepareTemplateForEditing($_POST['txtContent']);
    $strVariable = $_POST['txtVariable'];
  } else {
    if ($blnCheckID) {
      $arrVariable = $CMS->UV->Get($intVariableID);
      if (count($arrVariable) == 0) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Plugin: $intVariableID");
      }
      $strName = $CMS->StripSlashesIFW($arrVariable['name']);
    }
    if ($blnCreate) {
      $strVariable = '$usr';
    } elseif ($blnEdit) {
      $strContent  = $CMS->PrepareTemplateForEditing($arrVariable['content']);
      $strVariable = $arrVariable['user_variable'];
    }
  }
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->LocationButton("Cancel", FN_ADM_USER_VARIABLES);

  // ** BUILD FORM HTML ** // -- General settings
  
  $strHTML = <<<Header
<h1>$strPageTitle</h1>
<form id="frmMajesticForm" action="{FN_ADM_USER_VARIABLE}?$strFormAction" method="post">

Header;

  if (!$blnDelete) {
    $strHTML .= <<<EditForm

<table class="DefaultTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>General</b></td>
  </tr>
  <tr>
    <td><label for="txtName">Name:</label></td>
    <td>
      $strMissingName
      <input type="text" id="txtName" name="txtName" maxlength="100" size="50" value="$strName" />
    </td>
  </tr>
  <tr>
    <td><label for="txtVariable">Variable:</label></td>
    <td>
      $strMissingVariable
      <input type="text" id="txtVariable" name="txtVariable" maxlength="100" size="40" value="$strVariable" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtContent">Content:</label>
    </td>
    <td>
      $strMissingContent
      <textarea id="txtContent" name="txtContent" cols="60" rows="20">$strContent</textarea>
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>

EditForm;

  } elseif ($blnDelete) {
    $strHTML .= <<<DeleteForm
<p>You are about to delete the following variable: $strName (ID: $intVariableID)</p>
<p><input type="hidden" name="dummy" value="dummy" /></p>
<p>$strSubmitButton $strCancelButton</p>

DeleteForm;
  }

  // ** END FORM HTML ** //

  $strHTML .= "</form>";
  $CMS->AP->Display($strHTML);
?>