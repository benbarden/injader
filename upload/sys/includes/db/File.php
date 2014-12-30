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

  class File extends Helper {
    var $blnDeleteAll = false;
    var $intArticleID;
    // ** Common ** //
    function F_Logger($strAction, $intFID, $strTitle, $intUserID, $intUserIP) {
      global $CMS;
      if (!$intUserIP) {
        $intUserIP = $_SERVER['REMOTE_ADDR'];
      }
      if (!$intUserID) {
        $intUserID = $CMS->RES->GetCurrentUserID();
      }
      switch ($strAction) {
        case "Create": $CMS->SYS->CreateAccessLog("Created file: $strTitle (ID: $intFID)", AL_TAG_FILE_CREATE, $intUserID, ""); break;
        case "Edit": $CMS->SYS->CreateAccessLog("Edited file: $strTitle (ID: $intFID)", AL_TAG_FILE_EDIT, $intUserID, ""); break;
        case "Delete": $CMS->SYS->CreateAccessLog("Deleted file: $strTitle (ID: $intFID)", AL_TAG_FILE_DELETE, $intUserID, $intUserIP); break;
      }
    }
    // Insert, Update, Delete //
    function Create($strLocation, $intAuthorID, $dteCreated, $strTitle, $strThumbSmall, $strAvatar, $strSiteImage, $strThumbMedium, $strThumbLarge) {
      global $CMS;
      $strSEOTitle = $this->MakeSEOTitle($strTitle);
      $CMS->PL->SetTitle($strSEOTitle);
      $strAbsLoc = ABS_ROOT.$strLocation;
      $strFileSize = basename(filesize($strAbsLoc));
      $intArticleID = !empty($this->intArticleID) ? $this->intArticleID : 0;
      $intFID = $this->Query("INSERT INTO {IFW_TBL_UPLOADS}(location, author_id, create_date, title, thumb_small, is_avatar, is_siteimage, thumb_medium, thumb_large, upload_size, seo_title, article_id) VALUES ('$strLocation', $intAuthorID, '$dteCreated', '$strTitle', '$strThumbSmall', '$strAvatar', '$strSiteImage', '$strThumbMedium', '$strThumbLarge', '$strFileSize', '$strSEOTitle', $intArticleID)", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $this->F_Logger("Create", $intFID, $strTitle, "", "");
      return $intFID;
    }
    function CreateAttachment($strLocation, $intAuthorID, $dteCreated, $strTitle, $strThumbSmall, $strThumbMedium, $strThumbLarge, $intArticleID) {
      $this->intArticleID = $intArticleID;
      $intFID = $this->Create($strLocation, $intAuthorID, $dteCreated, $strTitle, $strThumbSmall, "N", "N", $strThumbMedium, $strThumbLarge);
      return $intFID;
    }
    function Edit($intFID, $strDBFilePath, $strTitle, $dteCreateDate, $strThumbSmall, $strThumbMedium, $strThumbLarge) {
      global $CMS;
      $strSEOTitle = $this->MakeSEOTitle($strTitle);
      $CMS->PL->SetTitle($strSEOTitle);
      $strAbsLoc = ABS_ROOT.$strDBFilePath;
      $strFileSize = basename(filesize($strAbsLoc));
      $dteEditDate = $CMS->SYS->GetCurrentDateAndTime();
      $this->Query("UPDATE {IFW_TBL_UPLOADS} SET location = '$strDBFilePath', title = '$strTitle', create_date = '$dteCreateDate', edit_date = '$dteEditDate', thumb_small = '$strThumbSmall', thumb_medium = '$strThumbMedium', thumb_large = '$strThumbLarge', upload_size = '$strFileSize', seo_title = '$strSEOTitle' WHERE id = $intFID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $this->F_Logger("Edit", $intFID, $strTitle, "", "");
    }
    function EditAttachment($intFID, $strLocation, $intAuthorID, $dteCreated, $strTitle, $strThumbSmall, $strThumbMedium, $strThumbLarge, $intArticleID) {
      $this->intArticleID = $intArticleID;
      $this->Edit($intFID, $strLocation, $strTitle, $dteCreated, $strThumbSmall, $strThumbMedium, $strThumbLarge);
    }
    function Delete($intFileID, $intUserID, $intUserIP) {
      $arrFile = $this->ResultQuery("SELECT title FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strTitle = $arrFile[0]['title'];
      $this->UnlinkAll($intFileID);
      $this->Query("UPDATE {IFW_TBL_USERS} SET avatar_id = 0 WHERE avatar_id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $this->Query("DELETE FROM {IFW_TBL_COMMENTS} WHERE upload_id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $this->Query("DELETE FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if (!$this->blnDeleteAll) {
        $this->F_Logger("Delete", $intFileID, $strTitle, $intUserID, $intUserIP);
      }
    }
    function DeleteFromFileSystem($strLocation) {
      $strPath = ABS_ROOT.$strLocation;
      @ $fp = unlink($strPath);
      return $fp ? true : false;
    }
    function UnlinkAll($intFileID) {
      $strWarnings = "";
      $arrLocation = $this->ResultQuery("SELECT location, thumb_small, thumb_medium, thumb_large FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID");
      $i = 0;
      $arrFiles[$i++] = $arrLocation[0]['location'];
      $arrFiles[$i++] = $arrLocation[0]['thumb_small'];
      $arrFiles[$i++] = $arrLocation[0]['thumb_medium'];
      $arrFiles[$i++] = $arrLocation[0]['thumb_large'];
      for ($i=0; $i<count($arrFiles); $i++) {
        if ($arrFiles[$i]) {
          $blnFile = $this->DeleteFromFileSystem($arrFiles[$i]);
          if (!$blnFile) {
            if (file_exists($arrFiles[$i]) == true) {
              $strErr = $this->Err_MWarn(M_ERR_UPLOAD_NOT_DELETED_ACCESS, $arrFiles[$i]);
            } else {
              $strErr = $this->Err_MWarn(M_ERR_UPLOAD_NOT_DELETED_MISSING, $arrFiles[$i]);
              $strWarnings .= $strErr;
            }
          }
        }
      }
      return $strWarnings;
    }
    function IncrementHits($intFileID) {
      $arrResult = $this->ResultQuery("SELECT hits FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $intHits = $arrResult[0]['hits'];
      $intHits++;
      $this->Query("UPDATE {IFW_TBL_UPLOADS} SET hits = $intHits WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function IsUserBelowUploadLimit($intAreaID, $intUserID) {
      $arrLimit = $this->ResultQuery("SELECT max_files_per_user AS max_files FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $arrCount = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_UPLOADS} u WHERE file_area_id = $intAreaID AND author_id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return ($arrLimit[0]['max_files'] > $arrCount[0]['count']) ? true : false;
    }
    // Select //
    function GetFile($intFileID) {
      global $CMS;
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $arrFile = $this->ResultQuery("SELECT up.id, up.author_id, us.username, DATE_FORMAT(up.create_date, '$strDateFormat') AS create_date, up.create_date AS create_date_raw, DATE_FORMAT(up.edit_date, '$strDateFormat') AS edit_date, up.edit_date AS edit_date_raw, up.hits, up.title, up.delete_flag, up.is_avatar, up.is_siteimage, up.location, up.thumb_small, up.thumb_medium, up.thumb_large, up.upload_size, up.seo_title, up.article_id, con.user_groups, con.content_area_id FROM {IFW_TBL_UPLOADS} up LEFT JOIN {IFW_TBL_CONTENT} con ON up.article_id = con.id LEFT JOIN {IFW_TBL_USERS} us ON up.author_id = us.id WHERE up.id = $intFileID ORDER BY up.id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0];
    }
    function GetFileAreaUploads($intAreaID, $strSortRule, $intStart, $intContentPerPage) {
      global $CMS;
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $arrAreaUploads = $this->ResultQuery("SELECT up.id, up.location, author_id, us.username, us.seo_username, hits, title, up.description, DATE_FORMAT(create_date, '$strDateFormat') AS create_date, create_date AS create_date_raw, DATE_FORMAT(edit_date, '$strDateFormat') AS edit_date, edit_date AS edit_date_raw, delete_flag, up.user_groups, up.thumb_small, up.thumb_medium, up.thumb_large, up.upload_size, up.seo_title FROM ({IFW_TBL_UPLOADS} up, {IFW_TBL_AREAS} a) LEFT JOIN {IFW_TBL_USERS} us ON up.author_id = us.id WHERE a.id = $intAreaID AND up.file_area_id = a.id $strSortRule LIMIT $intStart, $intContentPerPage", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreaUploads;
    }
    function CountAreaUploads($intAreaID) {
      $arrNumUploads = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_UPLOADS} WHERE file_area_id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrNumUploads[0]['count'];
    }
    // ** Single-field selects ** //
    function GetSEOTitle($intFileID) {
      $arrFile = $this->ResultQuery("SELECT seo_title FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0]['seo_title'];
    }
    function GetFileLocation($intFileID) {
      $arrFile = $this->ResultQuery("SELECT location FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0]['location'];
    }
    function GetFileThumb($intFileID, $strThumbSize) {
      if ($strThumbSize == "small") {
        $strField = "thumb_small";
      } elseif ($strThumbSize == "medium") {
        $strField = "thumb_medium";
      } elseif ($strThumbSize == "large") {
        $strField = "thumb_large";
      } else {
        $strField = "thumb_small";
      }
      $arrThumb = $this->ResultQuery("SELECT $strField FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrThumb[0]["$strField"];
    }
    function GetFileAreaID($intFileID) {
      $arrArea = $this->ResultQuery("SELECT file_area_id FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrArea[0]['file_area_id'];
    }
    function GetAttachedFiles($intArticleID) {
      $arrFiles = $this->ResultQuery("SELECT * FROM {IFW_TBL_UPLOADS} WHERE article_id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFiles;
    }
    function SubmissionError($intFileSubmitError) {
      if (($intFileSubmitError == 1) || ($intFileSubmitError == 2)) {
        $strFileSubmitError = M_ERR_UPLOAD_FILESIZE;
      } elseif ($intFileSubmitError == 3) {
        $strFileSubmitError = M_ERR_UPLOAD_PARTIAL;
      } elseif ($intFileSubmitError == 4) {
        $strFileSubmitError = M_ERR_UPLOAD_NONE;
      }
      return $strFileSubmitError;
    }
    function GetTitle($intFileID) {
      $arrFile = $this->ResultQuery("SELECT title FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0]['title'];
    }
    // ** Count deleted content ** //
    function CountDeletedFiles() {
      $arrFiles = $this->ResultQuery("SELECT count(*) AS upload_count FROM {IFW_TBL_UPLOADS} u WHERE delete_flag = 'Y'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFiles[0]['upload_count'];
    }
    // ** Checking ** //
    function IsDuplicateFile($strLocation, $intFileID) {
      $strExtraTest = $intFileID ? "AND id <> $intFileID" : "";
      $arrFile = $this->ResultQuery("SELECT location FROM {IFW_TBL_UPLOADS} WHERE location = '$strLocation' $strExtraTest", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0]['location'] ? true : false;
    }
    function IsFileAuthor($intFileID, $intAuthorID) {
      $arrFile = $this->ResultQuery("SELECT author_id FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0]['author_id'] == $intAuthorID ? true : false;
    }
    function IsAvatar($intFileID) {
      $arrFile = $this->ResultQuery("SELECT is_avatar FROM {IFW_TBL_UPLOADS} WHERE id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrFile[0]['is_avatar'] == "Y" ? true : false;
    }
    // ** Used in User Stats ** //
    function CountUserFiles($intUserID) {
      global $CMS;
      $arrUserContent = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_UPLOADS} WHERE author_id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUserContent[0]['count'];
    }
  }
?>