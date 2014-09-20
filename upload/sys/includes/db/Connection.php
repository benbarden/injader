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

  class Connection extends Helper {
    // ** Core functions ** //
    function Create($strName, $strHost, $strSchema, $strUser, $strPass) {
      $intID = $this->Query("INSERT INTO {IFW_TBL_CONNECTIONS}(conn_name, conn_host, conn_schema, conn_user, conn_pass) VALUES('$strName', '$strHost', '$strSchema', '$strUser', '$strPass')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $intID;
    }
    function Edit($intID, $strName, $strHost, $strSchema, $strUser, $strPass) {
      $this->Query("UPDATE {IFW_TBL_CONNECTIONS} SET conn_name = '$strName', conn_host = '$strHost', conn_schema = '$strSchema', conn_user = '$strUser', conn_pass = '$strPass' WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Select ** //
    function GetAll() {
      $arrConn = $this->ResultQuery("SELECT * FROM {IFW_TBL_CONNECTIONS} ORDER BY conn_name ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrConn;
    }
    function Get($intID) {
      $arrConn = $this->ResultQuery("SELECT * FROM {IFW_TBL_CONNECTIONS} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrConn[0];
    }
    function GetConnectionCount() {
      $arrConn = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_CONNECTIONS}", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrConn[0]['count'];
    }
  }

?>