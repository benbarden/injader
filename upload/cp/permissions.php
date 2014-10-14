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
  $strPageTitle = "Permissions";
  $CMS->AP->SetTitle($strPageTitle);

  $arrPermissions = $CMS->PP->GetAll();

  $strHTML = <<<MainContentStart
<p><a href="{FN_ADM_USERS}" title="Manage user accounts">Users</a> | <a href="{FN_ADM_USER_ROLES}" title="Set up roles to use with permission profiles">User Roles</a> | <b>Permissions</b></p>
<h1 class="page-header">$strPageTitle</h1>
<div class="table-responsive">
<table class="table table-striped" style="width: 500px;">
  <tr class="separator-row">
    <td>Type</td>
    <td>Profile Name</td>
    <td>Options</td>
  </tr>

MainContentStart;

	for ($i=0; $i<count($arrPermissions); $i++) {
    $intProfileID   = $arrPermissions[$i]['id'];
    $strProfileName = $arrPermissions[$i]['name'];
    $strIsSystem    = $arrPermissions[$i]['is_system'];
    if ($strIsSystem == "Y") {
      $strType = "System";
      $strDeleteLink = "Delete";
    } elseif ($CMS->PP->IsProfileUsed($intProfileID)) {
      $strType = "Area-Specific";
      $strDeleteLink = "Delete";
    } else {
      $strType = "Area-Specific";
      $strDeleteLink = "<a href=\"{FN_ADMIN_TOOLS}?action=deleteperprofile&amp;id=$intProfileID&amp;back={FN_ADM_PERMISSIONS}\">Delete</a>";
    }
    $strHTML .= <<<TableRow
  <tr>
    <td>$strType</td>
    <td>$strProfileName</td>
    <td><a href="{FN_ADM_PERMISSION}?action=edit&amp;id=$intProfileID">Edit</a> $strDeleteLink</td>
  </tr>

TableRow;
  }

  $strHTML .= <<<MainContentEnd
  <tr>
    <td class="FootColour SpanCell" colspan="99">
      <a href="{FN_ADM_PERMISSION}?action=create">Add New Profile</a>
    </td>
  </tr>
</table>
</div>
<div id="ajaxStatus"></div>

MainContentEnd;

  $CMS->AP->Display($strHTML);
?>