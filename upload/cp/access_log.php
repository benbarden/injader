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
  $strPageTitle = "Access Log";
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

  if ($_GET) {
    $strSearchParams = empty($_GET['q'])    ? "" : $_GET['q'];
    $strTagFilter    = empty($_GET['tag'])  ? "" : $_GET['tag'];
    $strIP           = empty($_GET['ip'])   ? "" : $_GET['ip'];
    $dteDateParam    = empty($_GET['date']) ? "" : $_GET['date'];
  } else {
    $strSearchParams = "";
    $strTagFilter = "";
    $strIP = "";
    $dteDateParam = "";
  }
  
  $strWhereClause = "";
  $strGetURL = "";

  $strTagFilterHTML = $CMS->DD->AccessLogTags($strTagFilter);
  
  if ($strSearchParams) {
    if ($strWhereClause) {
      $strWhereClause .= " AND detail LIKE '%".$CMS->AddSlashesIFW($strSearchParams)."%'";
    } else {
      $strWhereClause  = " WHERE detail LIKE '%".$CMS->AddSlashesIFW($strSearchParams)."%'";
    }
    if (empty($strGetURL)) {
      $strGetURL = "?q=$strSearchParams";
    } else {
      $strGetURL .= "&amp;q=$strSearchParams";
    }
  }
  
  if ($strIP) {
    if ($strWhereClause) {
      $strWhereClause .= " AND al.ip_address LIKE '$strIP%'";
    } else {
      $strWhereClause  = " WHERE al.ip_address LIKE '$strIP%'";
    }
    if (empty($strGetURL)) {
      $strGetURL = "?ip=$strIP";
    } else {
      $strGetURL .= "&amp;ip=$strIP";
    }
  }

  if ($strTagFilter) {
    if ($strWhereClause) {
      $strWhereClause .= " AND tag = '$strTagFilter'";
    } else {
      $strWhereClause  = " WHERE tag = '$strTagFilter'";
    }
    if (empty($strGetURL)) {
      $strGetURL = "?tag=$strTagFilter";
    } else {
      $strGetURL .= "&amp;tag=$strTagFilter";
    }
  }

  $strUser = "";
  $strInvalidUsername = "";
  $blnInvalidSearch = false;
  $strExcludeChecked = "";
  $blnExclude = false;
  if (!empty($_GET['user'])) {
    if (!empty($_GET['excludeuser'])) {
      $strExcludeChecked = "checked=\"checked\" ";
      $blnExclude = true;
      $strMatch = "<>";
    } else {
      $strMatch = "=";
    }
    $strUser = $CMS->FilterAlphanumeric($_GET['user'], C_CHARS_USERNAME);
    $intUserID = $CMS->US->GetIDFromName($strUser);
    if ($intUserID) {
      if (empty($strWhereClause)) {
        $strWhereClause = "WHERE al.user_id $strMatch $intUserID";
      } else {
        $strWhereClause .= " AND al.user_id $strMatch $intUserID";
      }
      if (empty($strGetURL)) {
        $strGetURL = "?user=$strUser";
      } else {
        $strGetURL .= "&amp;user=$strUser";
      }
    } else {
      $intUserID = 0;
      $strInvalidUsername = $CMS->AC->InvalidFormData(M_ERR_SEARCH_USER_NOT_FOUND);
      $blnInvalidSearch = true;
    }
    if (($strGetURL) && ($blnExclude)) {
      $strGetURL .= "&amp;excludeuser=on";
    }
  } else {
    $intUserID = "";
  }

  $now = time();
  $intOffset = $CMS->SYS->GetSysPref(C_PREF_SERVER_TIME_OFFSET);
  $dteBack[0] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now),  date("Y", $now));
  $dteBack[0] = date('Y-m-d', $dteBack[0]);
  $dteBack[1] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-1,  date("Y", $now));
  $dteBack[1] = date('Y-m-d', $dteBack[1]);
  $dteBack[2] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-2,  date("Y", $now));
  $dteBack[2] = date('Y-m-d', $dteBack[2]);
  $dteBack[3] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-3,  date("Y", $now));
  $dteBack[3] = date('Y-m-d', $dteBack[3]);
  $dteBack[4] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-4,  date("Y", $now));
  $dteBack[4] = date('Y-m-d', $dteBack[4]);
  $dteBack[5] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-5,  date("Y", $now));
  $dteBack[5] = date('Y-m-d', $dteBack[5]);
  $dteBack[6] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-6,  date("Y", $now));
  $dteBack[6] = date('Y-m-d', $dteBack[6]);
  $dteBack[7] = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now),  date("d", $now)-7,  date("Y", $now));
  $dteBack[7] = date('Y-m-d', $dteBack[7]);
  
  $strDateFilterHTML  = "<select id=\"date\" name=\"date\" class=\"ij-dropdown\">\n";
  $strDateFilterHTML .= "<option value=\"\">(Display all)</option>\n";
  for ($i=0; $i<8; $i++) {
    if ($dteBack[$i] == $dteDateParam) {
      $strSelected = " selected=\"selected\"";
    } else {
      $strSelected = "";
    }
    $strDateFilterHTML .= "<option value=\"$dteBack[$i]\"$strSelected>$dteBack[$i]</option>\n";
  }
  $strDateFilterHTML .= "</select>\n";
  
  if ($dteDateParam) {
    if ($strWhereClause) {
      $strWhereClause .= " AND log_date LIKE '%$dteDateParam%'";
    } else {
      $strWhereClause  = " WHERE log_date LIKE '%$dteDateParam%'";
    }
    if (empty($strGetURL)) {
      $strGetURL = "?date=$dteDateParam";
    } else {
      $strGetURL .= "&amp;date=$dteDateParam";
    }
  }
  
  $strSearchButton = $CMS->AC->SearchButton();

  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<form action="{FN_ADM_ACCESS_LOG}" method="get">
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td>
      <label for="q">Detail:</label>
      <input class="ij-text" type="text" size="18" id="q" name="q" value="$strSearchParams" />
    </td>
    <td>
      <label for="tag">Tag:</label>
      $strTagFilterHTML
    </td>
    <td>
      <label for="ip">IP:</label>
      <input type="text" size="12" id="ip" name="ip" value="$strIP" />
    </td>
    <td>
      $strSearchButton
    </td>
  </tr>
