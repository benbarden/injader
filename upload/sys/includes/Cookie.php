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

  class Cookie extends Helper {
    function Clear($strName) {
      setcookie($strName, "", time()-42000);
    }
    function Get($strName) {
	  global $CMS;
      if (empty($_COOKIE[$strName])) {
	    $cv = "";
	  } else {
		$cv = $CMS->FilterAlphanumeric($_COOKIE[$strName], "\-");
	  }
	  return $cv;
    }
    function Set($strName, $strValue, $intDuration) {
	  global $CMS;
	  $strName = $CMS->FilterAlphanumeric($strName, "\-");
      $strValue = $CMS->FilterAlphanumeric($strValue, "\-");
	  $blnValidate = setcookie($strName, $strValue, $intDuration);
      if (!$blnValidate) {
        exit("Error: Cannot set cookie.");
      }
    }
  }

?>