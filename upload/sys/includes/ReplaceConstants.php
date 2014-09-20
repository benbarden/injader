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

  class ReplaceConstants extends Helper {
    function Get($prefix) {
      $i = 0;
      foreach (get_defined_constants() as $key=>$value) {
        if (substr($key, 0, strlen($prefix)) == $prefix) {
          $arrKeys[$i]['key_name']  = $key;
          $arrKeys[$i]['key_value'] = $value;
          $i++;
        }
      }
      if (empty($arrKeys)) {
        return "Error: No constants found with prefix '$prefix'";
      } else {
        return $arrKeys;
      }
    }
    function Replace($arrConstants, &$strHTML) {
      for ($i=0; $i<count($arrConstants); $i++) {
        $strKeyName  = '{'.$arrConstants[$i]['key_name'].'}';
        $strKeyValue = $arrConstants[$i]['key_value'];
        $strHTML = str_replace($strKeyName, $strKeyValue, $strHTML);
      }
    }
    function DoAll($strHTML) {
      $arrConstants = $this->Get('C_');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('M_');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('FN');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('AL');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('URL');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('SVR');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('ABS');
      $this->Replace($arrConstants, $strHTML);
      $arrConstants = $this->Get('PRD');
      $this->Replace($arrConstants, $strHTML);
      $strHTML = str_replace(ZZZ_TEMP, '', $strHTML);
      return $strHTML;
    }
    function Preserve($strHTML) {
      $strHTML = str_replace("{", "{".ZZZ_TEMP, $strHTML);
      $strHTML = str_replace("}", ZZZ_TEMP."}", $strHTML);
      return $strHTML;
    }
  }
?>