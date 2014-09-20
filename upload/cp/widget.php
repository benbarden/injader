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
  $intPluginID = "";
  $strAction = $_GET['action'];
  $blnCreate = false; $blnEdit = false; $blnDelete = false;
  $blnCheckID = false;
  switch ($strAction) {
    case "create": $blnCreate = true; $strPageTitle = "Create Data Widget"; $strFormAction = "action=create"; break;
    case "edit":   $blnEdit = true;   $strPageTitle = "Edit Data Widget";   $blnCheckID = true; break;
    case "delete": $blnDelete = true; $strPageTitle = "Delete Data Widget"; $blnCheckID = true; break;
    default: $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction"); break;
  }
  
  if ($blnCheckID) {
    $intPluginID = $CMS->FilterNumeric($_GET['id']);
    if (!$intPluginID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    if ($blnEdit) {
      $strFormAction = "action=edit&amp;id=$intPluginID";
    } elseif ($blnDelete) {
      $strFormAction = "action=delete&amp;id=$intPluginID";
    }
  }
  
  $strName = "";
  $strMissingName = "";
  $strVersion = "";
  $strMissingVersion = "";
  $strVariable = "";
  $strMissingVariable = "";
  $intConnID = "";
  $strSQL = "";
  $strMissingSQL = "";
  $intItemLimit = 0;
  $strMissingItemLimit = "";
  $strTemplate = "";

  if ($_POST) {
  
    if (!$blnDelete) {

      // ** GRAB POST DATA ** //

      $strName      = $_POST['txtName'];
      $strVersion   = $_POST['txtVersion'];
      $strVariable  = $_POST['txtVariable'];
      $intConnID    = $_POST['optConnection'];
      $strUCPLink   = "N";
      $strACPLink   = "N";
      $strSQL       = $_POST['txtSQL'];
      $intItemLimit = $_POST['txtItemLimit'];
      $strTemplate  = $_POST['txtTemplate'];

      // ** VALIDATE POST DATA ** //

      $blnSubmitForm = true;
      if (!$strName) {
        $strMissingName = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      }
      if (!$strVersion) {
        $strMissingVersion = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      }
      if (!$strVariable) {
        $strMissingVariable = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      } else {
        if ($CMS->WGT->VarExists($strVariable, $intPluginID)) {
          $strMissingVariable = $CMS->AC->InvalidFormData("
            A Data Widget already exists with the same variable name. 
            Variable names must be unique.");
          $blnSubmitForm = false;
        } elseif (substr($strVariable, 0, 4) != '$plg') {
          $strMissingVariable = $CMS->AC->InvalidFormData("
            Data Widgets must start with \$plg to ensure they do not clash with internal 
            system variables.
          ");
          $blnSubmitForm = false;
        }
      }
      $blnInvalidQuery = false;
      if (!$strSQL) {
        $strMissingSQL = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      } else {
        if (strpos(strtoupper($strSQL), "INSERT ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "UPDATE ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "DELETE ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "INDEX ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "CREATE ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "ALTER ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "DROP ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "LOCK ") !== false) {
          $blnInvalidQuery = true;
        } elseif (strpos(strtoupper($strSQL), "REFERENCES ") !== false) {
          $blnInvalidQuery = true;
        }
      }
      if ($blnInvalidQuery) {
        $strMissingSQL = $CMS->AC->InvalidFormData("You can only use a SELECT query.");
        $blnSubmitForm = false;
      }
      if (!$intItemLimit) {
        $intItemLimit = 0;
      }

    } elseif ($blnDelete) {
      $blnSubmitForm = true;
    }

    if ($blnSubmitForm) {
      // Prepare
      $strName     = $CMS->AddSlashesIFW($strName);
      $strSQL      = $CMS->AddSlashesIFW($strSQL);
      $strTemplate = $CMS->PrepareTemplateForSaving($strTemplate);
      // ** WRITE TO DATABASE ** //
      if ($blnCreate) {
        $intPluginID = $CMS->WGT->Create($strName, $strVersion, $intConnID, $strUCPLink, 
          $strACPLink, $strSQL, $intItemLimit, $strVariable, $strTemplate, C_WIDGET_DATA);
        $strMsg = "created";
      } elseif ($blnEdit) {
        $CMS->WGT->Edit($intPluginID, $strName, $strVersion, $intConnID, $strUCPLink, 
          $strACPLink, $strSQL, $intItemLimit, $strVariable, $strTemplate);
        $strMsg = "edited";
      } elseif ($blnDelete) {
        $CMS->WGT->Delete($intPluginID);
        $strMsg = "deleted";
        }
      $strPageTitle .= " - Results";
      $strHTML = <<<ConfPage
<h1>$strPageTitle</h1>
<p>Data Widget was successfully $strMsg. 
<a href="{FN_ADM_WIDGETS}">Widgets</a></p>

ConfPage;
      $CMS->AP->SetTitle($strPageTitle);
      $CMS->AP->Display($strHTML);
    }
  }

  // ** NO POST ** //

  $CMS->AP->SetTitle($strPageTitle);

  if ($_POST) {
    $strName     = $CMS->StripSlashesIFW($_POST['txtName']);
    $strVersion  = $_POST['txtVersion'];
    $strVariable = $_POST['txtVariable'];
    $strSQL      = $CMS->StripSlashesIFW($_POST['txtSQL']);
    $strTemplate = $CMS->PrepareTemplateForEditing($_POST['txtTemplate']);
  } else {
    if ($blnCheckID) {
      $arrPlugin = $CMS->WGT->Get($intPluginID);
      if (count($arrPlugin) == 0) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Plugin: $intPluginID");
      }
      $strName = $CMS->StripSlashesIFW($arrPlugin['name']);
    }
    if ($blnCreate) {
      $strVariable  = '$plg';
    } elseif ($blnEdit) {
      $strVersion   = $arrPlugin['version'];
      $intConnID    = $arrPlugin['conn_id'];
      $strUCPLink   = $arrPlugin['ucp_link']; // Y/N
      $strACPLink   = $arrPlugin['acp_link']; // Y/N
      $strSQL       = $CMS->PrepareTemplateForEditing($arrPlugin['query_string']);
      $intItemLimit = $arrPlugin['item_limit'];
      $strVariable  = $arrPlugin['widget_variable'];
      $strTemplate  = $CMS->PrepareTemplateForEditing($arrPlugin['widget_template']);
    }
  }
  
  $strConnectionList = $CMS->DD->DatabaseConnections($intConnID);

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->LocationButton("Cancel", FN_ADM_WIDGETS);

  // ** BUILD FORM HTML ** // -- General settings
  
  $strHTML = <<<Header
<h1>$strPageTitle</h1>
<form id="frmMajesticForm" action="{FN_ADM_WIDGET}?$strFormAction" method="post">
<script type="text/javascript">
  if (!document.all) {
    frmMajesticForm = document.getElementById('frmMajesticForm');
  }
</script>

Header;

  if (!$blnDelete) {
    $strHTML .= <<<EditForm
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
      <input type="text" id="txtName" name="txtName" maxlength="100" size="50" value="$strName" />
    </td>
  </tr>
  <tr>
    <td><label for="txtVersion">Version:</label></td>
    <td>
      $strMissingVersion
      <input type="text" id="txtVersion" name="txtVersion" maxlength="20" size="20" value="$strVersion" />
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
      <label for="txtItemLimit">Item Limit:</label>
      <br />Set this to 0 (zero) to display all items.
    </td>
    <td>
      $strMissingItemLimit
      <input type="text" id="txtItemLimit" name="txtItemLimit" maxlength="3" size="3" value="$intItemLimit" />
    </td>
  </tr>
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>Database</b></td>
  </tr>
  <tr>
    <td>
      <label for="optConnection">Connection:</label><br />
      This allows you to choose a different Database Connection if your data is stored in a different database.
    </td>
    <td>
      <select id="optConnection" name="optConnection">
$strConnectionList
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtSQL">Query String (SQL):</label><br />
      Do not edit this unless you know what you are doing!
    </td>
    <td>
      $strMissingSQL
      <textarea id="txtSQL" name="txtSQL" cols="50" rows="6">$strSQL</textarea>
    </td>
  </tr>
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>Template</b></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td>
      In the list item tag, you can include any database field like this: <i>{SQL_fieldname}</i>
      <br />For instance, to display a <i>title</i> field, do this: <i>{SQL_title}</i>
      <br />The field must be available in your query, otherwise the field will not be displayed. We recommend using <i>SELECT * FROM tablename</i> to get all fields in a table.
      <br />
      <textarea id="txtTemplate" name="txtTemplate" cols="50" rows="6">$strTemplate</textarea>
      <br /><a href="javascript:EnlargeTextarea('txtTemplate');">Enlarge wrapper field</a> : <a href="javascript:ShrinkTextarea('txtTemplate');">Shrink wrapper field</a>
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
<p>You are about to delete the following Data Widget: $strName (ID: $intPluginID)</p>
<p><input type="hidden" name="dummy" value="dummy" /></p>
<p>$strSubmitButton $strCancelButton</p>

DeleteForm;
  }

  // ** END FORM HTML ** //

  $strHTML .= "</form>";
  $CMS->AP->Display($strHTML);
?>