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
  $strPageTitle = "User Sessions";

  $CMS->AP->SetTitle($strPageTitle);

  $arrSessions = $CMS->USess->GetAll();
  
  $strHTML = <<<MainContentStart
<h1>$strPageTitle</h1>
<p>This page displays a list of all currently logged in users. Users with multiple entries may have logged in at several locations or on different computers.</p>
<p>If you delete a session, the user will no longer be logged in at that location. You cannot delete your own session on this screen as it would log you out. If you wish to log out, please use the Logout link at the bottom of the page.</p>
<p><a href="{FN_ADMIN_TOOLS}?action=deleteexpiredsessions&amp;back={FN_ADM_TOOLS_USER_SESSIONS}">Delete all expired sessions</a>.</p>

MainContentStart;

  $strMySessionID = $CMS->RES->GetSessionIDCookie();
	for ($i=0; $i<count($arrSessions); $i++) {
    $intSID        = $arrSessions[$i]['id'];
    $strSessionID  = $arrSessions[$i]['session_id'];
    $intUserID     = $arrSessions[$i]['user_id'];
    $strUserName   = $arrSessions[$i]['username'];
    $strSEOName    = $arrSessions[$i]['seo_username'];
    $CMS->PL->SetTitle($strSEOName);
    $strViewUser = $CMS->PL->ViewUser($intUserID);
    $intUserIP     = $arrSessions[$i]['ip_address'];
    $dteLoginDate  = $arrSessions[$i]['login_date'];
    $dteExpiryDate = $arrSessions[$i]['expiry_date'];
    $strUserAgent  = $arrSessions[$i]['user_agent'];
    if ($i==0) {
      $strHTML .= <<<TableHeader
<table id="tblUserSessions" class="DefaultTable PageTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour TinyCell" />
    <col class="BaseColour TinyCell" />
    <col class="BaseColour TinyCell" />
    <col class="BaseColour TinyCell" />
    <col class="BaseColour WideCell" />
    <col class="BaseColour TinyCell" />
  </colgroup>
  <thead>
    <tr>
      <th>Username</th>
      <th>IP</th>
      <th>Login Date</th>
      <th>Expiry Date</th>
      <th>User Agent</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody>

TableHeader;
    }
    if ($strSessionID == $strMySessionID) {
      $strDeleteLink = "";
    } else {
      $strDeleteLink = "<a href=\"{FN_ADMIN_TOOLS}?action=deletesession&amp;id=$intSID&amp;back={FN_ADM_TOOLS_USER_SESSIONS}\">Delete</a>";
    }
    if (($i % 2) == 0) {
      $strRowClass = "even";
    } else {
      $strRowClass = "odd";
    }
    $strHTML .= <<<TableRow
    <tr class="$strRowClass">
      <td class="Centre"><a href="$strViewUser">$strUserName</a></td>
      <td class="Centre">$intUserIP</td>
      <td class="Centre">$dteLoginDate</td>
      <td class="Centre">$dteExpiryDate</td>
      <td class="Left">$strUserAgent</td>
      <td class="Centre">$strDeleteLink</td>
    </tr>

TableRow;
	}
  $strHTML .= "  </tbody>\n</table>\n";
  $CMS->AP->Display($strHTML);
?>