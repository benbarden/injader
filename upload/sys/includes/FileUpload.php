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

  class FileUpload extends Helper {
    // Error handling
    var $blnIsError;
    var $strErrorDesc;
    var $strErrorInfo;
    var $strWarnings;
    // Mode/Action
    var $blnFileUpload;
    var $blnFileLink;
    var $blnAvatars;
    var $blnSiteImages;
    // Paths
    var $strSiteDir;
    var $strFileFullPath;
    var $strFullPath;
    var $strDBFilePath;
    // Filenames
    var $strFName;
    var $strDBAvatarThumb;
    var $strDBThumbSmall;
    var $strDBThumbMedium;
    var $strDBThumbLarge;
    // Error handling
    function IsError() {
      return $this->blnIsError ? true : false;
    }
    function GetErrorDesc() {
      return $this->strErrorDesc;
    }
    function GetErrorInfo() {
      return $this->strErrorInfo;
    }
    function GetWarnings() {
      return $this->strWarnings;
    }
    // Mode/Action
    function IsFileUpload() {
      return $this->blnFileUpload ? true : false;
    }
    function IsFileLink() {
      return $this->blnFileLink ? true : false;
    }
    function IsAvatars() {
      return $this->blnAvatars ? true : false;
    }
    function IsSiteImages() {
      return $this->blnSiteImages ? true : false;
    }
    // Paths
    function GetSiteDir() {
      return $this->strSiteDir;
    }
    function GetFileFullPath() {
      return $this->strFileFullPath;
    }
    function GetFullPath() {
      return $this->strFullPath;
    }
    function GetDBFilePath() {
      return $this->strDBFilePath;
    }
    // Filenames
    function GetFName() {
      return $this->strFName;
    }
    function GetDBAvatarThumb() {
      return $this->strDBAvatarThumb;
    }
    function GetDBThumbSmall() {
      return $this->strDBThumbSmall;
    }
    function GetDBThumbMedium() {
      return $this->strDBThumbMedium;
    }
    function GetDBThumbLarge() {
      return $this->strDBThumbLarge;
    }
    // ** Useful stuff ** //
    function ClearErrors() {
      $this->blnIsError = false;
      $this->strErrorDesc = "";
      $this->strErrorInfo = "";
    }
    /**
      * Prepare for the file upload or URL change.
      * $strFileInfo - the path and filename. (Path is stripped out later)
      * $strMode     - what type of file we're working with. File, Upload, or Avatar
      * $strAction   - what we're doing. Upload, or Link
      */
    function Setup($strFileInfo, $strMode, $strAction) {
      global $CMS;
      // Set file directories
      $this->strFName = $strFileInfo;
      if ($strMode == "File") {
        $this->strSiteDir = $CMS->SYS->GetSysPref(C_PREF_DIR_MISC);
        if ($strAction == "Upload") { // Upload or replace file
          $this->blnFileUpload = true;
          $this->blnFileLink = false;
        } elseif ($strAction == "Link") { // Link to existing file
          $this->blnFileUpload = false;
          $this->blnFileLink = true;
          if (strpos($strFileInfo, "http://") === false) {
            $this->strFName = str_replace($this->strSiteDir, "", $this->strFName);
          }
        }
      } elseif ($strMode == "Avatar") {
        $this->strSiteDir = $CMS->SYS->GetSysPref(C_PREF_DIR_AVATARS);
        $this->blnAvatars = true;
      } elseif ($strMode == "Site") {
        $this->strSiteDir = $CMS->SYS->GetSysPref(C_PREF_DIR_SITE_IMAGES);
        if ($strAction == "Upload") { // Upload or replace file
          $this->blnFileUpload = true;
          $this->blnFileLink = false;
        } elseif ($strAction == "Link") { // Link to existing file
          $this->blnFileUpload = false;
          $this->blnFileLink = true;
          $this->strFName = str_replace($this->strSiteDir, "", $this->strFName);
        }
      }
      // Validate external URLs
      if (strpos($this->strFName, "http://") === false) {
        $this->strFullPath     = ABS_ROOT.$this->strSiteDir;
        $strFileFullPath       = str_replace(" ", "_", $this->strFName);
        $strFileFullPath       = $CMS->FilterAlphanumeric($strFileFullPath, "_.-");
        $this->strFileFullPath = ABS_ROOT.$this->strSiteDir.$strFileFullPath;
        $strDBFilePath         = str_replace(" ", "_", $this->strFName);
        $strDBFilePath         = $CMS->FilterAlphanumeric($strDBFilePath, "_.-");
        $this->strDBFilePath   = $this->strSiteDir.$strDBFilePath;
      } else {
        $this->strFullPath     = "";
        $this->strFileFullPath = $this->strFName;
        $this->strDBFilePath   = $this->strFName;
      }
    }
    // ** Upload file ** //
    function Submit($strTempFile) {
      $this->ClearErrors();
      @ $blnUploadedFile = is_uploaded_file($strTempFile);
      if ($blnUploadedFile) {
        @ $blnMovedFile = move_uploaded_file($strTempFile, $this->strFileFullPath);
        if (!$blnMovedFile) {
          $this->blnIsError = true;
          $this->strErrorDesc = M_ERR_UPLOAD_MOVE_ERROR;
          $this->strErrorInfo = "";
        }
      } else {
        $this->blnIsError = true;
        $this->strErrorDesc = M_ERR_UPLOAD_SECURITY;
        $this->strErrorInfo = $this->strFName;
      }
    }
    // ** Make thumbnails ** //
    function DoThumbs($intFileID) {
      global $CMS;
      if ($this->IsFileLink()) {
        $strExtensionUpper = strtoupper($this->GetExtensionFromPath($this->strDBFilePath));
        if (($strExtensionUpper == "PNG") || ($strExtensionUpper == "JPG")) {
          if (!$intFileID) {
            $blnMakeThumbnails = true;
          } else {
            if ($this->strDBFilePath == $CMS->FL->GetFileLocation($intFileID)) {
              $this->strDBThumbSmall  = $CMS->FL->GetFileThumb($intFileID, "small");
              $this->strDBThumbMedium = $CMS->FL->GetFileThumb($intFileID, "medium");
              $this->strDBThumbLarge  = $CMS->FL->GetFileThumb($intFileID, "large");
              $blnMakeThumbnails = false;
            } else {
              $this->strWarnings = $CMS->FL->UnlinkAll($intFileID);
              rename($this->strFullPath.$this->strFName, $this->strFileFullPath);
              $blnMakeThumbnails = true;
            }
          }
        } else {
          $blnMakeThumbnails = false;
        }
      } elseif ($this->IsFileUpload()) {
        $blnMakeThumbnails = true;
      } elseif ($this->IsAvatars()) {
        $blnMakeThumbnails = true;
      } elseif ($this->IsSiteImages()) {
        $blnMakeThumbnails = true;
      }
      if ($blnMakeThumbnails) {
        $this->MakeThumbs();
      }
    }
    function MakeThumbs() {
      global $CMS;
      $TH = new Thumb;
      if ($this->IsAvatars()) {
        // ** This is the output file ** //
        $strThumbNoExt  = $this->GetFileNameWithoutExtension($this->strFileFullPath);
        $strExtension   = $this->GetExtensionFromPath($this->strFileFullPath);
        $intAvatarSize  = $CMS->SYS->GetSysPref(C_PREF_AVATAR_SIZE);
        $strThumbAvatar = $strThumbNoExt."_t1.".$strExtension;;
        // ** We only need relative paths for the database ** //
        $this->strDBAvatarThumb = $this->strSiteDir.$this->GetFileNameFromPath($strThumbAvatar);
        if (!$TH->Make($this->strFileFullPath, $strThumbAvatar, $intAvatarSize)) {
          $this->strDBAvatarThumb = "";
        }
        @ $blnFile = unlink($this->GetFileFullPath());
        if (!$blnFile) {
          if (file_exists($this->strFileFullPath) == true) {
            $this->strWarnings .= M_ERR_UPLOAD_NOT_DELETED_ACCESS."(".$this->strFileFullPath.")";
          } else {
            $this->strWarnings .= M_ERR_UPLOAD_NOT_DELETED_MISSING."(".$this->strFileFullPath.")";
          }
        }
      } else {
        // ** These are the output files ** //
        $strThumbNoExt = $this->GetFileNameWithoutExtension($this->strFileFullPath);
        $strExtension  = $this->GetExtensionFromPath($this->strFileFullPath);
        $intThumbSmall  = $CMS->SYS->GetSysPref(C_PREF_THUMB_SMALL);
        $intThumbMedium = $CMS->SYS->GetSysPref(C_PREF_THUMB_MEDIUM);
        $intThumbLarge  = $CMS->SYS->GetSysPref(C_PREF_THUMB_LARGE);
        $strThumbSmall  = $strThumbNoExt."_t1.".$strExtension;;
        $strThumbMedium = $strThumbNoExt."_t2.".$strExtension;;
        $strThumbLarge  = $strThumbNoExt."_t3.".$strExtension;;
        // ** We only need relative paths for the database ** //
        $this->strDBThumbSmall  = $this->strSiteDir.$this->GetFileNameFromPath($strThumbSmall);
        $this->strDBThumbMedium = $this->strSiteDir.$this->GetFileNameFromPath($strThumbMedium);
        $this->strDBThumbLarge  = $this->strSiteDir.$this->GetFileNameFromPath($strThumbLarge);
        if (!$TH->Make($this->strFileFullPath, $strThumbSmall, $intThumbSmall)) {
          $this->strDBThumbSmall = "";
        }
        if (!$TH->Make($this->strFileFullPath, $strThumbMedium, $intThumbMedium)) {
          $this->strDBThumbMedium = "";
        }
        if (!$TH->Make($this->strFileFullPath, $strThumbLarge, $intThumbLarge)) {
          $this->strDBThumbLarge = "";
        }
      }
    }
  }

?>