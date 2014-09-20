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
  if ($strAction == "create") {
    $intConnID = "";
    $strPageTitle = "Create Database Connection";
    $strFormAction = "action=create";
    $blnCreate = true;
    $blnEdit = false;
  } elseif ($strAction == "edit") {
    $intConnID = $CMS->FilterNumeric($_GET['id']);
    if (!$intConnID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    $strPageTitle = "Edit Database Connection";
    $strFormAction = "action=edit&amp;id=$intConnID";
    $blnCreate = false;
    $blnEdit = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }
  
  $strName   = "";
  $strHost   = "";
  $strSchema = "";
  $strUser   = "";
  $strPass   = "";
  $strMissingName   = "";
  $strMissingHost   = "";
  $strMissingSchema = "";
  $strMissingUser   = "";

  if ($_POST) {

    // ** GRAB POST DATA ** //

    $arrPostData  = $CMS->ArrayAddSlashes($_POST);
    $strName      = $arrPostData['txtName'];
    $strHost      = $arrPostData['txtHost'];
    $strSchema    = $arrPostData['txtSchema'];
    $strUser      = $arrPostData['txtUser'];
    $strPass      = $arrPostData['txtPass'];

    // ** VALIDATE POST DATA ** //

    $blnSubmitForm = true;
    if (!$strName) {
      $strMissingName = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if (!$strHost) {
      $strMissingHost = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if (!$strSchema) {
      $strMissingSchema = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }
    if (!$strUser) {
      $strMissingUser = $CMS->AC->InvalidFormData("");
      $blnSubmitForm = false;
    }

    if ($blnSubmitForm) {

      // ** WRITE TO DATABASE ** //
      
      if ($blnCreate) {
        $intConnID = $CMS->CON->Create($strName, $strHost, $strSchema, $strUser, $strPass);
        $strMsg = "created";
      } elseif ($blnEdit) {
        $CMS->CON->Edit($intConnID, $strName, $strHost, $strSchema, $strUser, $strPass);
        $strMsg = "edited";
    	}
      $strPageTitle .= " - Results";
      $strHTML = <<<ConfPage
<h1>$strPageTitle</h1>
<p>Connection was successfully $strMsg. <a href="{FN_ADM_CONNECTIONS}">Database Connections</a></p>

ConfPage;
      $CMS->AP->SetTitle($strPageTitle);
      $CMS->AP->Display($strHTML);
    }
  }

  // ** NO POST ** //

  $CMS->AP->SetTitle($strPageTitle);

  if ($_POST) {
    // Fields where slashes need to be stripped
    $arrData = $CMS->ArrayStripSlashes($_POST);
    $strName      = $arrData['txtName'];
    $strHost      = $arrData['txtHost'];
    $strSchema    = $arrData['txtSchema'];
    $strUser      = $arrData['txtUser'];
    $strPass      = $arrData['txtPass'];
  } else {
    if ($blnEdit) {
      $arrData = $CMS->CON->Get($intConnID);
      if (count($arrData) == 0) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Connection: $intConnID");
      }
      $strName   = $arrData['conn_name'];
      $strHost   = $arrData['conn_host'];
      $strSchema = $arrData['conn_schema'];
      $strUser   = $arrData['conn_user'];
      $strPass   = $arrData['conn_pass'];
    }
  }

  // ** SUBMIT / CANCEL BUTTONS ** //
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->LocationButton("Cancel", FN_ADM_CONNECTIONS);
  
  // ** BUILD FORM HTML ** //
  
  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form id="frmMajesticForm" action="{FN_ADM_CONNECTION}?$strFormAction" method="post">
<script type="text/javascript">
  if (!document.all) {
    frmMajesticForm = document.getElementById('frmMajesticForm');
  }
</script>
<table class="DefaultTable WideTable FixedTable" cellspacing="1">
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
      <input type="text" id="txtName" name="txtName" maxlength="100" size="40" value="$strName" />
    </td>
  </tr>
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>Connection Parameters</b></td>
  </tr>
  <tr>
    <td><label for="txtHost">Host:</label></td>
    <td>
      $strMissingHost
      <input type="text" id="txtHost" name="txtHost" maxlength="100" size="40" value="$strHost" />
    </td>
  </tr>
  <tr>
    <td><label for="txtSchema">Database Name:</label></td>
    <td>
      $strMissingSchema
      <input type="text" id="txtSchema" name="txtSchema" maxlength="100" size="40" value="$strSchema" />
    </td>
  </tr>
  <tr>
    <td><label for="txtUser">Username:</label></td>
    <td>
      $strMissingUser
      <input type="text" id="txtUser" name="txtUser" maxlength="100" size="40" value="$strUser" />
    </td>
  </tr>
  <tr>
    <td><label for="txtPass">Password:</label></td>
    <td>
      <input type="password" id="txtPass" name="txtPass" maxlength="100" size="40" value="$strPass" />
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>

END;

  // ** END FORM HTML ** //

  $strHTML .= "</table>\n</form>";
  $CMS->AP->Display($strHTML);
?>