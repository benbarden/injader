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

  class UserGroup extends Helper {
    var $arrAllUserGroups;
    var $blnReadUserlist;
    var $intAdminGroupID;
    // Insert, Update, Delete //
    function Create($strName) {
      global $CMS;
      $intGroupID = $this->Query("INSERT INTO {IFW_TBL_USER_GROUPS}(name, is_default, is_admin) VALUES('$strName', 'N', 'N')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USERGROUP_CREATE, $intGroupID, $strName);
      return $intGroupID;
    }
    function Edit($intGroupID, $strName) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_USER_GROUPS} SET name = '$strName' WHERE id = $intGroupID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USERGROUP_EDIT, $intGroupID, $strName);
    }
    function Delete($intGroupID, $strName) {
      global $CMS;
      $this->Query("DELETE FROM {IFW_TBL_USER_GROUPS} WHERE id = $intGroupID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USERGROUP_DELETE, $intGroupID, $strName);
    }
    // Select //
    function GetAll() {
      if (isset($this->arrAllUserGroups)) {
        $arrGroups = $this->arrAllUserGroups;
      } else {
        $arrGroups = $this->ResultQuery("SELECT * FROM {IFW_TBL_USER_GROUPS} ORDER BY is_default DESC, is_admin DESC, name ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $this->arrAllUserGroups = $arrGroups;
      }
      return $arrGroups;
    }
    function GetNamedGroupID($strName) {
      global $CMS;
      $arrGroups = $this->GetAll();
      $intGroupID = "";
      for ($i=0; $i<count($arrGroups); $i++) {
        if ($arrGroups[$i]['name'] == $strName) {
          $intGroupID = $arrGroups[$i]['id'];
          break;
        }
      }
      return $intGroupID;
    }
    function GetName($intGroupID) {
      $arrGroups = $this->GetAll();
      $strName = "";
      for ($i=0; $i<count($arrGroups); $i++) {
        if ($arrGroups[$i]['id'] == $intGroupID) {
          $strName = $arrGroups[$i]['name'];
          break;
        }
      }
      return $strName;
    }
    // Special group selection //
    function GetDefaultGroup() {
      $arrGroup = $this->ResultQuery("SELECT id FROM {IFW_TBL_USER_GROUPS} WHERE is_default = 'Y'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrGroup[0]['id'];
    }
    function GetAdminGroupID() {
      if (empty($this->intAdminGroupID)) {
        $arrGroup = $this->ResultQuery("SELECT id FROM {IFW_TBL_USER_GROUPS} WHERE is_admin = 'Y'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $this->intAdminGroupID = $arrGroup[0]['id'];
      }
      $intID = $this->intAdminGroupID;
      return $intID;
    }
    // Exists
    function GroupExists($intGroupID) {
      $arrGroup = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USER_GROUPS} WHERE id = $intGroupID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrGroup[0]['count'] > 0 ? true : false;
    }
    // Useful functions //
    function GroupMatch($strUserGroups, $strAllowedGroups) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrUserGroups    = explode("|", $strUserGroups);
      $arrAllowedGroups = explode("|", $strAllowedGroups);
      $blnMatch = false;
      for ($i=0; $i<count($arrUserGroups); $i++) {
        $intGroupID = $arrUserGroups[$i];
        for ($j=0; $j<count($arrAllowedGroups); $j++) {
          $intReqGroupID = $arrAllowedGroups[$j];
          if ($intGroupID == $intReqGroupID) {
            $blnMatch = true;
            break;
          }
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $blnMatch;
    }
    function BuildGroupList($arrSelectedCheckboxes, $strName) {
      $dteStartTime = $this->MicrotimeFloat();
      $strGroupList = "";
      for ($i=0; $i<count($arrSelectedCheckboxes); $i++) {
        $intGroupID = substr($arrSelectedCheckboxes[$i], strlen($strName));
        if ($i == 0) {
          $strDelim = "";
        } else {
          $strDelim = "|";
        }
        $strGroupList .= $strDelim.$intGroupID;
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strGroupList;
    }
    function BuildUserGroupSQL($strTableAlias, $strUserGroups, $blnForceWhere, $blnAllowEmpty) {
      $blnOpenBracket = false;
      $strWhereClause = "";
      $strAlias = $strTableAlias ? $strTableAlias."." : "";
      if ($strUserGroups) {
        if ($blnAllowEmpty) {
          if ($blnForceWhere) {
            $strWhereClause = "WHERE ".$strAlias."user_groups = ''";
          } else {
            $strWhereClause = "AND (".$strAlias."user_groups = ''";
            $blnOpenBracket = true;
          }
        }
        if ($strWhereClause) {
          $strWhereClause .= " OR ";
        } else {
          if ($blnForceWhere) {
            $strWhereClause = "WHERE ";
          } else {
            $strWhereClause = "AND (";
            $blnOpenBracket = true;
          }
        }
        $arrGroups = explode("|", $strUserGroups);
        for ($i=0; $i<count($arrGroups); $i++) {
          $intGroupID = $arrGroups[$i];
          if ($i > 0) {
            $strWhereClause .= " OR ";
          }
          $strWhereClause .= $strAlias."user_groups = '$intGroupID'"
                          .  " OR ".$strAlias."user_groups LIKE '$intGroupID|%'"
                          .  " OR ".$strAlias."user_groups LIKE '%|$intGroupID|%'"
                          .  " OR ".$strAlias."user_groups LIKE '%|$intGroupID'";
        }
      } else {
        if ($blnForceWhere) {
          $strWhereClause = "WHERE ".$strAlias."user_groups = ''";
        } else {
          $strWhereClause = "AND ".$strAlias."user_groups = ''";
        }
      }
      if ($blnOpenBracket) {
        $strWhereClause .= ")";
      }
      return $strWhereClause;
    }
    function BuildUserUnreadSQL($strTableAlias, $intUserID, $blnForceWhere) {
      $blnOpenBracket = false;
      if ($intUserID) {
        $strAlias = $strTableAlias ? $strTableAlias."." : "";
        if ($blnForceWhere) {
          $strWhereClause = "WHERE ".$strAlias."read_userlist = ''";
        } else {
          $strWhereClause = "AND (".$strAlias."read_userlist = ''";
          $blnOpenBracket = true;
        }
        $strWhereClause .= " OR (".$strAlias."read_userlist <> '$intUserID'"
                        .  " AND ".$strAlias."read_userlist NOT LIKE '$intUserID|%'"
                        .  " AND ".$strAlias."read_userlist NOT LIKE '%|$intUserID|%'"
                        .  " AND ".$strAlias."read_userlist NOT LIKE '%|$intUserID')";
        if ($blnOpenBracket) {
          $strWhereClause .= ")";
        }
      } else {
        $strWhereClause = "";
      }
      return $strWhereClause;
    }
  }
?>