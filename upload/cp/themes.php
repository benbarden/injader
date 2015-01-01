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
  $strPageTitle = "Themes";
  $CMS->AP->SetTitle($strPageTitle);
  
  $strDefaultTheme = $CMS->SYS->GetSysPref(C_PREF_DEFAULT_THEME);

  $arrDirList = glob(ABS_ROOT.'themes/user/*');

  $strHTML = <<<MainContentStart
<h1 class="page-header">$strPageTitle</h1>
<div class="table-responsive">
<table class="table table-striped" style="width: 400px;">
  <tr class="separator-row">
    <td>Theme</td>
    <td>Usage</td>
  </tr>

MainContentStart;

  if (is_array($arrDirList)) {
    for ($i=0; $i<count($arrDirList); $i++) {
      $strDir = $arrDirList[$i];
      if (!is_dir($strDir)) continue;
      //if ($strDir == 'index.php') continue;
      $strDir = basename($strDir);
        if ($strDir == $strDefaultTheme) {
          $strDefaultLink = "<i>(default)</i>";
        } else {
          $strDefaultLink = "<a href=\"{FN_ADMIN_TOOLS}?action=applytheme&amp;theme=$strDir&amp;back={FN_ADM_THEMES}\">Apply</a>";
        }
        $strHTML .= <<<TableRow
  <tr>
    <td>$strDir</td>
    <td>$strDefaultLink</td>
  </tr>

TableRow;
    }
  }
  $strHTML .= "</table></div>\n";

  $CMS->AP->Display($strHTML);
