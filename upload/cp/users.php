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
  $strPageTitle = "View Users";

  $CMS->AP->SetTitle($strPageTitle);
  
  // Multi-paging
  if (!empty($_GET['page'])) {
    $intPageNumber = $_GET['page'];
    if ($intPageNumber < 1) {
      $intPageNumber = 1;
    }
  } else {
    $intPageNumber = 1;
  }
  
  $blnSubmitForm = false;
  $blnShowAll    = false;
  $strGetURL     = "";
  $strUsername   = "";
  $strIPAddress  = "";
  $strOrder      = "";
  $strDir        = "";
  $strMissingSearchParams = "";

  if ($_GET) {
    $strUsername  = empty($_GET['un']) ? "" : $CMS->DoEntities($_GET['un']);
    $strIPAddress = empty($_GET['ip']) ? "" : $CMS->DoEntities($_GET['ip']);
    $strOrder     = empty($_GET['order']) ? "" : $CMS->DoEntities($_GET['order']);
    $strDir       = empty($_GET['dir']) ? "" : $CMS->DoEntities($_GET['dir']);
    $blnSubmitForm = true;
    if (!empty($_GET['action']) && ($_GET['action'] == "showall")) {
      $blnSubmitForm = true;
      $blnShowAll = true;
    }
    if ($blnShowAll) {
      $strGetURL = "?action=showall";
    } else {
      $strGetURL = "?un=$strUsername&amp;ip=$strIPAddress";
      if ((!$strUsername) && (!$strIPAddress)) {
        $blnSubmitForm = false;
        $strMissingSearchParams = $CMS->AC->InvalidFormData(M_ERR_MISSING_SEARCH_PARAMS);
      }
    }
    $strGetURL .= "&amp;order=$strOrder&amp;dir=$strDir";
  }
  
  $strDropDownOBF = $CMS->DD->UserOrderField($strOrder);
  $strDropDownOBD = $CMS->DD->SortRuleOrder($strDir);

  $strSearchButton = $CMS->AC->SearchButton();

  $strHTML = <<<END
<p><b>Users</b> | <a href="{FN_ADM_USER_ROLES}" title="Set up roles to use with permission profiles">User Roles</a> | <a href="{FN_ADM_PERMISSIONS}" title="Assign privileges to user roles">Permissions</a></p>
<h1 class="page-header">$strPageTitle</h1>
<button onclick="top.location.href = '{FN_ADM_USER}?action=create';">Add a new user</button>
<br><br>
<form action="{FN_ADM_USERS}" method="get">
$strMissingSearchParams
<div class="table-responsive">
<table class="table table-striped">
  <tr class="separator-row">
    <td colspan="7">Search for users</td>
  </tr>
  <tr>
    <td>
      <label for="un">Username</label>
    </td>
    <td>
      <input id="un" name="un" type="text" size="20" maxlength="100" value="$strUsername" />
    </td>
    <td>
      <label for="ip">IP Address</label>
    </td>
    <td>
      <input id="ip" name="ip" type="text" size="12" maxlength="20" value="$strIPAddress" />
    </td>
    <td>
      <label for="order">Order by:</label>
    </td>
    <td>
      <select id="order" name="order">
      $strDropDownOBF
      </select>
      <select id="dir" name="dir">
      $strDropDownOBD
      </select>
    </td>
    <td>
      $strSearchButton
    </td>
  </tr>
</table>
</div>
</form>
<br />
<form action="{FN_ADM_USERS}" method="get">
<div class="table-responsive">
<table class="table table-striped">
  <tr class="separator-row">
    <td colspan="3">Show all users</td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="action" value="showall" />
      <label for="order2">Order by:</label>
    </td>
    <td>
      <select id="order2" name="order">
      $strDropDownOBF
      </select>
      <select id="dir2" name="dir">
      $strDropDownOBD
      </select>
    </td>
    <td>
      $strSearchButton
    </td>
  </tr>
</table>
</div>
</form>

