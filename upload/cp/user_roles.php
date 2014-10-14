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
  $strPageTitle = "User Roles";

  $CMS->AP->SetTitle($strPageTitle);

  $arrGroups = $CMS->UG->GetAll();
	$intNumGroups = count($arrGroups);

  $strHTML = <<<MainContentStart
<p><a href="{FN_ADM_USERS}" title="Manage user accounts">Users</a> | <b>User Roles</b> | <a href="{FN_ADM_PERMISSIONS}" title="Assign privileges to user roles">Permissions</a></p>
<h1 class="page-header">$strPageTitle</h1>
<div class="table-responsive">
<table class="table table-striped">
  <thead>
    <tr class="separator-row">
      <td>Name</td>
      <td>New user default</td>
      <td>Admin</td>
      <td>Membership</td>
      <td>Messaging</td>
      <td>Options</td>
    </tr>
  </thead>
  <tbody id="tblUserGroupsBody">

MainContentStart;

  $strNewGroupButton = $CMS->AC->LocationButton(M_BTN_ADD_GROUP, "{FN_ADM_USER_ROLE}?action=create");

	for ($i=0; $i<$intNumGroups; $i++) {
    $intGroupID   = $arrGroups[$i]['id'];
    $strGroupName = $arrGroups[$i]['name'];
    $strIsDefault = $arrGroups[$i]['is_default'];
    $strIsAdmin   = $arrGroups[$i]['is_admin'];
    $arrUsers = $CMS->US->GetAllWithUserGroup($intGroupID);
    if (is_array($arrUsers)) {
      $strDelete = "Delete";
      $strGroupUsers = "<a href=\"{FN_ADM_USER_ROLE_USAGE}?groupid=$intGroupID\">View Users</a>";
      $strGroupMsg = "<a href=\"{FN_ADM_USER_ROLE_MESSAGE}?id=$intGroupID\">Send Message</a>";
    } else {
      $strDelete = "<a href=\"{FN_ADM_USER_ROLE}?id=$intGroupID&amp;action=delete\">Delete</a>";
      $strGroupUsers = "(no users)";
      $strGroupMsg = "(no users)";
    }
    if (($i % 2) == 0) {
      $strRowClass = "even";
    } else {
      $strRowClass = "odd";
    }
    $strHTML .= <<<TableRow
    <tr class="$strRowClass">
      <td class="Left">$strGroupName</td>
      <td>$strIsDefault</td>
      <td>$strIsAdmin</td>
      <td>$strGroupUsers</td>
      <td>$strGroupMsg</td>
      <td><a href="{FN_ADM_USER_ROLE}?id=$intGroupID&amp;action=edit">Edit</a> : $strDelete</td>
    </tr>

TableRow;
	}
  
  $strHTML .= <<<MainContentEnd
  <tr>
    <td class="FootColour SpanCell" colspan="99">$strNewGroupButton</td>
  </tr>
  </tbody>
</table>
</div>

MainContentEnd;

  $CMS->AP->Display($strHTML);
