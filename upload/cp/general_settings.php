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
    $strRSSArticlesURL   = $_POST['txtRSSArticlesURL'];
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
    $strSystemLock       = !empty($_POST['chkSystemLock']) ? "Y" : "N";
    $strUserReg          = !empty($_POST['chkUserRegistration']) ? 1 : 0;
    $strChangePass       = !empty($_POST['chkUserChangePass']) ? "Y" : "N";
    $strAllowPasswordResets = !empty($_POST['chkAllowPasswordResets']) ? "Y" : "N";
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
    if (!$strSiteDesc) {
      $blnSubmitForm = false;
      $strMissingDescText = $CMS->AC->InvalidFormData("");
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
      if ($CMS->SYS->GetSysPref(C_PREF_RSS_ARTICLES_URL) != $strRSSArticlesURL) {
        $CMS->SYS->WriteSysPref(C_PREF_RSS_ARTICLES_URL, $strRSSArticlesURL);
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
      if ($CMS->SYS->GetSysPref(C_PREF_SYSTEM_LOCK) != $strSystemLock) {
        $CMS->SYS->WriteSysPref(C_PREF_SYSTEM_LOCK, $strSystemLock);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_USER_REGISTRATION) != $strUserReg) {
        $CMS->SYS->WriteSysPref(C_PREF_USER_REGISTRATION, $strUserReg);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_USER_CHANGE_PASS) != $strChangePass) {
        $CMS->SYS->WriteSysPref(C_PREF_USER_CHANGE_PASS, $strChangePass);
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
      if ($CMS->SYS->GetSysPref(C_PREF_ALLOW_PASSWORD_RESETS) != $strAllowPasswordResets) {
        $CMS->SYS->WriteSysPref(C_PREF_ALLOW_PASSWORD_RESETS, $strAllowPasswordResets);
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
        case C_PREF_SYSTEM_LOCK:           $strSystemLock = $strValue; break;
        case C_PREF_USER_REGISTRATION:     $strUserReg = $strValue; break;
        case C_PREF_USER_CHANGE_PASS:      $strChangePass = $strValue; break;
        case C_PREF_ALLOW_PASSWORD_RESETS: $strAllowPasswordResets = $strValue; break;
        case C_PREF_COOKIE_DAYS:           $intCookieDays = $strValue; break;
        case C_PREF_DATE_FORMAT:           $intDateFormat = $strValue; break;
        case C_PREF_TIME_FORMAT:           $intTimeFormat = $strValue; break;
        case C_PREF_RSS_ARTICLES_URL:      $strRSSArticlesURL = $strValue; break;
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
  
  $strSystemLock == "Y" ? $strSystemLockChecked = " checked=\"checked\"" : $strSystemLockChecked = "";
  $strUserReg == 1 ? $strUserRegChecked = " checked=\"checked\"" : $strUserRegChecked = "";
  $strChangePass == "Y" ? $strUserPassChecked = " checked=\"checked\"" : $strUserPassChecked = "";
  $strAllowPasswordResets == "Y" ? $strAllowPWResetChecked = " checked=\"checked\"" : $strAllowPWResetChecked = "";
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  // Main form
  $strHTML = <<<END
<p>Settings: <b>General</b> | <a href="{FN_ADM_CONTENT_SETTINGS}" title="Content Settings">Content</a> | <a href="{FN_ADM_FILES_SETTINGS}" title="File Settings">Files</a> | <a href="{FN_ADM_URL_SETTINGS}" title="URLs">URLs</a></p>
<h1>$strPageTitle</h1>
$strConfMsg
<form id="frmSystemPrefs" action="{FN_ADM_GENERAL_SETTINGS}" method="post">
<table class="DefaultTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour MediumCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Site Settings</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteTitle">Site Title</label></b>
      <br />Enter the name of your site.
    </td>
    <td>
      $strMissingTitleText
      <input id="txtSiteTitle" name="txtSiteTitle" type="text" size="40" maxlength="100" value="$strSiteTitle" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteDescription">Site Description</label></b>
      <br />Enter a description of your site.
    </td>
    <td>
      $strMissingDescText
      <textarea id="txtSiteDescription" name="txtSiteDescription" rows="5" cols="40">$strSiteDesc</textarea>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteKeywords">Site Keywords</label></b>
      <br />These are the words or phrases that people will search for in order to locate your site. Separate each keyword with a comma. e.g. injader, content management, blogging
    </td>
    <td>
      <textarea id="txtSiteKeywords" name="txtSiteKeywords" rows="5" cols="40">$strSiteKeywords</textarea>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteHeader">Site Header</label></b>
      <br />HTML that will be included in the &lt;head&gt; tag of your site.
    </td>
    <td>
      <textarea id="txtSiteHeader" name="txtSiteHeader" rows="5" cols="40">$strSiteHeader</textarea>
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteEmail">Site Email</label></b>
      <br />The contact email address for your web site.
    </td>
    <td>
      $strMissingEmailText
      <input id="txtSiteEmail" name="txtSiteEmail" type="text" size="40" maxlength="100" value="$strSiteEmail" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtSiteFavicon">Favicon</label></b>
      <br />A direct link to your favicon, if you wish to use one.
    </td>
    <td>
      <input id="txtSiteFavicon" name="txtSiteFavicon" type="text" size="40" maxlength="100" value="$strSiteFavicon" />
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Feed Settings</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtRSSArticlesURL">Feedburner Site Feed</label></b>
      <br />If you use <a href="http://www.feedburner.com">Feedburner</a> for your site feed, enter the URL here.
    </td>
    <td>
      <input id="txtRSSArticlesURL" name="txtRSSArticlesURL" type="text" size="50" maxlength="100" value="$strRSSArticlesURL" />
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">System Settings</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtSystemPageCount">System Page Count</label></b>
      <br />The number of items per page within the Control Panel.
    </td>
    <td>
      <input id="txtSystemPageCount" name="txtSystemPageCount" type="text" size="4" maxlength="4" value="$intSystemPageCount" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtMaxLogEntries">Maximum Log Entries</label></b>
      <br />The maximum number of rows that will be saved in the Access Log.
    </td>
    <td>
      <input id="txtMaxLogEntries" name="txtMaxLogEntries" type="text" size="5" maxlength="5" value="$intMaxLogEntries" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="chkSystemLock">System Lock</label></b>
      <br />Block everyone except admins from accessing the system.
    </td>
    <td>
      <input id="chkSystemLock" name="chkSystemLock" type="checkbox"$strSystemLockChecked />
    </td>
  </tr>
  <tr id="mRow_DateAndTime">
    <th class="HeadColour SpanCell Left" colspan="2">Date and Time</th>
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
      <br />Use positive/negative numbers to change the time. Existing content will be unaffected.
      <br /><i>Current server time: $dteCurrentServerTime
      <br />Time with current offset: $dteOffsetServerTime</i>
    </td>
    <td>
      $strMissingServerTimeText
      <input id="txtServerTimeOffset" name="txtServerTimeOffset" type="text" size="3" maxlength="3" value="$intServerTimeOffset" />
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">User Settings</th>
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
      <b><label for="chkUserChangePass">Allow password changes</label></b>
    </td>
    <td>
      <input id="chkUserChangePass" name="chkUserChangePass" type="checkbox"$strUserPassChecked />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="chkAllowPasswordResets">Allow password resets</label></b>
    </td>
    <td>
      <input id="chkAllowPasswordResets" name="chkAllowPasswordResets" type="checkbox"$strAllowPWResetChecked />
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
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</form>

END;
  $CMS->AP->Display($strHTML);
?>