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

  class DropDown extends Helper {
    var $blnIncludeDefault;
    var $blnAllowEmpty;
    var $blnIsSearch;
    var $strEmptyItem;
    // ** Generic Code ** //
    function BasicList($arrListItems, $strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      if (!empty($this->blnAllowEmpty) && ($this->blnAllowEmpty == true)) {
        $strOutput = "<option value=\"0\">(".$this->strEmptyItem.")</option>\n";
      } else {
        $strOutput = "";
      }
      $intStartAt = !empty($arrListItems[0]) ? 0 : 1;
      for ($i=$intStartAt; $i<count($arrListItems); $i++) {
        $strValue = $arrListItems[$i]['list_value'];
        $strText  = $arrListItems[$i]['list_text'];
        if ($strSelValue) {
          if ($strValue == $strSelValue) {
            $strSelected = " selected=\"selected\"";
          } else {
            $strSelected = "";
          }
        } else {
          $strSelected = "";
        }
        $strOutput .= "<option value=\"$strValue\"$strSelected>$strText</option>\n";
      }
      $this->blnAllowEmpty = false;
      $this->strEmptyItem = "";
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strOutput;
    }
    // ** Area list ** //
    function AreaHierarchy($strSelValue, $intExcludeAreaID, $strAreaType, $blnAllowEmpty, $blnIsSearch) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $this->blnAllowEmpty = $blnAllowEmpty;
      if (empty($this->strEmptyItem)) {
        $this->strEmptyItem = "All";
      }
      $this->blnIsSearch = $blnIsSearch;
      $arrDDLAreas = $CMS->AT->BuildAreaArray(1); // Just get the values
      $j = 0; // List count
      for ($i=0; $i<count($arrDDLAreas); $i++) {
        $intDDLID    = $arrDDLAreas[$i]['id'];
        $strDDLName  = $arrDDLAreas[$i]['name'];
        $strDDLType  = $arrDDLAreas[$i]['type'];
        $intDDLLevel = $arrDDLAreas[$i]['level'];
        $blnProceed = false;
        if (($intDDLID <> $intExcludeAreaID) && ($strDDLType <> "Smart") && ($strDDLType <> "Linked")) {
          if ($strAreaType == "All") {
            $blnProceed = true;
          } elseif ($strDDLType == $strAreaType) {
            if ($this->blnIsSearch) {
              $blnProceed = true;
            } elseif ($strAreaType == "Content") {
              $CMS->RES->CreateArticle($intDDLID);
              $blnProceed = $CMS->RES->IsError() ? false : true;
            } elseif ($strAreaType == "File") {
              $CMS->RES->UploadFile($intDDLID);
              $blnProceed = $CMS->RES->IsError() ? false : true;
            }
          }
          if ($blnProceed) {
            $strDDLNameIndent = " ";
            if ($intDDLLevel > 1) {
              for ($k=1; $k<$intDDLLevel; $k++) {
                $strDDLNameIndent .= "- ";
              }
            }
            $arrListData[$j]['list_value'] = $intDDLID;
            $arrListData[$j]['list_text']  = $strDDLNameIndent.$strDDLName;
            $j++;
          }
        }
      }
      if (empty($arrListData)) {
        $strHTML = $this->BasicList("", $strSelValue);
      } else {
        $strHTML = $this->BasicList($arrListData, $strSelValue);
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** Attach File JS ** //
    function AreaHierarchyAttachJS() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $arrAreas = $CMS->AT->BuildAreaArray(1); // Just get the values
      $arrListData = "";
      $j = 0; // List count
      for ($i=0; $i<count($arrAreas); $i++) {
        $intID   = $arrAreas[$i]['id'];
        $strType = $arrAreas[$i]['type'];
        $blnProceed = false;
        if ($strType == "Content") {
          $CMS->RES->AttachFile($intID);
          $blnProceed = $CMS->RES->IsError() ? false : true;
          if ($blnProceed) {
            // Check it isn't already in the array
            $blnProceed = true;
            if ($j > 0) {
              for ($k=0; $k<count($arrListData); $k++) {
                if ($arrListData[$k] == $intID) {
                  $blnProceed = false;
                  break;
                }
              }
            }
            if ($blnProceed) {
              $arrListData[$j] = $intID;
              $j++;
            }
          }
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrListData;
    }
    function GetAttachArrayJS($arrAttachAreas, $strSuffix) {
      $strHTML = "";
      $strAreaArray = "";
      $strCanAttachFile = "";
      if (!is_array($arrAttachAreas) || count($arrAttachAreas) == 0) {
        $strCanAttachFile = "N";
        $strAreaArray  = "  arrAttachAreas$strSuffix = new Array();\n";
      } elseif (count($arrAttachAreas) == 1) {
        $strCanAttachFile = "Y";
        $strAreaArray  = "  arrAttachAreas$strSuffix = new Array();\n";
        $strAreaArray .= "  arrAttachAreas$strSuffix[0] = ".$arrAttachAreas[0].";\n";
      } else {
        $strCanAttachFile = "Y";
        $strAreaArray = "  arrAttachAreas$strSuffix = new Array(";
        for ($i=0; $i<count($arrAttachAreas); $i++) {
          $intID = $arrAttachAreas[$i];
          if ($i == 0) {
            $strAreaArray .= "$intID";
          } else {
            $strAreaArray .= ", $intID";
          }
        }
        $strAreaArray .= ");\n";
      }
      //   document.getElementById('txtCanAttachFile').value = '$strCanAttachFile';
      $strHTML .= $strAreaArray;
      return $strHTML;
    }
    // ** Date/Time ** //
    function SystemDateFormat($strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrDates = array(
      1 => array("list_value" => 1, "list_text" => "MMM DD, YYYY - December 31, 2020"),
      2 => array("list_value" => 2, "list_text" => "DD MMM, YYYY - 31 December, 2020"),
      3 => array("list_value" => 3, "list_text" => "DD/MM/YYYY - 31/12/2020"),
      4 => array("list_value" => 4, "list_text" => "MM/DD/YYYY - 12/31/2020"),
      5 => array("list_value" => 5, "list_text" => "YYYY/MM/DD - 2020/12/31"),
      6 => array("list_value" => 6, "list_text" => "YYYY-MM-DD - 2020-12-31"),
      7 => array("list_value" => 7, "list_text" => "YYYY/DD/MM - 2020/31/12"),
      8 => array("list_value" => 8, "list_text" => "YYYY-DD-MM - 2020-31-12")
      );
      $strHTML = $this->BasicList($arrDates, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    function SystemTimeFormat($strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrTimes = array(
      0 => array("list_value" => 0, "list_text" => "None - do not display time"),
      1 => array("list_value" => 1, "list_text" => "24 Hour - 23:55"),
      2 => array("list_value" => 2, "list_text" => "24 Hour with seconds - 23:55:00"),
      3 => array("list_value" => 3, "list_text" => "12 Hour - 11:55PM")
      );
      $strHTML = $this->BasicList($arrTimes, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** Permission Profiles ** //
    function PermissionProfile($strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrProfiles = $this->ResultQuery("SELECT id AS list_value, name AS list_text FROM {IFW_TBL_PERMISSION_PROFILES} WHERE is_system = 'N' ORDER BY name ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strHTML = $this->BasicList($arrProfiles, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** Themes ** //
    function ThemeList($strSelValue) {
      global $CMS;
      $arrDirList = $CMS->Dir(ABS_ROOT.'themes/user/', "folders", false);
      $strDirList = "";
      if (is_array($arrDirList)) {
        for ($i=0; $i<count($arrDirList); $i++) {
          $strDir = $arrDirList[$i];
          $arrThemes[$i]['list_value'] = $strDir;
          $arrThemes[$i]['list_text']  = $strDir;
        }
        $strHTML = $this->BasicList($arrThemes, $strSelValue);
      } else {
        $strHTML = "";
      }
      return $strHTML;
    }
    // ** Users ** //
    function UserGroup($strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrUserGroups = $this->ResultQuery("SELECT id AS list_value, name AS list_text FROM {IFW_TBL_USER_GROUPS} ORDER BY list_text ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strHTML = $this->BasicList($arrUserGroups, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    function UserListNone($intSelectedID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->blnAllowEmpty = true;
      $this->strEmptyItem = "All";
      $strHTML = $this->UserList($intSelectedID);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    function UserList($strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrUsers = $this->ResultQuery("SELECT id AS list_value, CONCAT(username, ' (', forename, ' ', surname, ')') AS list_text FROM {IFW_TBL_USERS} ORDER BY username ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strHTML = $this->BasicList($arrUsers, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** Sort Rules ** //
    function SortRuleField($strSelValue) {
      global $CMS;
      $arrRuleFields = $CMS->GetSortRuleFields();
      $strHTML = "<option value=\"0\">Default</option>\n";
      for ($i=0; $i<count($arrRuleFields); $i++) {
        $arrRuleFields[$i]['list_value'] = $arrRuleFields[$i]['value'];
        $arrRuleFields[$i]['list_text']  = $arrRuleFields[$i]['text'];
      }
      $strHTML .= $this->BasicList($arrRuleFields, $strSelValue);
      return $strHTML;
    }
    function SortRuleOrder($strSelValue) {
      global $CMS;
      $arrRuleOrder = $CMS->GetSortRuleOrder();
      for ($i=0; $i<count($arrRuleOrder); $i++) {
        $arrRuleOrder[$i]['list_value'] = $arrRuleOrder[$i]['value'];
        $arrRuleOrder[$i]['list_text']  = $arrRuleOrder[$i]['text'];
      }
      $strHTML = $this->BasicList($arrRuleOrder, $strSelValue);
      return $strHTML;
    }
    // ** Tags ** //
    function TagList($strSelValue) {
      $dteStartTime = $this->MicrotimeFloat();
      $arrTags = $this->ResultQuery("SELECT id AS list_value, tag AS list_text FROM {IFW_TBL_TAGS} ORDER BY tag ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strHTML = "<option value=\"0\">(none)</option>\n";
      $strHTML .= $this->BasicList($arrTags, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** View Users ** //
    function UserOrderField($strSelValue) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $arrListData = array(
        array('list_value' => "id", 'list_text' => "ID"),
        array('list_value' => "username", 'list_text' => "Username")
      );
      $strHTML = $this->BasicList($arrListData, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** View Users ** //
    function FileOrderField($strSelValue) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $arrListData = array(
        array('list_value' => "id", 'list_text' => "ID"),
        array('list_value' => "title", 'list_text' => "Title"),
        array('list_value' => "create_date", 'list_text' => "Creation Date")
      );
      $strHTML = $this->BasicList($arrListData, $strSelValue);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** Access Log ** //
    function AccessLogTags($strSelValue) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $arrTags = $CMS->RC->Get("AL_TAG"); // Can't use AL as Hostgator uses ALT_DIGITS (131119)
      sort($arrTags);
      for ($i=0; $i<count($arrTags); $i++) {
        $arrAccessLogTags[$i]['list_value'] = $arrTags[$i]['key_value'];
        $arrAccessLogTags[$i]['list_text']  = $arrTags[$i]['key_value'];
      }
      $this->strEmptyItem = "Display all";
      $this->blnAllowEmpty = true;
      $strStartList = "<select id=\"tag\" name=\"tag\">\n";
      $strEndList = "\n</select>";
      $strHTML = $strStartList.$this->BasicList($arrAccessLogTags, $strSelValue).$strEndList;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    // ** Content Status ** //
    function ContentStatus($strSelValue) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $arrStatusList = $CMS->RC->Get("C_CONT_");
      sort($arrStatusList);
      for ($i=0; $i<count($arrStatusList); $i++) {
        $arrStatusValues[$i]['list_value'] = $arrStatusList[$i]['key_value'];
        $arrStatusValues[$i]['list_text']  = $arrStatusList[$i]['key_value'];
      }
      $this->strEmptyItem = "Display all";
      $this->blnAllowEmpty = true;
      $strStartList = "<select id=\"status\" name=\"status\">\n";
      $strEndList = "\n</select>";
      $strHTML = $strStartList.$this->BasicList($arrStatusValues, $strSelValue).$strEndList;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
    
  }
