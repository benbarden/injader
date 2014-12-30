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
  $strPageTitle = "Error Log Viewer";
  $strFile = empty($_GET['file']) ? "" : $CMS->FilterAlphanumeric($_GET['file'], "-_.");
  $strFile = str_replace("/", "", $strFile);
  $strFile = str_replace("\\", "", $strFile);
  if (!$strFile) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strFile");
  } elseif ($strFile == ".") {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strFile");
  } elseif ($strFile == "..") {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strFile");
  }
  
  $strLogFilePath = ABS_ROOT."data/logs/".$strFile;
  if (file_exists($strLogFilePath) !== true) {
    $CMS->Err_MFail("File not found.", $strLogFilePath);
  }
  
  $strFileData = file_get_contents($strLogFilePath);
  $strFileData = $CMS->DoEntities($strFileData);
  
  $strHTML = <<<EditForm
<h1 class="page-header">$strPageTitle</h1>
<div class="table-responsive">
<table class="table table-striped">
  <tr class="separator-row">
    <td>
    Viewing file: $strFile |
    <a href="{FN_ADM_ERROR_LOG}">Go back</a>
    </td>
  </tr>
  <tr>
    <td>
      <textarea id="txtContent" name="txtContent" style="width: 100%; height: 400px; font-family: 'Courier New', monospace; font-size: 12px;">$strFileData</textarea>
    </td>
  </tr>
  <tr>
    <td class="FootColour"><a href="{FN_ADM_ERROR_LOG}">Go back to the list of error logs.</a></td>
  </tr>
</table>
</div>

EditForm;

  $CMS->AP->SetTitle($strPageTitle);
  $CMS->AP->Display($strHTML);
?>