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
  $CMS->RES->ValidateLoggedIn();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_NOT_LOGGED_IN, "");
  }
  $strPageTitle = "Upload Avatar";
  $intUserID = $CMS->RES->GetCurrentUserID();
  
  $intAvatarsPerUser = $CMS->SYS->GetSysPref(C_PREF_AVATARS_PER_USER);
  $arrCurrentAvatars = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_UPLOADS} WHERE author_id = $intUserID AND is_avatar = 'Y'", basename(__FILE__), __LINE__);
  $intCurrentAvatars = $arrCurrentAvatars[0]['count'];
  if ($intCurrentAvatars >= $intAvatarsPerUser) {
    $CMS->Err_MFail(M_ERR_AVATAR_LIMIT, "");
  }
  
  $intAvatarSize = $CMS->SYS->GetSysPref(C_PREF_AVATAR_SIZE);
  $intMaxFileSize = $CMS->SYS->GetSysPref(C_PREF_AVATAR_MAX_SIZE);
  $strFileSizeMB = ($intMaxFileSize / 1000000)."MB";
  $strFileSizeKB = ($intMaxFileSize / 1000)."KB";

  $strFileError = "";

  if ($_POST) {

    $blnSubmitForm = true;

    // ** FILE UPLOAD CHECKING ** //
    
    // Process file errors
    if (($_FILES['txtFile']['error']) && ($_FILES['txtFile']['name'])) {
      $blnSubmitForm = false;
      $strFileSubmitError = $CMS->FL->SubmissionError($_FILES['txtFile']['error']);
      $strFileError = $CMS->AC->InvalidFormData($strFileSubmitError);
    }

    $FU = new FileUpload;
    if ($_FILES['txtFile']['name']) {
      $FU->Setup($_FILES['txtFile']['name'], "Avatar", "");
    } else {
      $blnSubmitForm = false;
      $strFileError = $CMS->AC->InvalidFormData("");
    }

    // Check file size
    if ($_FILES['txtFile']['size'] > $intMaxFileSize) {
      $blnSubmitForm = false;
      $strFileError = $CMS->Err_MWarn(M_ERR_UPLOAD_FILESIZE, $FU->GetDBFilePath());
    }

    // Prevent two uploads referencing the same file
    if ($_FILES['txtFile']['name']) {
      if ($CMS->FL->IsDuplicateFile($FU->GetDBFilePath(), "")) {
        $blnSubmitForm = false;
        $strFileError = $CMS->Err_MWarn(M_ERR_UPLOAD_DUPLICATE, $FU->GetDBFilePath());
      }
      // Is this a valid image?
      $strExtension = $CMS->GetExtensionFromPath($FU->GetDBFilePath());
      if ((strtoupper($strExtension) != "JPG") && (strtoupper($strExtension) != "PNG")) {
        $blnSubmitForm = false;
        $strFileError = $CMS->Err_MWarn(M_ERR_UPLOAD_NOT_IMAGE, $FU->GetDBFilePath());
      }
    }
    
    if ($blnSubmitForm) {
      // Upload file
      $FU->Submit($_FILES['txtFile']['tmp_name']);
      if ($FU->IsError()) {
        $CMS->Err_MFail($FU->GetErrorDesc(), $FU->GetErrorInfo());
      }
      // Make thumbnails
      $FU->DoThumbs("");
      $strWarnings = $FU->GetWarnings();
      // Write to DB
      $dteCreated = $CMS->SYS->GetCurrentDateAndTime();
      $intFileID = $CMS->FL->Create($FU->GetDBAvatarThumb(), $intUserID, $dteCreated, "Avatar", "", "Y", "N", "", "");
      // Set avatar
      $CMS->Query("UPDATE {IFW_TBL_USERS} SET avatar_id = $intFileID WHERE id = $intUserID", basename(__FILE__), __LINE__);
      $CMS->SYS->CreateAccessLog("Set avatar (File ID: $intFileID)", AL_TAG_AVATAR_SET, $intUserID);
      // Confirmation page
      $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n\n<p>The file was uploaded and set as your avatar. <a href=\"{FN_ADM_MANAGE_AVATARS}\">Manage Avatars</a></p>";
      $CMS->AP->SetTitle($strPageTitle .= " - Results");
      $CMS->AP->Display($strHTML);
    }

  }

  // NO POST //

  $strSubmitButton = $CMS->AC->Submit(M_BTN_UPLOAD_AVATAR);
  $strCancelButton = $CMS->AC->CancelButton();

  $CMS->AP->SetTitle($strPageTitle);

  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<ul>
<li>Only JPG and PNG files are permitted.</li>
<li>Avatars will be resized if the height or width is greater than $intAvatarSize pixels.</li>
<li>Files greater than $strFileSizeMB / $strFileSizeKB will not upload. This is to reduce server load and processing time.</li>
</ul>
<form enctype="multipart/form-data" action="{FN_ADM_UPLOAD_AVATAR}" method="post">
<div class="table-responsive">
<table class="table table-striped" style="width: 400px;">
  <tr class="separator-row">
    <td>Select a file to upload:</td>
  </tr>
  <tr>
    <td>$strFileError
      <input type="hidden" name="MAX_FILE_SIZE" value="$intMaxFileSize" />
      <input type="file" name="txtFile" size="50" />
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</div>
</form>

END;

  $CMS->AP->Display($strHTML);