</table>
</div>
</form>

END;

  // Page numbers
  $intContentPerPage = $CMS->SYS->GetSysPref(C_PREF_SYSTEM_PAGE_COUNT);
  $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
  // Get log items
  $strSQL = "SELECT al.*, u.username, u.seo_username FROM {IFW_TBL_ACCESS_LOG} al LEFT JOIN {IFW_TBL_USERS} u ON al.user_id = u.id $strWhereClause ORDER BY al.id DESC LIMIT $intStart, $intContentPerPage";
  $arrLogs = $CMS->ResultQuery($strSQL, __FILE__, __LINE__);
  $arrLogCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_ACCESS_LOG} al LEFT JOIN {IFW_TBL_USERS} u ON al.user_id = u.id $strWhereClause ORDER BY al.id DESC", basename(__FILE__), __LINE__);
  $intLogCount = $arrLogCount[0]['count'];
  // Page number links
  $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intLogCount);
  if (empty($strGetURL)) {
    $strPageNumbers = $CMS->PNN->MakeOneParam($intNumPages, $intPageNumber, basename(__FILE__));
  } else {
    $strPageNumbers = $CMS->PNN->Make($intNumPages, $intPageNumber, basename(__FILE__)."$strGetURL");
  }
  // Check for blanks
  if ((count($arrLogs) == 0) || (!$arrLogs)) {
    $strHTML .= "<h2>Sorry - no log entries were found.</h2>";
  } else {
    for ($i=0; $i<count($arrLogs); $i++) {
      if ($i == 0) {
        $strHTML .= <<<TableHeader
<div class="spacer">&nbsp;</div>
$strPageNumbers
<div class="table-responsive">
<table class="table table-striped">
  <!--
  <colgroup>
    <col class="BaseColour TinyCell" />
    <col class="BaseColour MediumCell" />
    <col class="BaseColour NarrowCell" />
    <col class="BaseColour NarrowCell" />
    <col class="BaseColour" />
    <col class="BaseColour NarrowCell" />
  </colgroup>
  -->
  <tr>
    <th>ID</th>
    <th>Date</th>
    <th>User</th>
    <th>Tag</th>
    <th>Detail</th>
    <th>IP</th>
  </tr>

TableHeader;
      }
      $intLogID    = $arrLogs[$i]['id'];
      $strDetail   = $arrLogs[$i]['detail'];
      $strTag      = $arrLogs[$i]['tag'];
      $intUserID   = $arrLogs[$i]['user_id'];
      $strUsername = $arrLogs[$i]['username'];
      $strSEOName  = $arrLogs[$i]['seo_username'];
      $dteLogDate  = $arrLogs[$i]['log_date'];
      $intUserIP   = $arrLogs[$i]['ip_address'];
      if ($strUsername) {
        $CMS->PL->SetTitle($strSEOName);
        $strViewUser = $CMS->PL->ViewUser($intUserID);
        $strUserMsg = "<a href=\"$strViewUser\">$strUsername</a>";
      } else {
        $strUserMsg = "<i>Guest</i>";
      }
      if (!$intUserIP) {
        $intUserIP = "<i>Unknown</i>";
      }
      $strHTML .= <<<TableRow
  <tr>
    <td>$intLogID</td>
    <td>$dteLogDate</td>
    <td>$strUserMsg</td>
    <td>$strTag</td>
    <td>$strDetail</td>
    <td>$intUserIP</td>
  </tr>

TableRow;
    }
    $strHTML .= "</table>\n</div>\n";
  }

  $CMS->AP->Display($strHTML);
?>