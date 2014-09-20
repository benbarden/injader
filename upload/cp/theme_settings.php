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
  $strPageTitle = "Theme Settings";
  $strName = empty($_GET['name']) ? "" : $CMS->FilterAlphanumeric($_GET['name'], "-_.");
  if (!$strName) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strName");
  }
  $strSPath = ABS_SYS_THEMES.$strName."/settings.txt";
  if (file_exists($strSPath) !== true) {
    $CMS->Err_MFail("File not found.", $strSPath);
  } else {
    //$strPath = $strSPath;
  }
  
  if ($_POST) {
    // Number of items
    $intNumItems = empty($_POST['txtNumItems']) ? "" : $_POST['txtNumItems'];
    if (!$intNumItems) {
      $CMS->Err_MFail("Missing value: NumItems", "");
    }
    // Grab file data
    $strFileData = "";
    for ($i=0; $i<$intNumItems; $i++) {
      $strKey   = empty($_POST['txtKey'.$i])   ? "" : $_POST['txtKey'.$i];
      $strValue = empty($_POST['txtValue'.$i]) ? "" : $_POST['txtValue'.$i];
      if (($strKey) && ($strValue)) {
        if (get_magic_quotes_gpc()) {
          $strKey   = $CMS->StripSlashesIFW($strKey);
          $strValue = $CMS->StripSlashesIFW($strValue);
        }
        $strFileData .= $strKey." => ".$strValue."\r\n";
      }
    }
    // Write to file
    @ $cmsFile = fopen($strSPath, 'w');
    if (!$cmsFile) {
      $CMS->Err_MFail("Cannot write to file. Please check the permissions on this file and try again.", $strSPath);
    }
    fwrite($cmsFile, $strFileData);
    fclose($cmsFile);
    $strBackLink = "{FN_ADM_THEMES}";
    $strConfMsg = "<h1>$strPageTitle - $strName</h1>\n<p>Settings successfully updated. <a href=\"$strBackLink\">Back to Themes</a></p>";
    $strHTML = $strConfMsg;
    $CMS->AP->SetTitle($strPageTitle);
    $CMS->AP->Display($strHTML);
  }

  $strSettings = file_get_contents($strSPath);
  if (strpos($strSettings, "\r\n") !== false) {
    $arrSettings = explode("\r\n", $strSettings); // Windows
  } elseif (strpos($strSettings, "\r") !== false) {
    $arrSettings = explode("\r", $strSettings); // Mac
  } elseif (strpos($strSettings, "\n") !== false) {
    $arrSettings = explode("\n", $strSettings); // Linux
  } else {
    $arrSettings[0] = $strSettings; // Only one item
  }
  
  $strHTML = "<h1>$strPageTitle - $strName</h1>\n";
  
  if ($strSettings) {
    if ((is_array($arrSettings)) && (count($arrSettings) > 0)) {
      $strHTML .= <<<FormData
<form action="{FN_ADM_THEME_SETTINGS}?name=$strName" method="post">
<table class="DefaultTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour MediumCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td class="HeadColour"><b>Field</b></td>
    <td class="HeadColour"><b>Value</b></td>
  </tr>

FormData;
      for ($i=0; $i<count($arrSettings); $i++) {
        //if ($arrSettings[$i]) {
          $arrSettingRow = explode(" => ", $arrSettings[$i]);
          $strCol1 = empty($arrSettingRow[0]) ? "" : $arrSettingRow[0];
          $strCol2 = empty($arrSettingRow[1]) ? "" : $arrSettingRow[1];
          $strHTML .= <<<TableRow
  <tr>
    <td>
      <input type="text" id="txtKey$i" name="txtKey$i" value="$strCol1" size="30" maxlength="30" />
    </td>
    <td>
      <input type="text" id="txtValue$i" name="txtValue$i" value="$strCol2" size="50" maxlength="100" />
    </td>
  </tr>

TableRow;
        //}
      }
      $strHTML .= <<<FormData
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      <input type="hidden" name="txtNumItems" value="$i" />
      <input type="submit" value="Save Changes" />
    </td>
  </tr>
</table>
</form>

FormData;
    } else {
      $strHTML .= "No settings file.";
    }
  } else {
    $strHTML .= "No settings file.";
  }
  
  $CMS->AP->SetTitle($strPageTitle);
  $CMS->AP->Display($strHTML);
?>