END;

  if ($blnSubmitForm) {
    if ($blnShowAll) {
      $strWhereClause = "";
    } else {
      if ($strUsername) {
        $strUsername  = $CMS->AddSlashesIFW($strUsername);
        $strWhereClause = " WHERE UPPER(username) LIKE UPPER('%$strUsername%') ";
      } else {
        $strWhereClause = "";
      }
      if ($strIPAddress) {
        if ($strWhereClause) {
          $strWhereClause .= " AND ip_address LIKE '%$strIPAddress%' ";
        } else {
          $strWhereClause = " WHERE ip_address LIKE '%$strIPAddress%' ";
        }
      }
    }
    if (($strOrder) && ($strDir)) {
      switch ($strOrder) {
        case "id": $strOrderBy = " ORDER BY id "; break;
        case "username": $strOrderBy = " ORDER BY username "; break;
        default: $strOrderBy = " ORDER BY id "; break;
      }
      switch ($strDir) {
        case "asc": $strOrderBy .= "ASC"; break;
        case "desc": $strOrderBy .= "DESC"; break;
        default: $strOrderBy .= "ASC"; break;
      }
    } else {
      $strOrderBy = "";
    }
    // Page numbers
    $intContentPerPage = $CMS->SYS->GetSysPref(C_PREF_SYSTEM_PAGE_COUNT);
    $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
    // Query
    if ($blnShowAll) {
      $strSQL = "SELECT * FROM {IFW_TBL_USERS}";
    } else {
      $strSQL = "SELECT * FROM {IFW_TBL_USERS}".$strWhereClause;
    }
    $arrUsers = $CMS->ResultQuery($strSQL.$strOrderBy." LIMIT $intStart, $intContentPerPage", basename(__FILE__), __LINE__);
    $arrUserCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USERS} $strWhereClause", basename(__FILE__), __LINE__);
    $intUserCount = $arrUserCount[0]['count'];
    // Page number links
    $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intUserCount);
    $strPageNumbers = $CMS->PNN->Make($intNumPages, $intPageNumber, basename(__FILE__)."$strGetURL");
    for ($i=0; $i<count($arrUsers); $i++) {
      if ($i==0) {
        $strHTML .= <<<TableHeader
$strPageNumbers
<div class="table-responsive">
<table class="table table-striped">
  <thead>
    <tr class="separator-row">
      <td>ID</td>
      <td>Username</td>
      <td>IP Address</td>
      <td>Maintain</td>
      <td>Email</td>
    </tr>
  </thead>
  <tbody id="tblUsersBody">

TableHeader;
      }
      $intUserID   = $arrUsers[$i]['id'];
      $strUsername = $arrUsers[$i]['username'];
      $strSEOName  = $arrUsers[$i]['seo_username'];
      $CMS->PL->SetTitle($strSEOName);
      $strViewUser = $CMS->PL->ViewUser($intUserID);
      $intUserIP   = $arrUsers[$i]['ip_address'];
      $strDeleted  = $arrUsers[$i]['user_deleted'];
      if (!$intUserIP) {
        $intUserIP = "<i>None</i>";
      }
      if ($strDeleted == "Y") {
        $strRowStyle = " style=\"background-color: #999; color: #000;\"";
        $strEditDelete = "<a href=\"{FN_ADM_USER}?action=edit&amp;id=$intUserID\">Edit</a> : <a href=\"{FN_ADMIN_TOOLS}?action=reinstateuser&amp;id=$intUserID&amp;back={FN_ADM_USERS}\">Reinstate</a>";
      } else {
        $strRowStyle = "";
        $strEditDelete = "<a href=\"{FN_ADM_USER}?action=edit&amp;id=$intUserID\">Edit</a> : <a href=\"{FN_ADMIN_TOOLS}?action=suspenduser&amp;id=$intUserID&amp;back={FN_ADM_USERS}\">Suspend</a>";
      }
      if (($i % 2) == 0) {
        $strRowClass = "even";
      } else {
        $strRowClass = "odd";
      }
      $strHTML .= <<<TableRow
    <tr$strRowStyle class="$strRowClass">
      <td class="Centre">$intUserID</td>
      <td class="Left"><a href="$strViewUser">$strUsername</a></td>
      <td class="Centre">$intUserIP</td>
      <td class="Centre">$strEditDelete</td>
      <td class="Centre"><a href="{FN_ADM_USER_CONTACT}?id=$intUserID">Contact</a></td>
    </tr>

TableRow;
    }
    if (count($arrUsers) > 0) {
      $strHTML .= "  </tbody>\n</table></div>\n";
    } else {
      $strHTML .= "<p>No users found matching your search criteria.</p>\n";
    }
  }

  $CMS->AP->Display($strHTML);
?>