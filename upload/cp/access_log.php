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
  
  $strDateFilterHTML  = "<select id=\"date\" name=\"date\">\n";
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
<h1>$strPageTitle</h1>
<p>The access log provides information on what people are doing on your site.</p>
<p>A maximum number of log entries can be stored at any one time. You can change this in <a href="{FN_ADM_GENERAL_SETTINGS}">General Settings</a>. If the maximum is exceeded, the oldest entries will be deleted. If you wish to keep your log entries for longer, you should schedule a daily backup.</p>
<p>Article views are only logged if the article is accessed by a registered user. If a guest views an article, it will not appear in the access log, but it will increase the hit count for the article.</p>
<form action="{FN_ADM_ACCESS_LOG}" method="get">
<table class="OptionTable MediumTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <th colspan="2">Search the log</th>
  </tr>
  <tr>
    <td>
      <label for="tag">Tag filter:</label> 
    </td>
    <td>
      $strTagFilterHTML
    </td>
  </tr>
  <tr>
    <td>
      <label for="date">Date filter:</label> 
    </td>
    <td>
      $strDateFilterHTML
    </td>
  </tr>
  <tr>
    <td>
      <label for="q">Detail search:</label> 
    </td>
    <td>
      <input type="text" size="25" id="q" name="q" value="$strSearchParams" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="ip">IP address:</label> 
    </td>
    <td>
      <input type="text" size="25" id="ip" name="ip" value="$strIP" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="user">User:</label>
    </td>
    <td>
      $strInvalidUsername
      <input type="text" id="user" name="user" size="25" maxlength="100" value="$strUser" />
      <input type="checkbox" id="excludeuser" name="excludeuser" $strExcludeChecked/><label for="excludeuser">Exclude</label>
    </td>
  </tr>
  <tr>
    <td class="FootColour Centre" colspan="2">
      $strSearchButton
    </td>
  </tr>
</table>
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
<table class="DefaultTable PageTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour TinyCell" />
    <col class="BaseColour MediumCell" />
    <col class="BaseColour NarrowCell" />
    <col class="BaseColour NarrowCell" />
    <col class="BaseColour" />
    <col class="BaseColour NarrowCell" />
  </colgroup>
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
      if (($i % 2) == 0) {
        $strRowClass = "even";
      } else {
        $strRowClass = "odd";
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
  <tr class="$strRowClass">
    <td class="Centre">$intLogID</td>
    <td class="Centre">$dteLogDate</td>
    <td class="Centre">$strUserMsg</td>
    <td class="Centre">$strTag</td>
    <td>$strDetail</td>
    <td class="Centre">$intUserIP</td>
  </tr>

TableRow;
    }
    $strHTML .= "</table>\n";
  }

  $CMS->AP->Display($strHTML);
?>