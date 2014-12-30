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

  class PageLink extends Helper {
    var $strCachedTitle;
    function SetTitle($strTitle) {
      $this->strCachedTitle = $strTitle;
    }
    function GetTitle() {
      return $this->strCachedTitle;
    }
    function ViewArea($intID) {
      global $CMS;
      $strTitle = $CMS->PL->GetTitle();
      if (!$strTitle) {
        $strTitle = $CMS->AR->GetSEOTitle($intID);
      }
      $intLinkStyle = $CMS->SYS->GetSysPref(C_PREF_LINK_STYLE);
      if (!$intLinkStyle) {
        $intLinkStyle = "1";
      }
      switch ($intLinkStyle) {
        case "1": $strLink = FN_VIEW."/area/".$intID."/".$strTitle; break;
        case "2": $strLink = URL_ROOT."area/".$intID."/".$strTitle; break;
        case "3":
        case "4":
        case "5":
            $strLink = URL_ROOT.$strTitle."/"; break;
      }
      // Reset
      $CMS->PL->SetTitle("");
      return $strLink;
    }
    function ViewArticle($intID, $intAreaID = 0) {
      global $CMS;
      $strTitle = $this->GetTitle();
      if (!$strTitle) {
        $strTitle = $CMS->ART->GetSEOTitle($intID);
      }
      if (!$intAreaID) {
        $intAreaID = $CMS->ART->GetArticleAreaID($intID);
      }
      $strAreaTitle = $CMS->AR->GetSEOTitle($intAreaID);
      $intLinkStyle = $CMS->SYS->GetSysPref(C_PREF_LINK_STYLE);
      if (!$intLinkStyle) {
        $intLinkStyle = "1";
      }
      switch ($intLinkStyle) {
        case "1": $strLink = FN_VIEW."/article/".$intID."/".$strTitle; break;
        case "2": $strLink = URL_ROOT."article/".$intID."/".$strTitle; break;
        case "3": $strLink = URL_ROOT.$strTitle; break;
        case "4": $strLink = URL_ROOT.$strAreaTitle."/".$strTitle."/"; break;
        case "5":
            $arrArticle  = $CMS->ART->GetArticle($intID);
            $arrDate     = explode(" ", $arrArticle['create_date_raw']);
            $arrDateBits = explode("-", $arrDate[0]);
            $intYear     = $arrDateBits[0];
            $intMonth    = $arrDateBits[1];
            $intDay      = $arrDateBits[2];
            $strLink     = sprintf(URL_ROOT.'%s/%s/%s/%s', $intYear, $intMonth, $intDay, $strTitle);
            break;
      }
      // Reset
      $this->SetTitle("");
      return $strLink;
    }
    function ViewUser($intID) {
      global $CMS;
      // Default link style
      $strUser = $this->GetTitle();
      if (!$strUser) {
        $strUser = $CMS->US->GetSEOTitle($intID);
      }
      $strLink = FN_VIEW."/user/".$intID."/".$strUser;
      // Reset
      $this->SetTitle("");
      return $strLink;
    }
    function ViewComment($intID) {
      global $CMS;
      $strLink = "";
      $arrComment = $CMS->COM->GetComment($intID);
      if ($arrComment['story_id']) {
        $intArticleID = $arrComment['story_id'];
        $strLink = $this->ViewArticle($intArticleID);
        $strLink .= "#c".$intID;
      }
      // Reset
      $this->SetTitle("");
      return $strLink;
    }
  }
?>