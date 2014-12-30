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

  class PermissionProfile extends Helper {
    var $arrSystemProfile;
    // ** Content Area ** //
    function Create($strProfileName, $strViewArea, $strCreateArticle, $strPublishArticle, $strEditArticle, $strDeleteArticle, $strAddComment, $strEditComment, $strDeleteComment, $strLockArticle, $strAttachFile) {
      global $CMS;
      $intID = $this->Query("INSERT INTO {IFW_TBL_PERMISSION_PROFILES}(name, is_system, view_area, create_article, publish_article, edit_article, delete_article, add_comment, edit_comment, delete_comment, lock_article, attach_file) VALUES ('$strProfileName', 'N', '$strViewArea', '$strCreateArticle', '$strPublishArticle', '$strEditArticle', '$strDeleteArticle', '$strAddComment', '$strEditComment', '$strDeleteComment', '$strLockArticle', '$strAttachFile')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_PPCA_CREATE, $intID, "");
    }
    function Edit($intProfileID, $strProfileName, $strViewArea, $strCreateArticle, $strPublishArticle, $strEditArticle, $strDeleteArticle, $strAddComment, $strEditComment, $strDeleteComment, $strLockArticle, $strAttachFile) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_PERMISSION_PROFILES} SET name = '$strProfileName', view_area = '$strViewArea', create_article = '$strCreateArticle', publish_article = '$strPublishArticle', edit_article = '$strEditArticle', delete_article = '$strDeleteArticle', add_comment = '$strAddComment', edit_comment = '$strEditComment', delete_comment = '$strDeleteComment', lock_article = '$strLockArticle', attach_file = '$strAttachFile' WHERE id = $intProfileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_PPCA_EDIT, $intProfileID, "");
    }
    function Delete($intProfileID) {
      global $CMS;
      $this->Query("DELETE FROM {IFW_TBL_PERMISSION_PROFILES} WHERE id = $intProfileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_PPCA_DELETE, $intProfileID, "");
    }
    // ** Select All ** //
    function GetAll() {
      $arrPermissions = $this->ResultQuery("SELECT * FROM {IFW_TBL_PERMISSION_PROFILES} ORDER BY is_system DESC, name ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrPermissions;
    }
    // ** Select One ** //
    function Get($intID) {
      $arrPermissions = $this->ResultQuery("SELECT * FROM {IFW_TBL_PERMISSION_PROFILES} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrPermissions[0];
    }
    // ** View Area ** //
    function GetViewArea($intID) {
      $strGroups = "";
      if ($intID) {
        $arrPermissions = $this->ResultQuery("SELECT view_area FROM {IFW_TBL_PERMISSION_PROFILES} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $strGroups = $arrPermissions[0]['view_area'];
      } else {
        if (empty($this->arrSystemProfile['view_area'])) {
          $arrPermissions = $this->ResultQuery("SELECT view_area FROM {IFW_TBL_PERMISSION_PROFILES} WHERE is_system = 'Y'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
          $this->arrSystemProfile['view_area'] = $arrPermissions[0]['view_area'];
        }
        $strGroups = $this->arrSystemProfile['view_area'];
      }
      return $strGroups;
    }
    // ** Usage ** //
    function IsProfileUsed($intProfileID) {
      global $CMS;
      $arrUsage = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_AREAS} WHERE permission_profile_id = $intProfileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUsage[0]['count'] > 0 ? true : false;
    }
  }
?>