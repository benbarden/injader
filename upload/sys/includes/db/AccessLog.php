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

  class AccessLog extends Helper {
    // Required for login
    var $intTempUserID;
    // ** Insert into database ** //
    function Create($strDetail, $strTag) {
      global $CMS;
      $dteLogDate = $CMS->SYS->GetCurrentDateAndTime();
      if ($this->intTempUserID) {
        $intUserID = $this->intTempUserID;
      } else {
        $intUserID  = $CMS->RES->GetCurrentUserID();
        if (!$intUserID) {
          $intUserID = 0;
        }
      }
      $strUserIP = $_SERVER['REMOTE_ADDR'];
      // ** Build Query: Create Log ** //
      $strQuery = sprintf("INSERT INTO {IFW_TBL_ACCESS_LOG}(user_id, detail, tag, log_date, ip_address) VALUES(%s, '%s', '%s', '%s', '%s')",
        $intUserID,
        mysql_real_escape_string($strDetail),
        mysql_real_escape_string($strTag),
        mysql_real_escape_string($dteLogDate),
        mysql_real_escape_string($strUserIP)
      );
      // ** Process query ** //
      $this->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Log builder ** //
    function Build($strTag, $intItemID, $strItemTitle) {
      global $CMS;
      $strLogDesc = "";
      $blnBasicLog = true;
      $blnPLArticle = false;
      switch ($strTag) {
        case AL_TAG_AREA_CREATE:
          $strLogDesc = M_AL_AREA_CREATE; break;
        case AL_TAG_AREA_DELETE:
          $strLogDesc = M_AL_AREA_DELETE; break;
        case AL_TAG_AREA_EDIT:
          $strLogDesc = M_AL_AREA_EDIT;   break;
        case AL_TAG_ARTICLE_CREATE:
          $strLogDesc = M_AL_ARTICLE_CREATE;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_EDIT:
          $strLogDesc = M_AL_ARTICLE_EDIT;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_DELETE:
          $strLogDesc = M_AL_ARTICLE_DELETE;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_LOCK:
          $strLogDesc = M_AL_ARTICLE_LOCK;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_MARK:
          $strLogDesc = M_AL_ARTICLE_MARK;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_RESTORE:
          $strLogDesc = M_AL_ARTICLE_RESTORE;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_UNLOCK:
          $strLogDesc = M_AL_ARTICLE_UNLOCK;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_SAVEDRAFT:
          $strLogDesc = M_AL_ARTICLE_SAVEDRAFT;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_REVIEW:
          $strLogDesc = M_AL_ARTICLE_REVIEW;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_PUBLISH:
          $strLogDesc = M_AL_ARTICLE_PUBLISH;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_SCHEDULE:
          $strLogDesc = M_AL_ARTICLE_SCHEDULE;
          $blnBasicLog = false; $blnPLArticle = true; break;
        case AL_TAG_ARTICLE_BULKMOVE:
          $strItemTitle = str_replace(",", ", ", $strItemTitle);
          $strLogDesc = "Bulk moved articles $strItemTitle to area $intItemID";
          $blnBasicLog = false;
          break;
        case AL_TAG_ARTICLE_BULKLOCK:
          $strItemTitle = str_replace(",", ", ", $strItemTitle);
          $strLogDesc = "Bulk locked articles $strItemTitle";
          $blnBasicLog = false;
          break;
        case AL_TAG_ARTICLE_BULKUNLOCK:
          $strItemTitle = str_replace(",", ", ", $strItemTitle);
          $strLogDesc = "Bulk unlocked articles $strItemTitle";
          $blnBasicLog = false;
          break;
        case AL_TAG_ARTICLE_BULKEDITAUTHOR:
          $strItemTitle = str_replace(",", ", ", $strItemTitle);
          $strLogDesc = "Bulk edited author for articles $strItemTitle";
          $blnBasicLog = false;
          break;
        case AL_TAG_ARTICLE_BULKDELETE:
          $strItemTitle = str_replace(",", ", ", $strItemTitle);
          $strLogDesc = "Bulk deleted articles $strItemTitle";
          $blnBasicLog = false;
          break;
        case AL_TAG_ARTICLE_BULKRESTORE:
          $strItemTitle = str_replace(",", ", ", $strItemTitle);
          $strLogDesc = "Bulk restored articles $strItemTitle";
          $blnBasicLog = false;
          break;
        case AL_TAG_COMMENT_ADD:
          $strLogDesc = "Added comment"; break;
        case AL_TAG_COMMENT_DELETE:
          $strLogDesc = "Deleted comment"; break;
        case AL_TAG_COMMENT_EDIT:
          $strLogDesc = "Edited comment"; break;
        case AL_TAG_PLUGIN_CREATE:
          $strLogDesc = "Created plugin"; break;
        case AL_TAG_PLUGIN_EDIT:
          $strLogDesc = "Edited plugin"; break;
        case AL_TAG_PLUGIN_DELETE:
          $strLogDesc = "Deleted plugin"; break;
        case AL_TAG_PPCA_CREATE:
          $strLogDesc = "Created area-specific permission profile"; break;
        case AL_TAG_PPCA_DELETE:
          $strLogDesc = "Deleted area-specific permission profile"; break;
        case AL_TAG_PPCA_EDIT:
          $strLogDesc = "Edited area-specific permission profile"; break;
        case AL_TAG_PPSYS_EDIT:
          $strLogDesc = "Edited system permission profile"; break;
        case AL_TAG_USER_EDIT:
          $strLogDesc = "Edited user"; break; // Name
        case AL_TAG_USER_EDITPASSWORD:
          $strLogDesc = "Edited password"; break; // Name
        case AL_TAG_USER_EDITPROFILE:
          $strLogDesc = "Edited profile"; break; // Name
        case AL_TAG_USER_LOGIN:
          $strLogDesc = "Logged in"; $this->intTempUserID = $intItemID; break;
        case AL_TAG_USER_LOGOUT:
          $strLogDesc = "Logged out"; break;
        case AL_TAG_USER_REGISTER:
          $strLogDesc = "Registered new user"; break; // Name
        case AL_TAG_USER_REINSTATE:
          $strLogDesc = "Reinstated user"; break; // Name
        case AL_TAG_USER_SESSION_DELETE:
          $strLogDesc = "Deleted user session"; break;
        case AL_TAG_USER_SESSION_DELETE_EXPIRED:
          $strLogDesc = "Deleted all expired user sessions"; break;
        case AL_TAG_USER_SUSPEND:
          $strLogDesc = "Suspended user"; break; // Name
        case AL_TAG_USERGROUP_CREATE:
          $strLogDesc = "Created user group"; break;
        case AL_TAG_USERGROUP_EDIT:
          $strLogDesc = "Edited user group"; break;
        case AL_TAG_USERGROUP_DELETE:
          $strLogDesc = "Deleted user group"; break;
      }
      if ($strLogDesc) {
        if ($blnBasicLog) {
          if ($strItemTitle) {
            $strLogDesc .= ": $strItemTitle (ID: $intItemID)";
          } elseif (($strTag == AL_TAG_USER_LOGIN) || 
                    ($strTag == AL_TAG_USER_LOGOUT) ||
                    ($strTag == AL_TAG_USER_EDITPROFILE)) {
            //$strLogDesc .= "";
          } elseif ($intItemID) {
            $strLogDesc .= " (ID: $intItemID)";
          }
        } elseif ($blnPLArticle) {
          $strViewLink = $CMS->PL->ViewArticle($intItemID);
          $strLogDesc = $strLogDesc.": <a href=\"$strViewLink\">$strItemTitle</a> (ID: $intItemID)";
        }
        $this->Create($strLogDesc, $strTag);
      }
    }
    // ** Purge old access log entries ** //
    function Purge() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $intLogLimit = $CMS->SYS->GetSysPref(C_PREF_MAX_LOG_ENTRIES);
      $arrLogData = $CMS->ResultQuery("SELECT id FROM {IFW_TBL_ACCESS_LOG} ORDER BY id DESC LIMIT $intLogLimit,1", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $intLogID = $arrLogData[0]['id'];
      if ($intLogID) {
        $arrExcess = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_ACCESS_LOG} WHERE id < $intLogID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $intExcessRowCount = $arrExcess[0]['count'];
        if ($intExcessRowCount > 50) {
          $CMS->Query("DELETE FROM {IFW_TBL_ACCESS_LOG} WHERE id < $intLogID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
  }

?>