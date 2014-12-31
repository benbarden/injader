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
  $strPageTitle = "General Settings";

  $CMS->AP->SetTitle($strPageTitle);
  
  $blnSubmitForm = false;
  $strMissingTitleText  = "";
  $strMissingDescText   = "";
  $strMissingEmailText  = "";
  $strMissingServerTimeText = "";

  if ($_POST) {
    $strSiteTitle        = $CMS->AddSlashesIFW($_POST['txtSiteTitle']);
    $strSiteDesc         = $CMS->AddSlashesIFW($_POST['txtSiteDescription']);
    $strSiteKeywords     = $CMS->AddSlashesIFW($_POST['txtSiteKeywords']);
    $strSiteEmail        = $_POST['txtSiteEmail'];
    $strSiteHeader       = $CMS->AddSlashesIFW($_POST['txtSiteHeader']);
    $strSiteFavicon      = $CMS->AddSlashesIFW($_POST['txtSiteFavicon']);
    $intServerTimeOffset = $_POST['txtServerTimeOffset'];
    $intSystemPageCount  = $CMS->FilterNumeric($_POST['txtSystemPageCount']);
    if ((!$intSystemPageCount) || ($intSystemPageCount <= 0)) {
      $intSystemPageCount = 25;
    }
    $intMaxLogEntries    = $CMS->FilterNumeric($_POST['txtMaxLogEntries']);
    if ((!$intMaxLogEntries) || ($intMaxLogEntries <= 0)) {
      $intMaxLogEntries = 3000;
    }
    $strUserReg          = !empty($_POST['chkUserRegistration']) ? 1 : 0;
    $intCookieDays       = $CMS->FilterNumeric($_POST['txtCookieDays']);
    if ((!$intCookieDays) || ($intCookieDays <= 0)) {
      $intCookieDays = 1;
    }
    $intDateFormat       = $_POST['optDateFormat'];
    $intTimeFormat       = $_POST['optTimeFormat'];
    // Validation
    $blnSubmitForm = true;
    if (!$strSiteTitle) {
      $blnSubmitForm = false;
      $strMissingTitleText = $CMS->AC->InvalidFormData("");
    }
    if (!$strSiteEmail) {
      $blnSubmitForm = false;
      $strMissingEmailText = $CMS->AC->InvalidFormData("");
    }
    if (!$intServerTimeOffset) {
      $intServerTimeOffset = '0';
    }
    // Update database
    if ($blnSubmitForm) {
      if ($CMS->SYS->GetSysPref(C_PREF_SITE_TITLE) != $strSiteTitle) {
        $CMS->SYS->WriteSysPref(C_PREF_SITE_TITLE, $strSiteTitle);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SITE_DESCRIPTION) != $strSiteDesc) {
        $CMS->SYS->WriteSysPref(C_PREF_SITE_DESCRIPTION, $strSiteDesc);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SITE_KEYWORDS) != $strSiteKeywords) {
        $CMS->SYS->WriteSysPref(C_PREF_SITE_KEYWORDS, $strSiteKeywords);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL) != $strSiteEmail) {
        $CMS->SYS->WriteSysPref(C_PREF_SITE_EMAIL, $strSiteEmail);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SITE_HEADER) != $strSiteHeader) {
        $CMS->SYS->WriteSysPref(C_PREF_SITE_HEADER, $strSiteHeader);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SITE_FAVICON) != $strSiteFavicon) {
        $CMS->SYS->WriteSysPref(C_PREF_SITE_FAVICON, $strSiteFavicon);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SERVER_TIME_OFFSET) != $intServerTimeOffset) {
        $CMS->SYS->WriteSysPref(C_PREF_SERVER_TIME_OFFSET, $intServerTimeOffset);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_SYSTEM_PAGE_COUNT) != $intSystemPageCount) {
        $CMS->SYS->WriteSysPref(C_PREF_SYSTEM_PAGE_COUNT, $intSystemPageCount);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_MAX_LOG_ENTRIES) != $intMaxLogEntries) {
        $CMS->SYS->WriteSysPref(C_PREF_MAX_LOG_ENTRIES, $intMaxLogEntries);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_USER_REGISTRATION) != $strUserReg) {
        $CMS->SYS->WriteSysPref(C_PREF_USER_REGISTRATION, $strUserReg);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COOKIE_DAYS) != $intCookieDays) {
        $CMS->SYS->WriteSysPref(C_PREF_COOKIE_DAYS, $intCookieDays);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_DATE_FORMAT) != $intDateFormat) {
        $CMS->SYS->WriteSysPref(C_PREF_DATE_FORMAT, $intDateFormat);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_TIME_FORMAT) != $intTimeFormat) {
        $CMS->SYS->WriteSysPref(C_PREF_TIME_FORMAT, $intTimeFormat);
      }
      // Rebuild the cache
      $CMS->SYS->RebuildCache();
    }
  }
  
  if ($_POST) {
    $strSiteTitle    = $CMS->StripSlashesIFW($strSiteTitle);
    $strSiteDesc     = $CMS->StripSlashesIFW($strSiteDesc);
    $strSiteKeywords = $CMS->StripSlashesIFW($strSiteKeywords);
    $strSiteHeader   = $CMS->StripSlashesIFW($strSiteHeader);
    $strSiteFavicon  = $CMS->StripSlashesIFW($strSiteFavicon);
  } else {
    if (!isset($CMS->SYS->arrSysPrefs[C_PREF_SITE_TITLE])) {
      $CMS->SYS->GetAllSysPrefs();
    }
    $arrSysPrefs = $CMS->SYS->arrSysPrefs;
    foreach ($arrSysPrefs as $strKey => $strValue) {
      switch ($strKey) {
        case C_PREF_SITE_TITLE:            $strSiteTitle = $strValue; break;
        case C_PREF_SITE_DESCRIPTION:      $strSiteDesc = $strValue; break;
        case C_PREF_SITE_KEYWORDS:         $strSiteKeywords = $strValue; break;
        case C_PREF_SITE_EMAIL:            $strSiteEmail = $strValue; break;
        case C_PREF_SITE_HEADER:           $strSiteHeader = $strValue; break;
        case C_PREF_SITE_FAVICON:          $strSiteFavicon = $strValue; break;
        case C_PREF_SERVER_TIME_OFFSET:    $intServerTimeOffset = $strValue; break;
        case C_PREF_SYSTEM_PAGE_COUNT:     $intSystemPageCount = $strValue; break;
        case C_PREF_MAX_LOG_ENTRIES:       $intMaxLogEntries = $strValue; break;
        case C_PREF_USER_REGISTRATION:     $strUserReg = $strValue; break;
        case C_PREF_COOKIE_DAYS:           $intCookieDays = $strValue; break;
        case C_PREF_DATE_FORMAT:           $intDateFormat = $strValue; break;
        case C_PREF_TIME_FORMAT:           $intTimeFormat = $strValue; break;
      }
    }
  }
  
  if ($blnSubmitForm) {
    $strConfMsg = "<p><b>Settings updated successfully.</b></p>\n\n";
  } else {
    $strConfMsg = "";
  }
  
  $strSiteHeader = $CMS->DoEntities($strSiteHeader);
  
  $dteCurrentServerTime = date('H:i:s');
  $dteOffsetServerTime  = $CMS->SYS->GetCurrentDateAndTime();
  
  $strDateFormatList = $CMS->DD->SystemDateFormat($intDateFormat);
  $strTimeFormatList = $CMS->DD->SystemTimeFormat($intTimeFormat);

  $strUserReg == 1 ? $strUserRegChecked = " checked=\"checked\"" : $strUserRegChecked = "";

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  // Main form
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strConfMsg
<form id="frmSystemPrefs" action="{FN_ADM_GENERAL_SETTINGS}" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td>
      <b><label for="txtSiteTitle">Site Title</label></b>
    </td>
    <td>
      $strMissingTitleText
      <input id="txtSiteTitle" name="txtSiteTitle" type="text" size="50" maxlength="100" value="$strSiteTitle" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteDescription">Site Description</label></b>
    </td>
    <td>
      $strMissingDescText
      <textarea id="txtSiteDescription" name="txtSiteDescription" style="width: 400px; height: 100px;">$strSiteDesc</textarea>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteKeywords">Site Keywords</label></b>
    </td>
    <td>
      <textarea id="txtSiteKeywords" name="txtSiteKeywords" style="width: 400px; height: 100px;">$strSiteKeywords</textarea>
      <br />Separate with commas. E.g. weather, blue sky, clouds
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteHeader">Custom header (&lt;head&gt; tag)</label></b>
    </td>
    <td>
      <textarea id="txtSiteHeader" name="txtSiteHeader" style="width: 400px; height: 200px; font-family: 'Courier New', monospace; font-size: 12px;">$strSiteHeader</textarea>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteEmail">Site Email</label></b>
    </td>
    <td>
      $strMissingEmailText
      <input id="txtSiteEmail" name="txtSiteEmail" type="text" size="40" maxlength="100" value="$strSiteEmail" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteFavicon">Favicon</label></b>
    </td>
    <td>
      <input id="txtSiteFavicon" name="txtSiteFavicon" type="text" size="40" maxlength="100" value="$strSiteFavicon" />
    </td>
  </tr>
  <tr class="separator-row">
    <td colspan="2">Date and Time</td>
  </tr>
  <tr>
    <td>
      <b><label for="optDateFormat">Date Format</label></b>
    </td>
    <td>
      <select id="optDateFormat" name="optDateFormat">
      $strDateFormatList
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="optTimeFormat">Time Format</label></b>
    </td>
    <td>
      <select id="optTimeFormat" name="optTimeFormat">
      $strTimeFormatList
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtServerTimeOffset">Server Time Offset</label></b>
    </td>
    <td>
      $strMissingServerTimeText
      <input id="txtServerTimeOffset" name="txtServerTimeOffset" type="text" size="3" maxlength="3" value="$intServerTimeOffset" />
    </td>
  </tr>
  <tr class="separator-row">
    <td colspan="2">User Settings</td>
  </tr>
  <tr>
    <td>
      <b><label for="chkUserRegistration">Allow user registrations</label></b>
    </td>
    <td>
      <input id="chkUserRegistration" name="chkUserRegistration" type="checkbox"$strUserRegChecked />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtCookieDays">Stay logged in for</label></b>
    </td>
    <td>
      <input id="txtCookieDays" name="txtCookieDays" type="text" size="3" maxlength="3" value="$intCookieDays" /> days
    </td>
  </tr>
  <tr class="separator-row">
    <td colspan="2">System Settings</td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSystemPageCount">Control Panel page count</label></b>
    </td>
    <td>
      <input id="txtSystemPageCount" name="txtSystemPageCount" type="text" size="4" maxlength="4" value="$intSystemPageCount" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtMaxLogEntries">Log file row limit</label></b>
    </td>
    <td>
      <input id="txtMaxLogEntries" name="txtMaxLogEntries" type="text" size="5" maxlength="5" value="$intMaxLogEntries" />
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</div>
</form>

END;
  $CMS->AP->Display($strHTML);
