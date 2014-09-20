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

  $intGroupID = $CMS->FilterNumeric($_GET['groupid']);
  if (!$intGroupID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "intGroupID");
  }
  if (!$CMS->UG->GroupExists($intGroupID)) {
    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Role: $intGroupID");
  }
  $strPageTitle = "User Role Usage";

  $CMS->AP->SetTitle($strPageTitle);

  $strHTML = "<h1>$strPageTitle</h1>\n";

  $blnFirstMatch = true;
  $arrUsers = $CMS->US->GetAllWithUserGroup($intGroupID);
  if (is_array($arrUsers)) {
    for ($i=0; $i<count($arrUsers); $i++) {
      if ($blnFirstMatch) {
        $blnFirstMatch = false;
        $strHTML .= <<<GroupHeader
<p>To change a user's role membership, click on the Edit link. Suspended user accounts are not displayed. <a href="{FN_ADM_USER_ROLES}">Back to User Roles</a></p>
<table id="tblSysResults" class="OptionTable MediumTable" cellspacing="1">
  <colgroup>
   <col class="BaseColour TinyCell" />
   <col class="BaseColour" />
   <col class="BaseColour NarrowCell" />
  </colgroup>
  <tr>
    <th>ID</th>
    <th>Username</th>
    <th>Maintain</th>
  </tr>

GroupHeader;
      }
      $intUserID   = $arrUsers[$i]['id'];
      $strUsername = $arrUsers[$i]['username'];
      $strSEOName  = $arrUsers[$i]['seo_username'];
      $strDeleted  = $arrUsers[$i]['user_deleted'];
      if ($strDeleted != "Y") {
        $CMS->PL->SetTitle($strSEOName);
        $strViewUser = $CMS->PL->ViewUser($intUserID);
        $strHTML .= <<<TableRow
  <tr>
    <td class="Centre">$intUserID</td>
    <td class="Left"><a href="$strViewUser">$strUsername</a></td>
    <td class="Centre"><a href="{FN_ADM_USER}?action=edit&amp;id=$intUserID">Edit</a></td>
  </tr>

TableRow;
      }
    }
    $strHTML .= "</table>\n";
  } else {
    $strHTML .= "<p>There are no users with this role.</p>";
  }

  $CMS->AP->Display($strHTML);
?>