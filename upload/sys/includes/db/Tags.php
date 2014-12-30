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

  class Tags extends Helper {
    var $blnUseLinks = false;
    var $intAreaID = 0;
    var $arrTagCache;
    var $arrTagNameCache;
    function Add($strTag) {
      $intTagID = $this->Query("INSERT INTO {IFW_TBL_TAGS}(tag, tag_count, article_list) VALUES('$strTag', 1, '')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $intTagID;
    }
    function Delete($intID) {
      $this->Query("DELETE FROM {IFW_TBL_TAGS} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function UpdateCount($intTagID, $intCount) {
      if (!$intCount) {
        $intCount = 0;
      }
      if ($intTagID) {
        $this->Query("UPDATE {IFW_TBL_TAGS} SET tag_count = $intCount WHERE id = $intTagID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
    }
    function Plus($intTagID, $intArticleID) {
      $dteStartTime = $this->MicrotimeFloat();
      $intCount = $this->GetTagCount($intTagID);
      $intCount++;
      $this->UpdateCount($intTagID, $intCount);
      if ($intArticleID > 0) {
        $this->Retag($intTagID, $intArticleID, true);
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function Minus($intTagID, $intArticleID) {
      $dteStartTime = $this->MicrotimeFloat();
      $intCount = $this->GetTagCount($intTagID);
      $intCount--;
      if ($intCount == 0) {
        $this->Delete($intTagID);
      } else {
        $this->UpdateCount($intTagID, $intCount);
        if ($intArticleID > 0) {
          $this->Retag($intTagID, $intArticleID, false);
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Select ** //
    function Exists($strTag) {
      $arrResult = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_TAGS} WHERE tag = '$strTag'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['count'] > 0 ? true : false;
    }
    function GetID($strTag) {
      $arrResult = $this->ResultQuery("SELECT id FROM {IFW_TBL_TAGS} WHERE tag = '$strTag'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['id'];
    }
    function GetTag($intID) {
      if (!isset($this->arrTagNameCache[$intID])) {
        $arrResult = $this->ResultQuery("SELECT tag FROM {IFW_TBL_TAGS} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $this->arrTagNameCache[$intID] = $arrResult[0]['tag'];
      }
      return $this->arrTagNameCache[$intID];
    }
    function GetTagCount($intID) {
      if (!$intID) {
        return 0;
      } else {
        $arrResult = $this->ResultQuery("SELECT tag_count FROM {IFW_TBL_TAGS} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrResult[0]['tag_count'];
      }
    }
    function GetArticleList($strTag) {
      $arrResult = $this->ResultQuery("SELECT article_list FROM {IFW_TBL_TAGS} WHERE tag = '$strTag'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['article_list'];
    }
    function GetAll() {
      $arrResult = $this->ResultQuery("SELECT tag, tag_count FROM {IFW_TBL_TAGS} ORDER BY tag ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult;
    }
    function GetMaxCount() {
      $arrResult = $this->ResultQuery("SELECT max(tag_count) AS max_tag FROM {IFW_TBL_TAGS}", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return (integer) $arrResult[0]['max_tag'];
    }
    function GetAllTagCounts() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $intTagThreshold = (integer) $CMS->SYS->GetSysPref(C_PREF_TAG_THRESHOLD);
      if (!$intTagThreshold) {
        $intTagThreshold = 1;
      }
      $arrResult = $this->ResultQuery("SELECT tag_count FROM {IFW_TBL_TAGS} GROUP BY tag_count HAVING tag_count >= $intTagThreshold ORDER BY tag_count ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult;
    }
    // ** Add/remove article IDs to/from a tag ** //
    function Retag($intTagID, $intArticleID, $blnInclude) {
      $dteStartTime = $this->MicrotimeFloat();
      if ($intTagID) {
        $arrTempAL = $this->ResultQuery("SELECT article_list FROM {IFW_TBL_TAGS} WHERE id = $intTagID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $arrOldAL = explode(",", $arrTempAL[0]['article_list']);
        $strNewAL = "";
        $intCount = 0;
        for ($i=0; $i<count($arrOldAL); $i++) {
          $strItem = $arrOldAL[$i];
          if ($strItem != $intArticleID) {
            if ($intCount == 0) {
              $strNewAL = $strItem;
            } else {
              $strNewAL .= ",".$strItem;
            }
            $intCount++;
          }
        }
        if ($blnInclude == true) {
          if ($strNewAL) {
            $strNewAL .= ",".$intArticleID;
          } else {
            $strNewAL = $intArticleID;
          }
        }
        $this->Query("UPDATE {IFW_TBL_TAGS} SET article_list = '$strNewAL' WHERE id = $intTagID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Remove all tags from an article ** //
    function RemoveArticleTags($strOldTags, $intContentID) {
      $arrOldTags = explode(",", $strOldTags);
      for ($i=0; $i<count($arrOldTags); $i++) {
        $intTagID = $arrOldTags[$i];
        $this->Minus($intTagID, $intContentID);
      }
    }
    // ** More useful functions ** //
    function BuildIDList($strTagNames, $intContentID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strTagList = "";
      $arrArticleTags = explode(",", $strTagNames);
      for ($i=0; $i<count($arrArticleTags); $i++) {
        $strTag = $arrArticleTags[$i];
        $strTag = trim($strTag);
        if ($strTag) {
          $strTag = $CMS->AddSlashesIFW($strTag);
          $strTag = strtolower($strTag);
          $intTagID = $this->GetID($strTag);
          if ($intTagID == 0) {
            $intTagID = $this->Add($strTag);
            $this->Retag($intTagID, $intContentID, true);
          } else {
            $this->Plus($intTagID, $intContentID);
          }
          if ($i == 0) {
            $strTagList = $intTagID;
          } else {
            $strTagList .= ",".$intTagID;
          }
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strTagList;
    }
    function BuildNameList($strTagIDs) {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->arrTagCache[$this->intAreaID][$strTagIDs])) {
        $strTagList = "";
        $arrArticleTags = explode(",", $strTagIDs);
        for ($i=0; $i<count($arrArticleTags); $i++) {
          $intTagID = $arrArticleTags[$i];
          if ($intTagID) {
            $strTag = $this->GetTag($intTagID);
            if ($this->blnUseLinks == true) {
              $strTagLink = str_replace(" ", "+", $strTag);
              $strItem = "<a href=\"".FN_SEARCH."?go=yes&amp;t=$strTagLink&amp;a=".$this->intAreaID."\">$strTag</a>";
            } else {
              $strItem = $strTag;
            }
            if ($i == 0) {
              $strTagList = $strItem;
            } else {
              $strTagList .= ", ".$strItem;
            }
          }
        }
        $this->arrTagCache[$this->intAreaID][$strTagIDs] = $strTagList;
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->arrTagCache[$this->intAreaID][$strTagIDs];
    }
    function BuildLinkedNameList($strTagIDs, $intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->blnUseLinks = true;
      $this->intAreaID = $intAreaID;
      $strTagList = $this->BuildNameList($strTagIDs);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strTagList;
    }
    function MatchTag($strTagIDs, $strTagToMatch) {
      $dteStartTime = $this->MicrotimeFloat();
      $blnMatch = false;
      $arrArticleTags = explode(",", $strTagIDs);
      for ($i=0; $i<count($arrArticleTags); $i++) {
        $intTagID = $arrArticleTags[$i];
        if ($intTagID) {
          $strTag = $this->GetTag($intTagID);
          if ($strTag == $strTagToMatch) {
            $blnMatch = true;
            break;
          }
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $blnMatch;
    }
  }
?>