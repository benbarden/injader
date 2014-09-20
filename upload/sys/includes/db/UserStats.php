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

  class UserStats extends Helper {
    var $arrStatData = array();
    // ** Verify if there's an existing entry for this user ** //
    function Exists($strEmail) {
      $blnExists = false;
      if (isset($arrStatData[$strEmail])) {
        $blnExists = true;
      } else {
        $arrTemp = $this->ResultQuery("SELECT * FROM {IFW_TBL_USER_STATS} WHERE user_email = '$strEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        if (is_array($arrTemp) && (count($arrTemp) > 0)) {
          if (isset($arrTemp[0]['comment_count'])) {
            $this->arrStatData[$strEmail]['comment_count']         = $arrTemp[0]['comment_count'];
            $this->arrStatData[$strEmail]['article_subscriptions'] = $arrTemp[0]['article_subscriptions'];
            $blnExists = true;
          }
        }
      }
      return $blnExists;
    }
    // ** Standard match function ** //
    function Match($strHaystack, $strNeedle, $strDelim) {
      $blnFound = false;
      $arrCurrent = explode($strDelim, $strHaystack);
      for ($i=0; $i<count($arrCurrent); $i++) {
        if ($arrCurrent[$i] == $strNeedle) {
          $blnFound = true;
          break;
        }
      }
      return $blnFound;
    }
    // ** Check if user is subscribed ** //
    function IsSubscribed($strEmail, $intSubID) {
      $blnSubscribed = false;
      if ($this->Exists($strEmail)) {
        $strSubs = $this->arrStatData[$strEmail]['article_subscriptions'];
        $blnSubscribed = $this->Match($strSubs, $intSubID, ",");
      }
      return $blnSubscribed;
    }
    // ** Get user subscriptions ** //
    function GetSubscriptions($strEmail) {
      $strSubs = "";
      if ($this->Exists($strEmail)) {
        $strSubs = $this->arrStatData[$strEmail]['article_subscriptions'];
      }
      return $strSubs;
    }
    // ** Generate article subscription ** //
    function MakeSub($strCurrent, $intNewID) {
      if ($strCurrent) {
        if (!$this->Match($strCurrent, $intNewID, ",")) {
          $strCurrent .= ",".$intNewID;
        }
      } else {
        $strCurrent .= $intNewID;
      }
      return $strCurrent;
    }
    // ** Only do subscription (e.g. if comment is pending) ** //
    function SetSub($strEmail, $intSubID) {
      if ($this->Exists($strEmail)) {
        // Make article subscription
        if ($intSubID) {
          $strSubs = $this->MakeSub($this->arrStatData[$strEmail]['article_subscriptions'], $intSubID);
        } else {
          $strSubs = $this->arrStatData[$strEmail]['article_subscriptions'];
        }
        // Update
        $this->Query("UPDATE {IFW_TBL_USER_STATS} SET article_subscriptions = '$strSubs' WHERE user_email = '$strEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      } else {
        // Insert
        $this->Query("INSERT INTO {IFW_TBL_USER_STATS}(user_email, comment_count, article_subscriptions) VALUES('$strEmail', 0, '$intSubID')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
    }
    // ** Insert or update comment count ** //
    function Plus($strEmail, $intSubID) {
      if ($this->Exists($strEmail)) {
        // Make article subscription
        if ($intSubID) {
          $strSubs = $this->MakeSub($this->arrStatData[$strEmail]['article_subscriptions'], $intSubID);
        } else {
          $strSubs = $this->arrStatData[$strEmail]['article_subscriptions'];
        }
        // Update
        $this->Query("UPDATE {IFW_TBL_USER_STATS} SET comment_count = comment_count + 1, article_subscriptions = '$strSubs' WHERE user_email = '$strEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      } else {
        // Insert
        $this->Query("INSERT INTO {IFW_TBL_USER_STATS}(user_email, comment_count, article_subscriptions) VALUES('$strEmail', 1, '$intSubID')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
    }
    function Minus($strEmail) {
      if ($this->Exists($strEmail)) {
        // Update
        $this->Query("UPDATE {IFW_TBL_USER_STATS} SET comment_count = comment_count - 1 WHERE user_email = '$strEmail'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      } else {
        // Nothing to do
      }
    }
    // ** Retrieve ** //
    function Get($strEmail) {
      if ($this->Exists($strEmail)) {
        return $this->arrStatData[$strEmail];
      } else {
        return "";
      }
    }
    // ** Standard get email function ** //
    function GetEmail() {
      global $CMS;
      $CMS->RES->ValidateLoggedIn();
      if ($CMS->RES->IsError()) {
        $strEmail = $CMS->CK->Get(C_CK_COMMENT_EMAIL);
      } else {
        $arrUserData = $CMS->US->Get($CMS->RES->GetCurrentUserID());
        $strEmail = $arrUserData['email'];
      }
      $CMS->RES->ClearErrors();
      return $strEmail;
    }
    // ** Get subscriptions ** //
    function GetSubscribedUsers($intArticleID) {
      $arrUsers = $this->ResultQuery("SELECT user_email FROM {IFW_TBL_USER_STATS} WHERE (article_subscriptions = '$intArticleID' OR article_subscriptions LIKE '$intArticleID,%' OR article_subscriptions LIKE '%,$intArticleID,%' OR article_subscriptions LIKE '%,$intArticleID')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUsers;
    }
  }

?>