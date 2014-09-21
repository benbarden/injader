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
  $strPageTitle = "Theme File Editor";
  $strTheme = empty($_GET['theme']) ? "" : $CMS->FilterAlphanumeric($_GET['theme'], "-_.");
  if (!$strTheme) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strTheme");
  }
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

  $strLayout = empty($_GET['layout']) ? "" : $CMS->FilterAlphanumeric($_GET['layout'], "-_.");
  $strLayout = str_replace("/", "", $strLayout);
  $strLayout = str_replace("\\", "", $strLayout);
  if ($strLayout == ".") {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strLayout");
  } elseif ($strLayout == "..") {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strLayout");
  }
  
  $strFileData = "";
  $strMissingContent = "";
  $strConfMsg = "";
  $strDisplayPath = "";
  
  if ($strLayout) {
    $strLSPath = ABS_SYS_THEMES.$strTheme."/".$strLayout."/".$strFile;
    $strDisplayPath = URL_SYS_THEMES.$strTheme."/".$strLayout."/".$strFile;
  } else {
    $strLSPath = ABS_SYS_THEMES.$strTheme."/".$strFile;
    $strDisplayPath = URL_SYS_THEMES.$strTheme."/".$strFile;
  }

  if (file_exists($strLSPath) !== true) {
    $CMS->Err_MFail("File not found.", $strLSPath);
  } else {
    $strPath = $strLSPath;
  }
  
  if ($_POST) {
    // Grab file data
    if (get_magic_quotes_gpc()) {
      $strFileData = $CMS->StripSlashesIFW($_POST['txtContent']);
    } else {
      $strFileData = $_POST['txtContent'];
    }
    // Write to file
    @ $cmsFile = fopen($strPath, 'w');
    if (!$cmsFile) {
      $CMS->Err_MFail("Cannot write to file. Please check the permissions on this file and try again.", $strPath);
    }
    fwrite($cmsFile, $strFileData);
    fclose($cmsFile);
    if ($strLayout) {
      $strBackLink = "{FN_ADM_THEME}?name=$strTheme&amp;layout=$strLayout";
    } else {
      $strBackLink = "{FN_ADM_THEME}?name=$strTheme";
    }
    $strConfMsg = "<p><b>File successfully updated.</b> <a href=\"$strBackLink\">Back to Theme Files</a></p>";
  } else {
    $strFileData = file_get_contents($strPath);
  }

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->LocationButton("Cancel", FN_ADM_THEME."?name=".$strTheme);
  
  if ($strLayout) {
    $strFormAction = "{FN_ADM_THEME_FILE}?theme=$strTheme&amp;layout=$strLayout&amp;file=$strFile";
  } else {
    $strFormAction = "{FN_ADM_THEME_FILE}?theme=$strTheme&amp;file=$strFile";
  }
  
    $strExtension = $CMS->GetExtensionFromPath($strFile);
  
  $strFileData = $CMS->DoEntities($strFileData);
  
  $strHTML = <<<EditForm
<h1>$strPageTitle</h1>
$strConfMsg
<script type="text/javascript">
/* <![CDATA[ */
    if (!document.all) {
        themeFileForm = document.getElementById('themeFileForm');
    }
/* ]]> */
</script>
<form id="themeFileForm" name="themeFileForm" action="$strFormAction" method="post">
<table class="DefaultTable PageTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td class="HeadColour">Editing file: <b>$strDisplayPath</b></td>
  </tr>
  <tr>
    <td>
      $strMissingContent
      <textarea id="txtContent" name="txtContent" cols="90" rows="25">$strFileData</textarea>
    </td>
  </tr>
  <tr>
    <td class="FootColour Centre">
      <input class="button" type="submit" value="Save Changes" />
      $strCancelButton
    </td>
  </tr>
</table>
</form>

EditForm;

  $CMS->AP->SetTitle($strPageTitle);
  $CMS->AP->Display($strHTML);
?>