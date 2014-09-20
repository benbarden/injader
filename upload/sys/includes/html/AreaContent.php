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

  class AreaContent extends Helper {
    var $arrResult = "";
    var $strSQL = "";
    var $intCurrentID = 0;
    var $intCurrentIndex = 0;
    var $intNavID = 0;
    var $strNavTitle = "";
    var $strDirection = "";
    var $strAreaType = "";
    function Build($intAreaID, $intItemID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $this->intCurrentID = $intItemID;
      $arrSortRule = $CMS->ResultQuery("SELECT sort_rule FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strTempSortRule = $arrSortRule[0]['sort_rule'];
      $strSortRule = $CMS->BuildAreaSortRule($strTempSortRule);
      $strUserTable = strpos($strSortRule, "username") !== false ? "LEFT JOIN {IFW_TBL_USERS} u ON con.author_id = u.id" : "";
      if (strpos($strSortRule, 'ASC') !== false) {
        $this->strDirection = "ASC";
      } else {
        $this->strDirection = "DESC";
      }
      // Per-article permissions
      $intCurrentUserID = $CMS->RES->GetCurrentUserID();
      if ($intCurrentUserID) {
        $strCurrentUserGroups = $CMS->US->GetUserGroups($intCurrentUserID);
      } else {
        $strCurrentUserGroups = "";
      }
      $strSortRule = $CMS->UG->BuildUserGroupSQL("con", $strCurrentUserGroups, false, true).$strSortRule;
      // Query builder
      if ($this->strSQL) {
        $this->strSQL = str_replace("\$strSortRule", $strSortRule, $this->strSQL);
        $this->strSQL = str_replace("\$strUserTable", $strUserTable, $this->strSQL);
        $this->arrResult = $CMS->ResultQuery($this->strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      } else {
        $strMajQuery = str_replace("\$strUserTable", $strUserTable, "SELECT con.id, title, create_date AS create_date_raw FROM ({IFW_TBL_CONTENT} con, {IFW_TBL_AREAS} a) \$strUserTable WHERE con.content_area_id = a.id AND con.content_area_id = $intAreaID AND content_status = '{C_CONT_PUBLISHED}' $strSortRule");
        $this->arrResult = $CMS->ResultQuery($strMajQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
      $this->GetCurrentIndex();
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function BuildRelated($intAreaID, $intContentID, $intLimit) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->strSQL = "SELECT con.id, title, create_date AS create_date_raw FROM ({IFW_TBL_CONTENT} con, {IFW_TBL_AREAS} a) WHERE con.content_area_id = a.id AND con.id <> $intContentID AND con.content_area_id = $intAreaID AND content_status = '{C_CONT_PUBLISHED}' \$strSortRule LIMIT $intLimit";
      $this->Build($intAreaID, $intContentID);
      $this->strSQL = "";
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function BuildSimilarArticles($strTagList, $intContentID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $this->arrResult = $this->ResultQuery("SELECT article_list FROM {IFW_TBL_TAGS} WHERE id IN ($strTagList)", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($this->arrResult); $i++) {
        $strArticleList = $this->arrResult[$i]['article_list'];
        $arrArticleList = explode(",", $strArticleList);
        for ($j=0; $j<count($arrArticleList); $j++) {
          $intID = $arrArticleList[$j];
          if ($CMS->ART->IsPublished($intID)) {
            if (($intID <> "") && ($intID <> $intContentID)) {
              $intAreaID = $CMS->ART->GetArticleAreaID($intID);
              if ($intAreaID) {
                if (isset($arrViewArea[$intAreaID])) {
                  if ($arrViewArea[$intAreaID]) {
                    if (!empty($arrArticles[$intID])) {
                      $arrArticles[$intID]++;
                    } else {
                      $arrArticles[$intID] = 1;
                    }
                  }
                } else {
                  $CMS->RES->ViewArea($intAreaID);
                  if ($CMS->RES->IsError()) {
                    $arrViewArea[$intAreaID] = false; // Caching
                  } else {
                    $arrViewArea[$intAreaID] = true; // Caching
                    if (!empty($arrArticles[$intID])) {
                      $arrArticles[$intID]++;
                    } else {
                      $arrArticles[$intID] = 1;
                    }
                  }
                }
              }
            }
          }
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if (!empty($arrArticles)) {
        return $arrArticles;
      } else {
        return "";
      }
    }
    function GetCurrentIndex() {
      $dteStartTime = $this->MicrotimeFloat();
      for ($i=0; $i<count($this->arrResult); $i++) {
        if ($this->arrResult[$i]['id'] == $this->intCurrentID) {
          $this->intCurrentIndex = $i;
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function Next() {
      $dteStartTime = $this->MicrotimeFloat();
      $j = $this->intCurrentIndex + 1;
      if ($j >= count($this->arrResult)) {
        $this->intNavID    = 0;
        $this->strNavTitle = "";
      } else {
        $this->intNavID    = $this->arrResult[$j]['id'];
        $this->strNavTitle = $this->arrResult[$j]['title'];
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function Prev() {
      $dteStartTime = $this->MicrotimeFloat();
      $j = $this->intCurrentIndex - 1;
      if ($j < 0) {
        $this->intNavID    = 0;
        $this->strNavTitle = "";
      } else {
        $this->intNavID    = $this->arrResult[$j]['id'];
        $this->strNavTitle = $this->arrResult[$j]['title'];
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function GetNext() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      if ($this->strDirection == "ASC") {
        $this->Next();
      } else {
        $this->Prev();
      }
      $strLink = "";
      if ($this->intNavID > 0) {
        $strLinkURL = $CMS->PL->ViewArticle($this->intNavID);
        $strLink = "<a href=\"$strLinkURL\">".$this->strNavTitle." &gt;</a>";
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strLink;
    }
    function GetPrev() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      if ($this->strDirection == "ASC") {
        $this->Prev();
      } else {
        $this->Next();
      }
      $strLink = "";
      if ($this->intNavID > 0) {
        $strLinkURL = $CMS->PL->ViewArticle($this->intNavID);
        $strLink = "<a href=\"$strLinkURL\">&lt; ".$this->strNavTitle."</a>";
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strLink;
    }
  }

?>