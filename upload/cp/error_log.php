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
  $strPageTitle = "Error Log";
  $CMS->AP->SetTitle($strPageTitle);
  
  $strHTML = <<<END
<h1>$strPageTitle</h1>
<p>The error log provides information on recent error messages that have appeared on your site. The most common errors occur when someone tries to view a page that has been deleted, or view a page they do not have access to. These are not usually system errors.</p>

END;
  
  $arrDirList = $CMS->Dir(ABS_ROOT."data/logs", "files", false);
  
  $blnFoundItems = false;
  $blnFirstItem  = true;
  if (is_array($arrDirList)) {
    for ($i=0; $i<count($arrDirList); $i++) {
      $strDir = $arrDirList[$i];
      if ($strDir != "index.php") {
        if ($blnFirstItem) {
          $blnFirstItem  = false;
          $blnFoundItems = true;
          $strHTML .= <<<MainContentStart
<table id="tblSysResults" class="DefaultTable MediumTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour" />
    <col class="BaseColour MediumCell" />
  </colgroup>
  <tr>
    <th>Log File</th>
    <th>Options</th>
  </tr>

MainContentStart;
        }
        $strHTML .= <<<TableRow
  <tr>
    <td>$strDir</td>
    <td><a href="{FN_ADM_ERROR_LOG_FILE}?file=$strDir">View Log</a></td>
  </tr>

TableRow;
      }
    }
  }
  if ($blnFoundItems) {
    $strHTML .= "</table>";
  } else {
    $strHTML .= "<p><i>No error logs found.</i></p>\n";
  }
  
  $CMS->AP->Display($strHTML);
?>