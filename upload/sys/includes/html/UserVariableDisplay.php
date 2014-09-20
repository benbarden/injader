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

  class UserVariableDisplay extends Helper {
    function Build($strHTML) {
      global $CMS;
      $intVarCount = $CMS->UV->GetVariableCount();
      if ($intVarCount > 0) {
        $arrVariables = $CMS->UV->GetAllVarDesc();
        for ($i=0; $i<count($arrVariables); $i++) {
          $strVariable = $arrVariables[$i]['user_variable'];
          $strContent  = $arrVariables[$i]['content'];
          if (strpos($strHTML, $strVariable) !== false) {
            $strHTML = str_replace($strVariable, $strContent, $strHTML);
          }
        }
      }
      return $strHTML;
    }
  }

?>