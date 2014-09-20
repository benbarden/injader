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

  class ThemeSetting extends Theme {
    var $arrSettings = array();
    function Get($strName) {
      return empty($this->arrSettings[$strName]) ? "&lt;undefined&gt;" : $this->arrSettings[$strName];
    }
    function Set($strName, $strValue) {
      if (($strName) && ($strValue)) {
        $this->arrSettings[$strName] = $strValue;
      }
    }
  }

?>