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
  $strAction = $_GET['action'];
  $blnCreate = false;
  $blnEdit = false;
  if ($strAction == "create") {
    $strPageTitle = "New Site File";
    $blnCreate = true;
  } elseif ($strAction == "edit") {
    $strPageTitle = "Edit Site File";
    $blnEdit = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }

  $intUserID = $CMS->RES->GetCurrentUserID();

  if ($blnEdit) {
    $intFileID = $CMS->FilterNumeric($_GET['id']);
    if (!$intFileID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    $arrFile = $CMS->FL->GetFile($intFileID);
  }

  $strTitle        = "";
  $strMissingTitle = "";
  $strFileLocation = "";
  $strFileError    = "";
  $strWarnings     = "";

  if ($_POST) {
    $blnSubmitForm = true;
    $strTitle = $_POST['txtTitle'];
    if (!$strTitle) {
      $blnSubmitForm = false;
      $strMissingTitle = $CMS->AC->InvalidFormData("");
    }
    if ((!$_POST['txtFileLocation']) && (!$_FILES['txtFile']['name'])) {
      $blnSubmitForm = false;
      $strFileError = $CMS->AC->InvalidFormData(M_ERR_UPLOAD_OR_URL);
    }
    $strTitle = $CMS->AddSlashesIFW($strTitle);
    
    
    // ** FILE UPLOAD CHECKING ** //
    
    // Process file errors
    if (($_FILES['txtFile']['error']) && ($_FILES['txtFile']['name'])) {
      $blnSubmitForm = false;
      $strFileSubmitError = $CMS->FL->SubmissionError($_FILES['txtFile']['error']);
      $strFileError = $CMS->AC->InvalidFormData($strFileSubmitError);
    }
    
    $FU = new FileUpload;
    if ($blnEdit) {
      if ($arrFile['is_siteimage'] == "Y") {
        $strMode = "Site";
      } elseif ($arrFile['is_avatar'] == "Y") {
        $strMode = "Avatar";
      } elseif ($arrFile['article_id'] > 0) {
        $strMode = "File";
      } else {
        // Catch-all
        $strMode = "Site";
      }
    } else {
      $strMode = "Site";
    }
    
    if ($_FILES['txtFile']['name']) {
      $FU->Setup($_FILES['txtFile']['name'], $strMode, "Upload");
    } elseif ($_POST['txtFileLocation']) {
      $FU->Setup($_POST['txtFileLocation'], $strMode, "Link");
      // Eliminate non-existent files
      if (!file_exists(ABS_ROOT.$_POST['txtFileLocation'])) {
        $blnSubmitForm = false;
        $strFileError = $CMS->Err_MWarn(M_ERR_UPLOAD_NOT_FOUND, ABS_ROOT.$_POST['txtFileLocation']);
      }
    }
    
    // Prevent two uploads referencing the same file
    if ($_FILES['txtFile']['name']) {
      $strCurrentFileID = $blnEdit ? $intFileID : "";
      if ($CMS->FL->IsDuplicateFile($FU->GetDBFilePath(), $strCurrentFileID)) {
        $blnSubmitForm = false;
        $strFileError = $CMS->Err_MWarn(M_ERR_UPLOAD_DUPLICATE, $FU->GetDBFilePath());
      } else {
          // Is this a valid file?
          $fileExtension = $CMS->GetExtensionFromPath($FU->GetDBFilePath());
          $fileExtension = strtoupper($fileExtension);
          $allowedTypesArray = explode(",", C_ALLOWED_FILE_TYPES);
          if (!in_array($fileExtension, $allowedTypesArray)) {
            $blnSubmitForm = false;
            $strFileError = $CMS->Err_MWarn("For security reasons, this file type is not permitted. [$fileExtension]", $FU->GetDBFilePath());
          }
      }
    }

    // ** SUBMIT FORM ** //
    if ($blnSubmitForm) {
    
      // If editing and a file is present, always delete previous version
      if (($blnEdit) && ($_FILES['txtFile']['name'])) {
        $strWarnings = $CMS->FL->UnlinkAll($intFileID);
      }

      // Upload file
      if ($FU->IsFileUpload()) {
        $FU->Submit($_FILES['txtFile']['tmp_name']);
        if ($FU->IsError()) {
          $CMS->Err_MFail($FU->GetErrorDesc(), $FU->GetErrorInfo());
        }
      }
      
      // Make thumbnails
      if ($blnCreate) {
        $FU->DoThumbs("");
      } elseif ($blnEdit) {
        $FU->DoThumbs($intFileID);
      }
      $strWarnings .= $FU->GetWarnings();
      
      // ** DATABASE WRITE ** //
      
      // Write to DB
      if ($blnCreate) {
        $dteFileCreated = $CMS->SYS->GetCurrentDateAndTime();
        $intFileID = $CMS->FL->Create($FU->GetDBFilePath(), $intUserID, $dteFileCreated, $strTitle, $FU->GetDBThumbSmall(), "N", "Y", $FU->GetDBThumbMedium(), $FU->GetDBThumbLarge());
        $strDidWhat = "uploaded";
        $strUploadAnother = "<br /><a href=\"{FN_ADM_FILES_SITE_UPLOAD}?action=create\">Upload another file</a> |";
      } elseif ($blnEdit) {
        $arrFile = $CMS->FL->GetFile($intFileID);
        $dteFileCreated = $arrFile['create_date_raw'];
        $CMS->FL->Edit($intFileID, $FU->GetDBFilePath(), $strTitle, $dteFileCreated, $FU->GetDBThumbSmall(), $FU->GetDBThumbMedium(), $FU->GetDBThumbLarge());
        $strDidWhat = "updated";
        $strUploadAnother = "";
      }
      // Confirmation page
      $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n\n$strWarnings<p>The file was successfully $strDidWhat.$strUploadAnother <a href=\"{FN_ADM_FILES}?type=site\">View Site Files</a></p>";
      $CMS->AP->SetTitle($strPageTitle." - Results");
      $CMS->AP->Display($strHTML);
    }
  }

  // NO POST //

  if ($blnEdit) {
    $strTitle        = $arrFile['title'];
    $strFileLocation = $arrFile['location'];
  }
  
  $strPostButtons   = $CMS->AC->PostButtons("txtDesc");
  $strSubmitButton  = $CMS->AC->SubmitButton();
  $strCancelButton  = $CMS->AC->CancelButton();
  if ($blnCreate) {
    $strFormTag = "<form enctype=\"multipart/form-data\" action=\"{FN_ADM_FILES_SITE_UPLOAD}?action=create\" method=\"post\">\n";
    $strFileUploadText = "File to upload:";
  } elseif ($blnEdit) {
    $strFormTag = "<form enctype=\"multipart/form-data\" action=\"{FN_ADM_FILES_SITE_UPLOAD}?action=edit&amp;id=$intFileID\" method=\"post\">\n";
    $strFileUploadText = "Replace file (optional):";
  }

  $CMS->AP->SetTitle($strPageTitle);

  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strFormTag
<div class="table-responsive">
<table class="table table-striped">
  <tr class="separator-row">
    <td colspan="2">Site File</td>
  </tr>
  <tr>
    <td><label for="txtTitle">Title:</label></td>
    <td>
      $strMissingTitle
      <input type="text" id="txtTitle" name="txtTitle" size="35" maxlength="100" value="$strTitle" />
    </td>
  </tr>
  <tr>
    <td><label for="txtFile">$strFileUploadText</label></td>
    <td>$strFileError
      <input type="file" id="txtFile" name="txtFile" size="50" />
    </td>
  </tr>
  <tr>
    <td><label for="txtFileLocation">Direct URL:</label></td>
    <td>
      <input type="text" id="txtFileLocation" name="txtFileLocation" size="35" value="$strFileLocation" />
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