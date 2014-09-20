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

  class PageNumber extends Helper {
    function GetPageStart($intPerPage, $intPageNo) {
      if ($intPageNo == 1) {
        $intStart = 0;
      } else {
        $intStart = ($intPerPage * ($intPageNo - 1));
      }
      return $intStart;
    }
    function GetPageEnd($intPerPage, $intPageNo) {
      if ($intPageNo == 1) {
        $intEnd = $intPerPage;
      } else {
        $intEnd = ($intPageNo * $intPerPage);
      }
      return $intEnd;
    }
    function ItemsOnPage($intPerPage, $intItemCount) {
      if ($intPerPage > $intItemCount) {
        $intCount = $intItemCount;
      } else {
        $intCount = $intPerPage;
      }
      return $intCount;
    }
    function GetTotalPages($intPerPage, $intAreaCount) {
      if ($intPerPage >= $intAreaCount) {
        $intNumPages = 1;
      } elseif ($intPerPage == 0) {
        $intNumPages = 1;
      } else {
        $intNumPages = (floor($intAreaCount / $intPerPage));
        if (($intAreaCount % $intPerPage) > 0) {
          $intNumPages++;
        }
      }
      return $intNumPages;
    }
  }

?>