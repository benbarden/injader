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
  $strPageTitle = "File Settings";
  
  $CMS->AP->SetTitle($strPageTitle);
  
  $blnSubmitForm = false;

  if ($_POST) {
    $intThumbSmall  = $_POST['txtThumbSmall'];
    $intThumbMedium = $_POST['txtThumbMedium'];
    $intThumbLarge  = $_POST['txtThumbLarge'];
    $strKeepAspect  = !empty($_POST['chkKeepAspect']) ? "Y" : "N";
    $intAttachMaxSize = $_POST['txtAttachMaxSize'];
    if (!$intAttachMaxSize) {
      $intAttachMaxSize = 0;
    }
    $intAttachMaxSizeMB = $intAttachMaxSize * 1000000;
    $intAvatarsPerUser = $_POST['txtAvatarsPerUser'];
    if (!$intAvatarsPerUser) {
      $intAvatarsPerUser = 0;
    }
    $intAvatarSize    = $_POST['txtAvatarSize'];
    $intAvatarMaxSize = $_POST['txtAvatarMaxSize'];
    if (!$intAvatarMaxSize) {
      $intAvatarMaxSize = 0;
    }
    $intAvatarMaxSizeMB = $intAvatarMaxSize * 1000000;
    $strDirAvatars    = $_POST['txtDirAvatars'];
    $strDirSiteImages = $_POST['txtDirSiteImages'];
    $strDirMisc       = $_POST['txtDirMisc'];
    // Validation
    $blnSubmitForm = true;
    // Update database
    if ($blnSubmitForm) {
      if ($CMS->SYS->GetSysPref(C_PREF_THUMB_SMALL) != $intThumbSmall) {
        $CMS->SYS->WriteSysPref(C_PREF_THUMB_SMALL, $intThumbSmall);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_THUMB_MEDIUM) != $intThumbMedium) {
        $CMS->SYS->WriteSysPref(C_PREF_THUMB_MEDIUM, $intThumbMedium);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_THUMB_LARGE) != $intThumbLarge) {
        $CMS->SYS->WriteSysPref(C_PREF_THUMB_LARGE, $intThumbLarge);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_THUMB_KEEPASPECT) != $strKeepAspect) {
        $CMS->SYS->WriteSysPref(C_PREF_THUMB_KEEPASPECT, $strKeepAspect);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_ATTACH_MAX_SIZE) != $intAttachMaxSizeMB) {
        $CMS->SYS->WriteSysPref(C_PREF_ATTACH_MAX_SIZE, $intAttachMaxSizeMB);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_AVATARS_PER_USER) != $intAvatarsPerUser) {
        $CMS->SYS->WriteSysPref(C_PREF_AVATARS_PER_USER, $intAvatarsPerUser);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_AVATAR_SIZE) != $intAvatarSize) {
        $CMS->SYS->WriteSysPref(C_PREF_AVATAR_SIZE, $intAvatarSize);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_AVATAR_MAX_SIZE) != $intAvatarMaxSizeMB) {
        $CMS->SYS->WriteSysPref(C_PREF_AVATAR_MAX_SIZE, $intAvatarMaxSizeMB);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_DIR_AVATARS) != $strDirAvatars) {
        $CMS->SYS->WriteSysPref(C_PREF_DIR_AVATARS, $strDirAvatars);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_DIR_SITE_IMAGES) != $strDirSiteImages) {
        $CMS->SYS->WriteSysPref(C_PREF_DIR_SITE_IMAGES, $strDirSiteImages);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_DIR_MISC) != $strDirMisc) {
        $CMS->SYS->WriteSysPref(C_PREF_DIR_MISC, $strDirMisc);
      }
      // Rebuild the cache
      $CMS->SYS->RebuildCache();
    }
  } else {
    if (!isset($CMS->SYS->arrSysPrefs[C_PREF_SITE_TITLE])) {
      $CMS->SYS->GetAllSysPrefs();
    }
    $arrSysPrefs = $CMS->SYS->arrSysPrefs;
    foreach ($arrSysPrefs as $strKey => $strValue) {
      switch ($strKey) {
        case C_PREF_THUMB_SMALL:      $intThumbSmall = $strValue; break;
        case C_PREF_THUMB_MEDIUM:     $intThumbMedium = $strValue; break;
        case C_PREF_THUMB_LARGE:      $intThumbLarge = $strValue; break;
        case C_PREF_THUMB_KEEPASPECT: $strKeepAspect = $strValue; break;
        case C_PREF_ATTACH_MAX_SIZE:  $intAttachMaxSize = ((integer) $strValue / 1000000); break;
        case C_PREF_AVATARS_PER_USER: $intAvatarsPerUser = $strValue; break;
        case C_PREF_AVATAR_SIZE:      $intAvatarSize = $strValue; break;
        case C_PREF_AVATAR_MAX_SIZE:  $intAvatarMaxSize = ((integer) $strValue / 1000000); break;
        case C_PREF_DIR_AVATARS:      $strDirAvatars = $strValue; break;
        case C_PREF_DIR_SITE_IMAGES:  $strDirSiteImages = $strValue; break;
        case C_PREF_DIR_MISC:         $strDirMisc = $strValue; break;
      }
    }
  }
  
  $strConfMsg = "";
  if ($blnSubmitForm) {
    $strConfMsg = "<p><b>Settings updated successfully.</b></p>";
  }

  $strKeepAspectChecked = $strKeepAspect == "Y" ? " checked=\"checked\"" : "";

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  // Main form
  $strHTML = <<<END
