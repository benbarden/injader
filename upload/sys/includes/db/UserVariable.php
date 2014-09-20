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

  class UserVariable extends Helper {
    // ** Core functions ** //
    function Create($strName, $strContent, $strVariable) {
      global $CMS;
      $intID = $this->Query("INSERT INTO {IFW_TBL_USER_VARIABLES}(name, content, user_variable) VALUES('$strName', '$strContent', '$strVariable')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_VARIABLE_CREATE, $intID, $strName);
      return $intID;
    }
    function Edit($intID, $strName, $strContent, $strVariable) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_USER_VARIABLES} SET name = '$strName', content = '$strContent', user_variable = '$strVariable' WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_VARIABLE_EDIT, $intID, $strName);
    }
    function Delete($intID) {
      global $CMS;
      $arrVariable = $this->Get($intID);
      $strName = $arrVariable['name'];
      $this->Query("DELETE FROM {IFW_TBL_USER_VARIABLES} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_USER_VARIABLE_DELETE, $intID, $strName);
    }
    // ** Select ** //
    function GetAll() {
      $arrVariables = $this->ResultQuery("SELECT * FROM {IFW_TBL_USER_VARIABLES} ORDER BY name ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrVariables;
    }
    function GetAllVarDesc() {
      $arrVariables = $this->ResultQuery("SELECT * FROM {IFW_TBL_USER_VARIABLES} ORDER BY LENGTH(user_variable) DESC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrVariables;
    }
    function Get($intID) {
      $arrVariable = $this->ResultQuery("SELECT * FROM {IFW_TBL_USER_VARIABLES} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrVariable[0];
    }
    function GetContentByVar($strVarName) {
      $arrVariable = $this->ResultQuery("SELECT content FROM {IFW_TBL_USER_VARIABLES} WHERE user_variable = '$strVarName'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrVariable[0]['content'];
    }
    function VarExists($strVarName, $intID) {
      if ($intID) {
        $strWhereClause = " AND id <> $intID";
      } else {
        $strWhereClause = "";
      }
      $arrVariable = $this->ResultQuery("SELECT id FROM {IFW_TBL_USER_VARIABLES} WHERE UPPER(user_variable) = UPPER('$strVarName') $strWhereClause", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return !empty($arrVariable[0]) ? true : false;
    }
    function GetVariableCount() {
      $arrCount = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USER_VARIABLES}", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrCount[0]['count'];
    }
  }

?>