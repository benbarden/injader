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
  $strPageTitle = "Theme Files";
  $strName = empty($_GET['name']) ? "" : $CMS->FilterAlphanumeric($_GET['name'], "-_.");
  if (!$strName) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strName");
  }
  $strLayout = empty($_GET['layout']) ? "" : $CMS->FilterAlphanumeric($_GET['layout'], "-_.");
  
  $strLSPath2 = ABS_SYS_THEMES.$strName."/LayoutStyles.txt";
  if (file_exists($strLSPath2) !== true) {
    $CMS->Err_MFail("File not found.", $strLSPath2);
  } else {
    $strPath = $strLSPath2;
  }
  
  $strLayoutStyles = file_get_contents($strPath);
  if (strpos($strLayoutStyles, "\r\n") !== false) {
    $arrStyles = explode("\r\n", $strLayoutStyles); // Windows
  } elseif (strpos($strLayoutStyles, "\r") !== false) {
    $arrStyles = explode("\r", $strLayoutStyles); // Mac
  } elseif (strpos($strLayoutStyles, "\n") !== false) {
    $arrStyles = explode("\n", $strLayoutStyles); // Linux
  } else {
    $arrStyles[0] = $strLayoutStyles; // Only one layout style
  }

  if ($strLayout) {
    $strSubHead = "Files for layout: $strLayout";
  } else {
    $strSubHead = "Files for layout: (Default)";
  }
  
  $strHTML = <<<MainContentStart
<h1>$strPageTitle</h1>
<p>Theme Name: <b>$strName</b></p>
MainContentStart;
  
  if (is_array($arrStyles)) {
    $strHTML .= <<<FileList
<h2>Layouts</h2>
<table class="DefaultTable MediumTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour" />
    <col class="BaseColour NarrowCell" />
  </colgroup>
  <tr>
    <th>Layout Name</th>
    <th>Options</th>
  </tr>

FileList;

    for ($j=0; $j<count($arrStyles); $j++) {
      $strLayoutItem = $arrStyles[$j];
      if ($j == 0) {
        $strHTML .= <<<TableRow
  <tr>
    <td>(Default)</td>
    <td class="Centre"><a href="{FN_ADM_THEME}?name=$strName">View Files</a></td>
  </tr>

TableRow;
      }
      if (trim($strLayoutItem)) {
        $strHTML .= <<<TableRow
  <tr>
    <td>$strLayoutItem</td>
    <td class="Centre"><a href="{FN_ADM_THEME}?name=$strName&amp;layout=$strLayoutItem">View Files</a></td>
  </tr>

TableRow;
      }
    }
    $strHTML .= "</table>\n";
  }
  
  $strHTML .= <<<FileList
<h2>$strSubHead</h2>
<table class="DefaultTable MediumTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour" />
    <col class="BaseColour NarrowCell" />
  </colgroup>
  <tr>
    <th>File</th>
    <th>Options</th>
  </tr>

FileList;

  if ($strLayout) {
    $arrDirList = $CMS->Dir(ABS_SYS_THEMES.$strName."/".$strLayout, "files", true);
  } else {
    $arrDirList = $CMS->Dir(ABS_SYS_THEMES.$strName, "files", true);
  }

  if (is_array($arrDirList)) {
    if ($strLayout) {
      $strLayoutURL = "&amp;layout=$strLayout";
    } else {
      $strLayoutURL = "";
    }
    for ($i=0; $i<count($arrDirList); $i++) {
      $strFile = $arrDirList[$i];
      /*
      $blnFound = false;
      for ($j=0; $j<count($arrStyles); $j++) {
        if ($arrStyles[$j] == $strFile) {
          $blnFound = true;
          break;
        }
      }
      if (($strFile != "images") && (!$blnFound)) {
      }
      */
      $strHTML .= <<<TableRow
  <tr>
    <td>$strFile</td>
    <td class="Centre"><a href="{FN_ADM_THEME_FILE}?theme=$strName$strLayoutURL&amp;file=$strFile">Edit</a></td>
  </tr>

TableRow;
    }
    $strHTML .= "</table>\n";
  }
  $CMS->AP->SetTitle($strPageTitle);
  $CMS->AP->Display($strHTML);
?>