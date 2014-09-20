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

class PageNumberNavigation extends Helper {
    
    var $blnOneParam = false;
    
    function MakeOneParam($intNumPages, $intPageNumber, $strBaseURL) {
        
        $this->blnOneParam = true;
        $strHTML = $this->Make($intNumPages, $intPageNumber, $strBaseURL);
        return $strHTML;
        
    }
    
    function Make($intNumPages, $intPageNumber, $strBaseURL) {
        
        if (strpos($strBaseURL, "?") !== false) {
            $strSep = "&amp;";
        } elseif ($this->blnOneParam) {
            $strSep = "?";
        } else {
            $strSep = "&amp;";
        }
        
        $strPageNumbers = "<p class=\"pages\"><span class=\"pagedesc\">Pages:</span> ";
        for ($i=1; $i<$intNumPages+1; $i++) {
            // Display current page
            if ($i == $intPageNumber) {
                $strPageNumbers .= " <span class=\"pagelink thispage\">$i</span> ";
            } elseif ($intNumPages <= 10) {
                // Display all page numbers if there's 10 pages or fewer
                $strPageNumbers .= " <span class=\"pagelink\"><a href=\"".$strBaseURL.$strSep."page=$i\">$i</a></span> ";
            } elseif (
                // Get the first two and the last two
                ($i <= 2) || ($i >= ($intNumPages-1)) ||
                // And the numbers two places either side
                ($i == ($intPageNumber-1)) || ($i == ($intPageNumber+1)) ||
                ($i == ($intPageNumber-2)) || ($i == ($intPageNumber+2))
                ) {
                    $strPageNumbers .= " <span class=\"pagelink\"><a href=\"".$strBaseURL.$strSep."page=$i\">$i</a></span> ";
            } else {
                if ((substr($strPageNumbers, -3, 3)) != "...") {
                    $strPageNumbers .= "...";
                }
            }
        }
        $strPageNumbers .= "</p>";
        return $strPageNumbers;
    }
}
?>