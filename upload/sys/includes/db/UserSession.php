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

  class UserSession extends Helper {
    // ** Database writes ** //
    function Create($intUserID, $dteTodaysDate, $dteExpiryDate) {
      $intSessionID = md5(mt_rand().time());
      $intUserIP    = $_SERVER['REMOTE_ADDR'];
      $strUserAgent = empty($_SERVER['HTTP_USER_AGENT']) ? "" : $_SERVER['HTTP_USER_AGENT'];
      $this->Query("INSERT INTO {IFW_TBL_USER_SESSIONS}(session_id, user_id, ip_address, user_agent, login_date, expiry_date) VALUES('$intSessionID', $intUserID, '$intUserIP', '$strUserAgent', '$dteTodaysDate', '$dteExpiryDate')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $intSessionID;
    }
    function Delete($intSessionID) {
      global $CMS;
      $arrSData = $this->ResultQuery("SELECT u.username FROM ({IFW_TBL_USERS} u, {IFW_TBL_USER_SESSIONS} us) WHERE u.id = us.user_id AND us.id = $intSessionID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strUser = $arrSData[0]['username'];
      $this->Query("DELETE FROM {IFW_TBL_USER_SESSIONS} WHERE id = $intSessionID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_SESSION_DELETE, $intSessionID, $strUser);
    }
    function DeleteExpiredUserSessions($intUserID, $dteTodaysDate) {
      $this->Query("DELETE FROM {IFW_TBL_USER_SESSIONS} WHERE user_id = $intUserID AND expiry_date < '$dteTodaysDate'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function DeleteAllUserSessions($intUserID) {
      $this->Query("DELETE FROM {IFW_TBL_USER_SESSIONS} WHERE user_id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function DeleteAllExpiredSessions() {
      global $CMS;
      $dteDate = $CMS->SYS->GetCurrentDateAndTime();
      $this->Query("DELETE FROM {IFW_TBL_USER_SESSIONS} WHERE expiry_date < '$dteDate'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_SESSION_DELETE_EXPIRED, "", "");
    }
    // ** Select ** //
    function GetAll() {
      $arrSessions = $this->ResultQuery("SELECT us.id, us.session_id, us.user_id, u.username, u.seo_username, us.user_agent, us.login_date, us.expiry_date, us.ip_address FROM {IFW_TBL_USER_SESSIONS} us LEFT JOIN {IFW_TBL_USERS} u ON us.user_id = u.id ORDER BY u.username ASC, us.id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrSessions;
    }
  }

?>