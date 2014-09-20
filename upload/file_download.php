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

  require 'sys/header.php';
  $intFileID = empty($_GET['id']) ? "" : $CMS->FilterNumeric($_GET['id']);
  if (!$intFileID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
  }
  $arrFile = $CMS->FL->GetFile($intFileID);
  if (count($arrFile) == 0) {
    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, $intFileID);
  }
  $strTitle       = $arrFile['title'];
  $strSEOTitle    = $arrFile['seo_title'];
  $strIsAvatar    = $arrFile['is_avatar'];
  $strIsSiteImage = $arrFile['is_siteimage'];
  $intArticleID   = $arrFile['article_id'];
  $intAreaID      = $arrFile['content_area_id']; // Article area ID
  if (($strIsAvatar == "N") && ($strIsSiteImage == "N")) {
    $CMS->RES->ViewArea($intAreaID);
    if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "ViewArea");
    }
  }
  // Default location
  $strDefaultLoc = $arrFile['location'];
  // Thumbnails
  if (empty($_GET['s'])) {
    $blnThumb = false;
    $strLocation = $arrFile['location'];
  } else {
    $blnThumb = true;
    $strSize = $_GET['s'];
    if ($strSize == "s") {
      if ($arrFile['thumb_small']) {
        $strLocation = $arrFile['thumb_small'];
      } else {
        $strLocation = $strDefaultLoc;
      }
    } elseif ($strSize == "m") {
      if ($arrFile['thumb_medium']) {
        $strLocation = $arrFile['thumb_medium'];
      } else {
        $strLocation = $strDefaultLoc;
      }
    } elseif ($strSize == "l") {
      if ($arrFile['thumb_large']) {
        $strLocation = $arrFile['thumb_large'];
      } else {
        $strLocation = $strDefaultLoc;
      }
    } else {
      $blnThumb = false;
      $strLocation = $arrFile['location'];
    }
  }
  // Load file
  $strFileName = $CMS->GetFileNameFromPath($strLocation);
  $strFileType = strtoupper($CMS->GetExtensionFromPath($strLocation));
  // Append site URL unless it already has an absolute URL
  // (Absolute URLs are used by the WP migration script)
  if (strpos($strLocation, "http://") === false) {
    $strLocation = ABS_ROOT.$strLocation;
    // Check if file exists
    if (file_exists($strLocation) == false) {
      $CMS->Err_MFail(M_ERR_FILE_NOT_FOUND, $strLocation);
    }
  }
  // Log activity
  if (($strIsAvatar != "Y") && ($strIsSiteImage != "Y") && (!$blnThumb)) {
    $intCurrentUserID = $CMS->RES->GetCurrentUserID();
    $CMS->PL->SetTitle($strSEOTitle);
    $CMS->SYS->CreateAccessLog("Downloaded file: $strTitle", AL_TAG_FILE_DOWNLOAD, $intCurrentUserID, "");
    // Push up those hits
    $CMS->FL->IncrementHits($intFileID);
  }

  // Stream file (music only)
  if (!empty($_GET['stream'])) {
    if ($_GET['stream'] == "yes") {
      httpRedirect($strLocation);
      exit;
    }
  }

  // Required for IE, otherwise Content-disposition is ignored
  if (ini_get('zlib.output_compression')) {
    ini_set('zlib.output_compression', 'Off');
  }
  // Download the file
  switch($strFileType) {
    case "MPG":
    case "MPEG":
    case "MP2":
    case "MP3": $strContentType = "audio/mpeg"; break;
    case "PDF": $strContentType = "application/pdf"; break;
    case "EXE": $strContentType = "application/octet-stream"; break;
    case "ZIP": $strContentType = "application/zip"; break;
    case "DOC": $strContentType = "application/msword"; break;
    case "XLS": $strContentType = "application/vnd.ms-excel"; break;
    case "PPT": $strContentType = "application/vnd.ms-powerpoint"; break;
    case "GIF": $strContentType = "image/gif"; break;
    case "PNG": $strContentType = "image/png"; break;
    case "JPEG":
    case "JPG": $strContentType = "image/jpg"; break;
    default: $strContentType = "application/force-download"; break;
  }
  header("Pragma: public"); // required
  header("Expires: 0");
  header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
  header("Cache-Control: private", false); // required for certain browsers
  header("Content-Type: $strContentType");
  header("Content-Transfer-Encoding: binary");
  // It's probably safer to check the real file size
  // instead of using the value in the database.
  // The DB field is for display purposes.
  header("Content-Length: ".basename(filesize($strLocation)));
  header("Content-Disposition: attachment; filename=\"$strFileName\"");
  readfile($strLocation);
  // Note: sometimes readfile does not read beyond 2,000,000 bytes.
  // If that's the case, we need a flag to try file_get_contents instead.
  // readfile seems to be ok at the moment, but I'll leave this here
  // for information.
  //
  //echo(file_get_contents($strLocation));
  exit;
?>