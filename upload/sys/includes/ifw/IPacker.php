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

  class IPacker extends Helper {
    function PackInsert($strTableName, $arrFields) {
      $strInsertInto   = "INSERT INTO $strTableName(";
      $strInsertFields = "";
      $strInsertValues = ") VALUES(";
      $strInsertData   = "";
      for ($i=0; $i<count($arrFields[0]); $i++) {
        $strKey = $arrFields[0][$i];
        if ($strInsertFields) {
          $strInsertFields .= ", $strKey";
        } else {
          $strInsertFields  = "$strKey";
        }
      }
      for ($j=0; $j<count($arrFields[1]); $j++) {
        $strValue = $arrFields[1][$j];
        if ($strInsertData) {
          $strInsertData   .= ", '$strValue'";
        } else {
          $strInsertData    = "'$strValue'";
        }
      }
      $strSQL = $strInsertInto.$strInsertFields.$strInsertValues.$strInsertData.")";
      return $strSQL;
    }
    function PackInsertSQL($strTableName, $arrFields) {
      $strInsertInto   = "INSERT INTO $strTableName(";
      $strInsertFields = "";
      $strInsertValues = ") VALUES(";
      $strInsertData   = "";
      foreach ($arrFields as $strKey => $strValue) {
        if ($strInsertFields) {
          $strInsertFields .= ", $strKey";
          $strInsertData   .= ", '$strValue'";
        } else {
          $strInsertFields  = "$strKey";
          $strInsertData    = "'$strValue'";
        }
      }
      $strSQL = $strInsertInto.$strInsertFields.$strInsertValues.$strInsertData.")";
      return $strSQL;
    }
  }

?>