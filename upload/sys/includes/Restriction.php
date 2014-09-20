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

  // Used to control who has access to do what.
  class Restriction extends Helper {
    var $strOutput;
    var $strUserGroups;
    var $strAllowedGroups;
    var $strCurrentUser;
    var $intCurrentUserID;
    var $strAuthorName;
    var $intAuthorID;
    var $strAJAXSID;
    var $strAJAXIP;
    // Caching //
    var $strCookie;
    var $arrSessionData;
    var $arrAreaProfileIDs;
    var $arrAreaAllowedGroups;
    var $arrSystemGroups;
    // Core Functions //
    function GetCurrentUser() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->strCurrentUser)) {
        $this->SetCurrentUser();
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->strCurrentUser;
    }
    function SetCurrentUser() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->strCurrentUser)) {
        $strSessionID = $this->GetSessionIDCookie();
        if (!$strSessionID) {
          $strSessionID = $this->GetAJAXSID();
        }
        $arrUser = $this->ResultQuery("SELECT u.username FROM ({IFW_TBL_USER_SESSIONS} us, {IFW_TBL_USERS} u) WHERE u.id = us.user_id AND session_id = '$strSessionID'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $this->strCurrentUser = $arrUser[0]['username'];
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function GetCurrentUserID() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->intCurrentUserID)) {
        $this->SetCurrentUserID();
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->intCurrentUserID;
    }
    function SetCurrentUserID() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->intCurrentUserID)) {
        $strSessionID = $this->GetSessionIDCookie();
        if (!$strSessionID) {
          $strSessionID = $this->GetAJAXSID();
        }
        $arrSID = $this->ResultQuery("SELECT user_id FROM {IFW_TBL_USER_SESSIONS} WHERE session_id = '$strSessionID'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $this->intCurrentUserID = empty($arrSID[0]['user_id']) ? 0 : $arrSID[0]['user_id'];
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function GetArticleAuthor($intArticleID) {
      global $CMS;
      return $CMS->ART->GetAuthor($intArticleID);
    }
    function GetCommentAuthor($intCommentID) {
      global $CMS;
      return $CMS->COM->GetAuthor($intCommentID);
    }
    function GetFileAuthor($intFileID) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrAuthor = $this->ResultQuery("SELECT username FROM {IFW_TBL_UPLOADS} up LEFT JOIN {IFW_TBL_USERS} u ON u.id = up.author_id WHERE up.id = $intFileID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAuthor[0]['username'];
    }
    function GetAuthor() {
      return $this->strAuthorName;
    }
    function SetAuthor($strAuthor) {
      $this->strAuthorName = $strAuthor;
    }
    function GetAuthorID() {
      return $this->intAuthorID;
    }
    function SetAuthorID($intAuthorID) {
      $this->intAuthorID = $intAuthorID;
    }
    function GetUserGroups() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->strUserGroups)) {
        $intUserID = $this->GetCurrentUserID();
        if ($intUserID) {
          $arrGroups = $this->ResultQuery("SELECT user_groups FROM {IFW_TBL_USERS} WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
          $strUserGroups = $arrGroups[0]['user_groups'];
        } else {
          $strUserGroups = "0";
        }
        $this->SetUserGroups($strUserGroups);
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->strUserGroups;
    }
    function SetUserGroups($strGroups) {
      $this->strUserGroups = $strGroups;
    }
    function GetAllowedGroups() {
      return $this->strAllowedGroups;
    }
    function SetAllowedGroups($strGroups) {
      $this->strAllowedGroups = $strGroups;
    }
    function GetSessionIDCookie() {
      global $CMS;
      if (!isset($this->strCookie)) {
        $this->strCookie = $CMS->CK->Get(C_CK_LOGIN);
      }
      return $this->strCookie;
    }
    function SetSessionIDCookie($intSID) {
      global $CMS;
      $intCookieDuration = $CMS->SYS->GetCookieDuration();
      $CMS->CK->Set(C_CK_LOGIN, $intSID, $intCookieDuration);
    }
    function GetAJAXSID() {
      return $this->strAJAXSID;
    }
    function SetAJAXSID($intSID) {
      $this->strAJAXSID = $intSID;
    }
    function GetAJAXIP() {
      return $this->strAJAXIP;
    }
    function SetAJAXIP($intIP) {
      $this->strAJAXIP = $intIP;
    }
    /////////////////
    function ValidateAllowedGroups() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $this->ClearErrors();
      /* This breaks guest comments
      $this->ValidateLoggedIn();
      if ($this->IsError()) {
        $this->InsufficientAccess();
      } */
      $strAllowedGroups = $this->GetAllowedGroups();
      if ($strAllowedGroups != "") {
        $strUserGroups = $this->GetUserGroups();
        $blnMatch = $CMS->UG->GroupMatch($strUserGroups, $strAllowedGroups);
        if ($blnMatch) {
          $this->ClearErrors();
        } else {
          $this->InsufficientAccess();
        }
      } else {
        $this->InsufficientAccess();
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function ValidateAuthor() {
      $dteStartTime = $this->MicrotimeFloat();
      $this->ValidateLoggedIn();
      if ($this->IsError()) {
        $this->InsufficientAccess();
      } else {
        //$this->SetCurrentUser();
        if ($this->GetCurrentUser() != $this->GetAuthor()) {
          $this->InsufficientAccess();
        } else {
          $this->ClearErrors();
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function ValidateAuthorID() {
      $dteStartTime = $this->MicrotimeFloat();
      $this->ValidateLoggedIn();
      if ($this->IsError()) {
        $this->InsufficientAccess();
      } else {
        if ($this->GetCurrentUserID() != $this->GetAuthorID()) {
          $this->InsufficientAccess();
        } else {
          $this->ClearErrors();
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function ValidateLoggedIn() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strSID = $this->GetSessionIDCookie();
      if ($strSID) {
        $intUserIP = $_SERVER['REMOTE_ADDR'];
        $blnAJAX = false;
      } else {
        $strSID = $this->GetAJAXSID();
        // User IP will be the server IP if using AJAX
        $intUserIP = $this->GetAJAXIP();
        $blnAJAX = true;
      }
      if (!$strSID) {
        $this->NotLoggedIn();
      } else {
        if (isset($this->arrSessionData[$strSID]['expiry_date'])) {
          $intSessionIP  = $this->arrSessionData[$strSID]['ip_address'];
          $dteExpiryDate = $this->arrSessionData[$strSID]['expiry_date'];
          $dteTodaysDate = $this->arrSessionData[$strSID]['todays_date'];
        } else {
          $dteTodaysDate = $CMS->SYS->GetCurrentDateAndTime();
          $arrSIDQuery = $this->ResultQuery("SELECT ip_address, expiry_date FROM {IFW_TBL_USER_SESSIONS} WHERE session_id = '$strSID'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
          $intSessionIP  = $arrSIDQuery[0]['ip_address'];
          $dteExpiryDate = $arrSIDQuery[0]['expiry_date'];
          $this->arrSessionData[$strSID]['ip_address']  = $intSessionIP;
          $this->arrSessionData[$strSID]['expiry_date'] = $dteExpiryDate;
          $this->arrSessionData[$strSID]['todays_date'] = $dteTodaysDate;
        }
        // Preference to add later
        // if ($intSessionIP == $intUserIP)
        if ($dteExpiryDate > $dteTodaysDate) {
          $this->ClearErrors();
        } else {
          $this->NotLoggedIn();
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function ValidatePublic() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strAllowedGroups = $this->GetAllowedGroups();
      $blnMatch = $CMS->UG->GroupMatch("0", $strAllowedGroups);
      if ($blnMatch) {
        $this->ClearErrors();
      } else {
        $this->InsufficientAccess();
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function InsufficientAccess() {
      $this->strOutput = $this->Err_MWarn(M_ERR_UNAUTHORISED, "");
    }
    function NotLoggedIn() {
      $this->strOutput = $this->Err_MWarn(M_ERR_NOT_LOGGED_IN, "");
    }
    function IsError() {
      return !empty($this->strOutput) ? true : false;
    }
    function ClearErrors() {
      $this->strOutput = "";
    }
    // ** Helper functions ** //
    function GetAreaProfileID($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrPerProfile = $this->ResultQuery("SELECT permission_profile_id FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrPerProfile[0]['permission_profile_id'];
    }
    function DoAreaGroups($intAreaID, $strFieldName) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($CMS->AR->arrArea[$intAreaID])) {
        $CMS->AR->arrArea[$intAreaID] = $CMS->AR->GetArea($intAreaID);
      }
      $intPerProfileID = $CMS->AR->arrArea[$intAreaID]['permission_profile_id'];
      if ($intPerProfileID > 0) {
        //$strAllowedGroups = $CMS->AR->arrArea[$intAreaID][$strFieldName];
        $arrAllowedGroups = $this->ResultQuery("SELECT pa.$strFieldName FROM ({IFW_TBL_AREAS} a, {IFW_TBL_PERMISSION_PROFILES} pa) WHERE a.permission_profile_id = pa.id AND a.id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $strAllowedGroups = $arrAllowedGroups[0][$strFieldName];
      } else {
        if (!isset($this->arrSystemGroups[$strFieldName])) {
          $arrAllowedGroups = $this->ResultQuery("SELECT $strFieldName FROM {IFW_TBL_PERMISSION_PROFILES} WHERE is_system = 'Y'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
          $this->arrSystemGroups[$strFieldName] = $arrAllowedGroups[0][$strFieldName];
        }
        $strAllowedGroups = $this->arrSystemGroups[$strFieldName];
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strAllowedGroups;
    }
    function GroupValidator($intAreaID, $strField) {
      $dteStartTime = $this->MicrotimeFloat();
      if (isset($this->arrAllowedGroups[$intAreaID][$strField])) {
        $strAllowedGroups = $this->arrAllowedGroups[$intAreaID][$strField];
      } else {
        $strAllowedGroups = $this->DoAreaGroups($intAreaID, $strField);
        $this->arrAllowedGroups[$intAreaID][$strField] = $strAllowedGroups;
      }
      $this->SetAllowedGroups($strAllowedGroups);
      $this->ValidateAllowedGroups();
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Admin ** //
    function Admin() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $intAdminGroupID = $CMS->UG->GetAdminGroupID();
      $this->SetAllowedGroups($intAdminGroupID);
      $this->ValidateAllowedGroups();
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Area Permissions ** //
    function ViewArea($intAreaID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      //if (!isset($this->arrAreaProfileIDs[$intAreaID])) {
      //  $intPerProfileID = $CMS->AR->GetPerProfileID($intAreaID);
      //  $this->arrAreaProfileIDs[$intAreaID] = $intPerProfileID;
      //}
      // Ensure area cache is loaded
      if (empty($CMS->AR->arrArea[$intAreaID])) {
        $CMS->AR->arrArea[$intAreaID] = $CMS->AR->GetArea($intAreaID);
      }
      if (empty($CMS->AR->arrArea[$intAreaID]['profile_id'])) {
        $intProfileID = 0;
      } else {
        $intProfileID = $CMS->AR->arrArea[$intAreaID]['profile_id'];
      }
      // Grab allowed groups
      if ($intProfileID) {
        $strAllowedGroups = $CMS->AR->arrArea[$intAreaID]['view_area'];
      } else {
        $strAllowedGroups = $CMS->PP->GetViewArea("");
      }
      //$intPerProfileID = $this->arrAreaProfileIDs[$intAreaID];
      //if (isset($this->arrAllowedGroups[$intAreaID]["view_area"])) {
      //  $strAllowedGroups = $this->arrAllowedGroups[$intAreaID]["view_area"];
      //} else {
      //  $this->arrAllowedGroups[$intAreaID]["view_area"] = $strAllowedGroups;
      //}
      $this->SetAllowedGroups($strAllowedGroups);
      $this->ValidatePublic();
      if ($this->strOutput) {
        $this->ValidateAllowedGroups();
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__ . " (Area: $intAreaID)", __LINE__);
    }
    // ** Articles ** //
    function CreateArticle($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "create_article");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function PublishArticle($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "publish_article");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function EditArticle($intAreaID, $intArticleID) {
      $dteStartTime = $this->MicrotimeFloat();
      if (!$this->GetAuthor()) {
        $this->SetAuthor($this->GetArticleAuthor($intArticleID));
      }
      $this->ValidateAuthor();
      if ($this->strOutput) {
        $this->GroupValidator($intAreaID, "edit_article");
      }
      $this->SetAuthor("");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function EditArticleCached($intAreaID, $intArticleID, $intAuthorID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->SetAuthorID($intAuthorID);
      $this->ValidateAuthorID();
      if ($this->strOutput) {
        $this->GroupValidator($intAreaID, "edit_article");
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function DeleteArticle($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "delete_article");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Attachments ** //
    function AttachFile($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "attach_file");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Comments ** //
    function AddComment($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "add_comment");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function EditComment($intAreaID, $intCommentID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->SetAuthor($this->GetCommentAuthor($intCommentID));
      $this->ValidateAuthor();
      if ($this->strOutput) {
        $this->GroupValidator($intAreaID, "edit_comment");
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function DeleteComment($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "delete_comment");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function LockArticle($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->GroupValidator($intAreaID, "lock_article");
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // Permissions across multiple areas //
    function CountTotalWriteAccess() {
      global $CMS;
      $arrTopLevelAreas  = $CMS->AT->GetParentedAreas("", "All", "");
      $intAreaWriteCount = 0;
      for ($i=0; $i<count($arrTopLevelAreas); $i++) {
        $intID = $arrTopLevelAreas[$i]['id'];
        $intAreaWriteCount += $CMS->RES->CountContentAreasWithWriteAccess($intID);
      }
      return $intAreaWriteCount;
    }
    function CountContentAreasWithWriteAccess($intParentID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $arrContentAreas = $CMS->AT->GetAllParentedAreas($intParentID, "Content", "");
      $intAllowedAreas = 0;
      for ($i=0; $i<count($arrContentAreas); $i++) {
        $intID = $arrContentAreas[$i]['id'];
        $this->CreateArticle($intID);
        if (!$this->IsError()) {
          $intAllowedAreas++;
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $intAllowedAreas;
    }
    // ** Manage Content ** //
    function ViewManageContent() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $intMyArticleCount = $CMS->ART->CountUserContent($this->GetCurrentUserID(), "");
      $intAreaWriteCount = $this->CountTotalWriteAccess();
      if (($intMyArticleCount > 0) || ($intAreaWriteCount > 0)) {
        $this->ClearErrors();
      } else {
        $this->InsufficientAccess();
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
  }
?>