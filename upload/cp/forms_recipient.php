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
  $blnCreate = false; $blnEdit = false;
  $strAction = $_GET['action'];
  if ($strAction == "create") {
    $strPageTitle = "Create Form Recipient";
    $blnCreate = true;
  } elseif ($strAction == "edit") {
    $strPageTitle = "Edit Form Recipient";
    $blnEdit = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }

  if ($blnEdit) {
    $intRecipientID = $CMS->FilterNumeric($_GET['id']);
    if (!$intRecipientID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
  }
  
  $CMS->AP->SetTitle($strPageTitle);
  
  $strName = "";
  $strEmail = "";
  $intOrder = "";
  $strMissingName = "";
  $strMissingEmail = "";
  $strInvalidEmail = "";
  
  if ($_POST) {
    $strName  = $_POST['txtName'];
    $strEmail = $_POST['txtEmail'];
    $intOrder = $CMS->FilterNumeric($_POST['txtOrder']);
    if (!$intOrder) {
      $intOrder = '0';
    }
    $blnSubmitForm = true;
    if (!$strName) {
      $strMissingName = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if (!$strEmail) {
      $strMissingEmail = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    } elseif (!$CMS->IsValidEmail($strEmail)) {
      $strInvalidEmail = $CMS->AC->InvalidFormData(M_ERR_INVALID_EMAIL);
      $blnSubmitForm = false;
    }
    if ($blnSubmitForm) {
      if ($blnCreate) {
        $strMsg = "created";
        // ** Build Query: Create form recipient ** //
        $strQuery = sprintf("INSERT INTO {IFW_TBL_FORM_RECIPIENTS}(name, email, recipient_order) VALUES('%s', '%s', %s)",
          mysql_real_escape_string($strName),
          mysql_real_escape_string($strEmail),
          $intOrder
        );
        // ** Process query ** //
        $intRecipientID = $CMS->Query($strQuery, basename(__FILE__), __LINE__);
        // ** Access log ** //
        $CMS->AL->Build(AL_TAG_FORM_RECIPIENT_CREATE, $intRecipientID, $strName);
      } elseif ($blnEdit) {
        $strMsg = "edited";
        // ** Build Query: Edit form recipient ** //
        $strQuery = sprintf("UPDATE {IFW_TBL_FORM_RECIPIENTS} SET name = '%s', email = '%s', recipient_order = %s WHERE id = %s",
          mysql_real_escape_string($strName),
          mysql_real_escape_string($strEmail),
          $intOrder,
          $intRecipientID
        );
        // ** Process query ** //
        $intRecipientID = $CMS->Query($strQuery, basename(__FILE__), __LINE__);
        // ** Access log ** //
        $CMS->AL->Build(AL_TAG_FORM_RECIPIENT_EDIT, $intRecipientID, $strName);
      }
      $strHTML = "<h1>$strPageTitle</h1>\n<p>Form recipient was successfully $strMsg. <a href=\"{FN_ADM_FORMS_RECIPIENTS}\">Form Recipients</a></p>";
      $CMS->AP->Display($strHTML);
    }
  }
  
  // ** End of POST data ** //

  if ($_POST) {
    $strName  = $CMS->StripSlashesIFW($strName);
    $strEmail = $CMS->StripSlashesIFW($strEmail);
  } else {
    if ($blnEdit) {
      $arrRecipient = $CMS->FR->Get($intRecipientID);
      if (!is_array($arrRecipient)) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "ID: $intRecipientID");
      }
      $strName  = $arrRecipient['name'];
      $strEmail = $arrRecipient['email'];
      $intOrder = $arrRecipient['recipient_order'];
    }
  }

  if ($blnCreate) {
    $strFormTag = "<form id=\"frmMajesticForm\" action=\"{FN_ADM_FORMS_RECIPIENT}?action=create\" method=\"post\">\n";
  } elseif ($blnEdit) {
    $strFormTag = "<form id=\"frmMajesticForm\" action=\"{FN_ADM_FORMS_RECIPIENT}?action=edit&amp;id=$intRecipientID\" method=\"post\">\n";
  }
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
$strFormTag
<table class="DefaultTable MediumTable FixedTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td class="HeadColour SpanCell" colspan="2">
      <b>Recipient Details</b>
    </td>
  </tr>
  <tr>
    <td><label for="txtName">Name:</label></td>
    <td>
      $strMissingName
      <input type="text" id="txtName" name="txtName" maxlength="100" size="30" value="$strName" />
    </td>
  </tr>
  <tr>
    <td><label for="txtEmail">Email:</label></td>
    <td>
      $strMissingEmail
      $strInvalidEmail
      <input type="text" id="txtEmail" name="txtEmail" maxlength="150" size="30" value="$strEmail" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtName">Order:</label><br />
      Using this field, recipients will be sorted in numerical order.
    </td>
    <td>
      <input type="text" id="txtOrder" name="txtOrder" maxlength="3" size="4" value="$intOrder" />
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