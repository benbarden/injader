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
  $strPageTitle = "Themes";
  $CMS->AP->SetTitle($strPageTitle);
  
  $strDefaultTheme = $CMS->SYS->GetSysPref(C_PREF_DEFAULT_THEME);

  $arrDirList = $CMS->Dir(ABS_SYS_THEMES, "both", false);

  $strHTML = <<<MainContentStart
<h1>$strPageTitle</h1>
<table id="tblSysResults" class="DefaultTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour MediumCell" />
    <col class="BaseColour MediumCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <th>Theme Name</th>
    <th>Configure</th>
    <th>Usage</th>
  </tr>

MainContentStart;

  if (is_array($arrDirList)) {
    for ($i=0; $i<count($arrDirList); $i++) {
      $strDir = $arrDirList[$i];
      if ($strDir != "index.php") {
        if ($strDir == $strDefaultTheme) {
          $strDefaultLink = "<i>(default)</i>";
        } else {
          $strDefaultLink = "<a href=\"{FN_ADMIN_TOOLS}?action=applytheme&amp;theme=$strDir&amp;back={FN_ADM_THEMES}\">Apply</a>";
        }
        $strHTML .= <<<TableRow
  <tr>
    <td>$strDir</td>
    <td><a href="{FN_ADM_THEME}?name=$strDir">Files</a> : <a href="{FN_ADM_THEME_SETTINGS}?name=$strDir">Settings</a></td>
    <td><a href="{FN_ADM_THEME_PREVIEW}?name=$strDir">Preview</a> : $strDefaultLink</td>
  </tr>

TableRow;
      }
    }
  }
  $strHTML .= "</table>\n";

  $CMS->AP->Display($strHTML);
?>