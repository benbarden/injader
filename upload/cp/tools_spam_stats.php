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

  $strPageTitle = "Comment Spam Stats";

  $CMS->AP->SetTitle($strPageTitle);

  $strHTML = <<<PageHeader
<h1 class="page-header">$strPageTitle</h1>
<p>This page can be used if you have a lot of spam comments and you want to isolate certain IP addresses. These can be banned within your website control panel. Deleted comments will not be included in these stats.</p>

PageHeader;

  $blnFirstMatch = true;
  $arrSpamStats = $CMS->ResultQuery("SELECT ip_address, count(*) AS count FROM {IFW_TBL_COMMENTS} WHERE comment_status = 'Spam' GROUP BY ip_address ORDER BY ip_address ASC", basename(__FILE__), __LINE__);
  if (is_array($arrSpamStats)) {
    $strHTML .= <<<GroupHeader
<div class="table-responsive">
<table class="table table-striped" style="width: 400px;">
  <tr class="separator-row">
    <td>IP Address</td>
    <td>Count</td>
  </tr>

GroupHeader;
    for ($i=0; $i<count($arrSpamStats); $i++) {
      $strIPAddress = $arrSpamStats[$i]['ip_address'];
      $intCount     = $arrSpamStats[$i]['count'];
      $strHTML .= <<<TableRow
  <tr>
    <td class="Centre">$strIPAddress</td>
    <td class="Centre">$intCount</td>
  </tr>

TableRow;
    }
    $strHTML .= "</table>\n";
  } else {
    $strHTML .= "<p><em>There are no spam comments.</em></p>";
  }

  $CMS->AP->Display($strHTML);
