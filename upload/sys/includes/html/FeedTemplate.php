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

  class FeedTemplate extends Helper {
    function Build($arrFeedItems, $strRSSTitle, $intFeedLimit) {
      $dteStartTime = $this->MicrotimeFloat();
      $strWrapperID = str_replace(" ", "-", $strRSSTitle);
      $strHTML  = "<div id=\"$strWrapperID-feedwrapper\" class=\"feedwrapper\">\n";
      $strHTML .= "<p class=\"feedtitle\">$strRSSTitle</p>\n";
      if (count($arrFeedItems) > 0) {
        if ($intFeedLimit > count($arrFeedItems)) {
          $intFeedLimit = count($arrFeedItems);
        }
        for ($i=1; $i<$intFeedLimit+1; $i++) {
          $strLink  = str_replace("\n", "", $arrFeedItems[$i]['LINK']);
          $strTitle = str_replace("\n", "", $arrFeedItems[$i]['TITLE']);
          $strDesc  = str_replace("\n", "", $arrFeedItems[$i]['DESCRIPTION']);
          $strIntro = str_replace("&nbsp;", " ", $strDesc);
          $strIntro = str_replace("  ", " ", $strIntro);
          $strIntro = strip_tags($strIntro);
          $strIntro = substr($strIntro, 0, 300);
          if (strlen($strDesc) > 300) {
            $strIntro .= "...";
          }
          $strHTML .= <<<FeedItem
<div class="feeditem">
<span class="feeditem-title"><a href="$strLink">$strTitle</a></span>
<span class="feeditem-intro">$strIntro</span>
<span class="feeditem-content">$strDesc</span>
</div>

FeedItem;
        }
      }
      $strHTML .= "</div>\n\n";
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }
  }

?>