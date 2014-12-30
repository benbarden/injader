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
  $strPageTitle = "Error Log";
  $CMS->AP->SetTitle($strPageTitle);
  
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>

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
<div class="table-responsive">
<table class="table table-striped" style="width: 400px;">
  <tr>
    <th>Log File</th>
  </tr>

MainContentStart;
        }
        $strHTML .= <<<TableRow
  <tr>
    <td><a href="{FN_ADM_ERROR_LOG_FILE}?file=$strDir">$strDir</a></td>
  </tr>

TableRow;
      }
    }
  }
  if ($blnFoundItems) {
    $strHTML .= "</table></div>";
  } else {
    $strHTML .= "<p><i>No error logs found.</i></p>\n";
  }
  
  $CMS->AP->Display($strHTML);
