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

  class User extends Helper {
    // Caching
    var $arrUser;
    var $blnForceDB = false;
    // Insert, Update, Delete //
    function Create($strCaller, $strUsername, $strPassword, $strForename, $strSurname, $strEmail, $strLocation, $strOccupation, $strInterests, $strHomepageLink, $strHomepageText, $intAvatarID, $dteJoinDate, $strUserIP, $strUserGroups) {
      global $CMS;
      $strPassword = md5($strPassword);
      if (empty($intAvatarID)) {
        $intAvatarID = 0;
      }
      if (($strCaller == FN_REGISTER) || ($strCaller == FN_COMMENT)) {
        if ($strUserGroups == "") {
          $strUserGroups = $CMS->UG->GetDefaultGroup();
        }
      }
      if (!$dteJoinDate) {
        $dteJoinDate = $CMS->SYS->GetCurrentDateAndTime();
      }
      $strHomepageLink = $CMS->AutoLink($strHomepageLink);
      $strSEOUsername = $this->MakeSEOTitle($strUsername);
      $intUserID = $this->Query("INSERT INTO {IFW_TBL_USERS}(username, userpass, forename, surname, email, location, occupation, interests, homepage_link, homepage_text, avatar_id, join_date, ip_address, user_groups, seo_username, user_deleted, user_moderate) VALUES ('$strUsername', '$strPassword', '$strForename', '$strSurname', '$strEmail', '$strLocation', '$strOccupation', '$strInterests', '$strHomepageLink', '$strHomepageText', $intAvatarID, '$dteJoinDate', '$strUserIP', '$strUserGroups', '$strSEOUsername', 'N', 'Y')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_REGISTER, $intUserID, $strUsername);
      return $intUserID;
    }
    function Edit($intUserID, $strUsername, $strForename, $strSurname, $strEmail, $strLocation, $strOccupation, $strInterests, $strHomepageLink, $strHomepageText, $intAvatarID, $dteJoinDate, $strUserIP, $strUserGroups) {
      global $CMS;
      if (empty($intAvatarID)) {
        $intAvatarID = 0;
      }
      if (!$dteJoinDate) {
        $dteJoinDate = $CMS->SYS->GetCurrentDateAndTime();
      }
      $strHomepageLink = $CMS->AutoLink($strHomepageLink);
      $strSEOUsername = $this->MakeSEOTitle($strUsername);
      $this->Query("UPDATE {IFW_TBL_USERS} SET username = '$strUsername', forename = '$strForename', surname = '$strSurname', email = '$strEmail', location = '$strLocation', occupation = '$strOccupation', interests = '$strInterests', homepage_link = '$strHomepageLink', homepage_text = '$strHomepageText', avatar_id = $intAvatarID, join_date = '$dteJoinDate', ip_address = '$strUserIP', user_groups = '$strUserGroups', seo_username = '$strSEOUsername' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_EDIT, $intUserID, $strUsername);
      return $intUserID;
    }
    function EditPassword($intUserID, $strPassword) {
      global $CMS;
      $strPassword = md5($strPassword);
      $this->Query("UPDATE {IFW_TBL_USERS} SET userpass = '$strPassword' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_EDITPASSWORD, $intUserID, "");
    }
    function EditProfile($intUserID, $strForename, $strSurname, $strEmail, $strLocation, $strOccupation, $strInterests, $strHomeLink, $strHomeText) {
      global $CMS;
      $strForename    = $CMS->AddSlashesIFW($strForename);
      $strSurname     = $CMS->AddSlashesIFW($strSurname);
      $strEmail       = $CMS->AddSlashesIFW($strEmail);
      $strLocation    = $CMS->AddSlashesIFW($strLocation);
      $strOccupation  = $CMS->AddSlashesIFW($strOccupation);
      $strInterests   = $CMS->AddSlashesIFW($strInterests);
      $strHomeLink    = $CMS->AutoLink($strHomeLink);
      $strHomeLink    = $CMS->AddSlashesIFW($strHomeLink);
      $strHomeText    = $CMS->AddSlashesIFW($strHomeText);
      $this->Query("UPDATE {IFW_TBL_USERS} SET forename = '$strForename', surname = '$strSurname', email = '$strEmail', location = '$strLocation', occupation = '$strOccupation', interests = '$strInterests', homepage_link = '$strHomeLink', homepage_text = '$strHomeText' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_EDITPROFILE, $intUserID, "");
    }
    function Login($intUserID) {
      global $CMS;
      // Create session
      $dteTodaysDate = $CMS->SYS->GetCurrentDateAndTime();
      $dteExpiryDate = $CMS->SYS->GetCookieExpiry();
      $intSID = $CMS->USess->Create($intUserID, $dteTodaysDate, $dteExpiryDate);
      $this->SetUserIP($intUserID, $_SERVER['REMOTE_ADDR']);
      // Set cookie
      $CMS->RES->SetSessionIDCookie($intSID);
      // Maintenance and logging
      $CMS->USess->DeleteExpiredUserSessions($intUserID, $dteTodaysDate);
      $CMS->AL->Build(AL_TAG_USER_LOGIN, $intUserID, "");
    }
    function Logout($intUserID) {
      global $CMS;
      $CMS->USess->DeleteAllUserSessions($intUserID);
      $CMS->CK->Clear(C_CK_LOGIN);
      $CMS->AL->Build(AL_TAG_USER_LOGOUT, $intUserID, "");
    }
    function SetUserIP($intUserID, $intUserIP) {
      $this->Query("UPDATE {IFW_TBL_USERS} SET ip_address = '$intUserIP' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Activation Key ** //
    function MakeActivationKey($intUserID) {
      $strKeyData = md5(mt_rand());
      $this->Query("UPDATE {IFW_TBL_USERS} SET activation_key = '$strKeyData' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strKeyData;
    }
    function GetActivationKey($intUserID) {
      $arrResult = $this->ResultQuery("SELECT activation_key FROM {IFW_TBL_USERS} WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['activation_key'];
    }
    function ClearActivationKey($intUserID) {
      $this->Query("UPDATE {IFW_TBL_USERS} SET activation_key = '' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Get All ** //
    function GetAllWithUserGroup($intUserGroupID) {
      global $CMS;
      $strWhereClause = $CMS->UG->BuildUserGroupSQL("", $intUserGroupID, true, false);
      $arrUsers = $this->ResultQuery("SELECT * FROM {IFW_TBL_USERS} $strWhereClause ORDER BY id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUsers;
    }
    // ** Retrieve User ** //
    function Get($intUserID) {
      if (empty($this->arrUser[$intUserID]['id']) || $this->blnForceDB == true) {
        $arrUser = $this->ResultQuery("SELECT us.*, us.location AS us_loc, up.id AS up_id FROM {IFW_TBL_USERS} us LEFT JOIN {IFW_TBL_UPLOADS} up ON up.id = us.avatar_id WHERE us.id = $intUserID", __CLASS__ . "::" . __FUNCTION__ . " (User: $intUserID)", __LINE__);
        $this->arrUser[$intUserID] = $arrUser[0];
      }
      return $this->arrUser[$intUserID];
    }
    function GetForceDB($intUserID) {
      $this->blnForceDB = true;
      return $this->Get($intUserID);
    }
    // ** Single-field selects ** //
    function GetSEOTitle($intUserID) {
      if (!isset($this->arrUser[$intUserID])) {
        $this->Get($intUserID);
      }
      return $this->arrUser[$intUserID]['seo_username'];
    }
    function GetNameFromID($intUserID) {
      if (!isset($this->arrUser[$intUserID])) {
        $this->Get($intUserID);
      }
      return $this->arrUser[$intUserID]['username'];
    }
    function GetUserGroups($intUserID) {
      if (!isset($this->arrUser[$intUserID])) {
        $this->Get($intUserID);
      }
      return $this->arrUser[$intUserID]['user_groups'];
    }
    function IsSuspended($intUserID) {
      if (!isset($this->arrUser[$intUserID])) {
        $this->Get($intUserID);
      }
      return $this->arrUser[$intUserID]['user_deleted'] == 'Y' ? true : false;
    }
    // ** Checking ** //
    function GetIDFromNameAndEmail($strUsername, $strEmail) {
      global $CMS;
      $strUsername = $CMS->AddSlashesIFW($strUsername);
      $arrResult = $this->ResultQuery("SELECT id, email FROM {IFW_TBL_USERS} WHERE username = '$strUsername' AND email = '$strEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['id'];
    }
    function GetIDFromName($strUsername) {
      global $CMS;
      $strUsername = $CMS->AddSlashesIFW($strUsername);
      $arrResult = $this->ResultQuery("SELECT id FROM {IFW_TBL_USERS} WHERE username = '$strUsername'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['id'];
    }
    function GetNewestUserIP() {
      $arrResult = $this->ResultQuery("SELECT ip_address FROM {IFW_TBL_USERS} ORDER BY id DESC LIMIT 1", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['ip_address'];
    }
    function ValidateLogin($strUsername, $strPassword) {
      global $CMS;
      $strUsername = $CMS->AddSlashesIFW($strUsername);
      $arrLoggedOn = $this->ResultQuery("SELECT username, id FROM {IFW_TBL_USERS} WHERE username = '$strUsername' AND userpass = '$strPassword'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strLoginName = $arrLoggedOn[0]['username'];
      $intUserID    = $arrLoggedOn[0]['id'];
      return $intUserID;
    }
    function IsUniqueUsername($strUsername) {
      global $CMS;
      $strUsername = $CMS->AddSlashesIFW($strUsername);
      $arrUserInfo = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USERS} WHERE username = '$strUsername'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUserInfo[0]['count'] == 0 ? true : false;
    }
    function IsUniqueEmail($strEmail) {
      global $CMS;
      $strEmail = $CMS->AddSlashesIFW($strEmail);
      $arrUserInfo = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USERS} WHERE email = '$strEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUserInfo[0]['count'] == 0 ? true : false;
    }
    function IsUsernameLengthValid($strUsername) {
      if ((strlen($strUsername) >= 3) && (strlen($strUsername) <= 45)) {
        return true;
      } else {
        return false;
      }
    }
    function IsUsernameInUse($strGuestName, $strGuestEmail) {
      global $CMS;
      $strGuestName = $CMS->AddSlashesIFW($strGuestName);
      $arrUserDetails = $this->ResultQuery("SELECT id FROM {IFW_TBL_USERS} WHERE username = '$strGuestName' AND email = '$strGuestEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return empty($arrUserDetails[0]['id']) ? "" : $arrUserDetails[0]['id'];
    }
    // ** Bulk, suspend/reinstate, moderate ** //
    function Suspend($intUserID) {
      global $CMS;
      if ($intUserID) {
        $strUsername = $this->GetNameFromID($intUserID);
        $this->Query("UPDATE {IFW_TBL_USERS} SET user_deleted = 'Y' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $CMS->USess->DeleteAllUserSessions($intUserID);
        $CMS->AL->Build(AL_TAG_USER_SUSPEND, $intUserID, $strUsername);
      }
    }
    function Reinstate($intUserID) {
      global $CMS;
      if ($intUserID) {
        $strUsername = $this->GetNameFromID($intUserID);
        $this->Query("UPDATE {IFW_TBL_USERS} SET user_deleted = 'N' WHERE id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $CMS->AL->Build(AL_TAG_USER_REINSTATE, $intUserID, $strUsername);
      }
    }
    function BulkSuspend($strUserIDs) {
      if ($strUserIDs) {
        $this->Query("UPDATE {IFW_TBL_USERS} SET user_deleted = 'Y' WHERE id IN $strUserIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
    }
    function BulkTrust($strUserIDs) {
      if ($strUserIDs) {
        $this->Query("UPDATE {IFW_TBL_USERS} SET user_moderate = 'N' WHERE id IN $strUserIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
    }
  }
?>