<p>Settings: <a href="{FN_ADM_GENERAL_SETTINGS}" title="General Settings">General</a> | <a href="{FN_ADM_CONTENT_SETTINGS}" title="Content Settings">Content</a> | <b>Files</b> | <a href="{FN_ADM_URL_SETTINGS}" title="URLs">URLs</a></p>
<h1>$strPageTitle</h1>
$strConfMsg
<form id="frmSystemPrefs" action="{FN_ADM_FILES_SETTINGS}" method="post">
<table class="DefaultTable FixedTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour HalfCell" />
    <col class="BaseColour HalfCell" />
  </colgroup> 
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Thumbnail Sizes</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtThumbSmall">Small</label></b>
    </td>
    <td>
      <input id="txtThumbSmall" name="txtThumbSmall" type="text" size="4" maxlength="4" value="$intThumbSmall" /> pixels
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtThumbMedium">Medium</label></b>
    </td>
    <td>
      <input id="txtThumbMedium" name="txtThumbMedium" type="text" size="4" maxlength="4" value="$intThumbMedium" /> pixels
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtThumbLarge">Large</label></b>
    </td>
    <td>
      <input id="txtThumbLarge" name="txtThumbLarge" type="text" size="4" maxlength="4" value="$intThumbLarge" /> pixels
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="chkKeepAspect">Keep aspect ratio</label></b>
    </td>
    <td>
      <input id="chkKeepAspect" name="chkKeepAspect" type="checkbox"$strKeepAspectChecked />
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Attachment Settings</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtAttachMaxSize">Attachment Limit (File size)</label></b>
    </td>
    <td>
      <input id="txtAttachMaxSize" name="txtAttachMaxSize" type="text" size="5" maxlength="5" value="$intAttachMaxSize" /> MB
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Avatar Settings</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtAvatarsPerUser">Avatars Per User</label></b>
      <br /><i>To disable avatars, set this to 0</i>
    </td>
    <td>
      <input id="txtAvatarsPerUser" name="txtAvatarsPerUser" type="text" size="2" maxlength="2" value="$intAvatarsPerUser" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtAvatarSize">Avatar Limit (Height/width)</label></b>
    </td>
    <td>
      <input id="txtAvatarSize" name="txtAvatarSize" type="text" size="4" maxlength="4" value="$intAvatarSize" /> pixels
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtAvatarMaxSize">Avatar Limit (File size)</label></b>
    </td>
    <td>
      <input id="txtAvatarMaxSize" name="txtAvatarMaxSize" type="text" size="5" maxlength="5" value="$intAvatarMaxSize" /> MB
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Directories - Must end with a /</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtDirAvatars">Avatar Directory</label></b>
    </td>
    <td>
      <input id="txtDirAvatars" name="txtDirAvatars" type="text" size="40" maxlength="100" value="$strDirAvatars" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtDirSiteImages">Site Image Directory</label></b>
    </td>
    <td>
      <input id="txtDirSiteImages" name="txtDirSiteImages" type="text" size="40" maxlength="100" value="$strDirSiteImages" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtDirMisc">Miscellaneous File Directory</label></b>
    </td>
    <td>
      <input id="txtDirMisc" name="txtDirMisc" type="text" size="40" maxlength="100" value="$strDirMisc" />
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