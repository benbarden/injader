<?php
/*
  Injader
  Copyright (c) 2005-2015 Ben Barden


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

$strIFWDir = str_replace("\\", "/", dirname(__FILE__)."\\");
//if (file_exists($strIFWDir.'IQuery_Tables_Old.php') == true) {
require 'IQuery_Tables.php';

class IQuery extends IFWCore {
    
    var $objLink;
    var $strQuery;
    var $strResult;
    var $blnMigration = false;
    
    function MicrotimeFloat() {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }
    
    // ** Core system ** //
    function Connect($strDBHost, $strDBSchema, $strDBAdminUser, $strDBAdminPass) {
        @ $link = mysql_connect($strDBHost, $strDBAdminUser, $strDBAdminPass, true);
        if (!$link) {
            @ $this->Disconnect();
            die($this->GetDBErrorText(
                "mysql_connect", 0, __CLASS__ . "::" . __FUNCTION__, __LINE__));
        }
        $this->objLink = $link;
        mysql_select_db($strDBSchema, $this->objLink) or die($this->GetDBErrorText(
            "mysql_select_db", 0, __CLASS__ . "::" . __FUNCTION__, __LINE__));
    }
    
    function Disconnect() {
        mysql_close($this->objLink);
    }
    
    // ** Begin error handling ** //
    function GetDBErrorText($strErrType, $strErrData = 0, $strErrSrc, $intLineNo = 0) {
      switch ($strErrType) {
        // MYSQL //
        case "mysql_connect":
          $strData = "Could not connect: ".mysql_error();
          break;
        case "mysql_select_db":
          $strData = "Could not select database.";
          break;
        case "mysql_query":
          $strData = "Query failed: ".mysql_error().".";
          if ($strErrData != "") {
            $strData .= " </p>\n\n<p>Your query was: $strErrData";
          }
          break;
      }
      $strCSS = URL_ROOT."sys/loginpage.css";
      $strErrorText = <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<title>Database Error</title>
<link rel="stylesheet" type="text/css" href="$strCSS" />
</head>
<body>
<div id="mPage">
<h1>Database Error</h1>
<p>$strData</p>
<p id="err-src"><strong>Source:</strong> $strErrSrc; Line: $intLineNo</p>
</div>
</body>
</html>
END;
      return $strErrorText;
    }
    // ** Replace table constants ** //
    function GetConstants($prefix) {
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
    function ReplaceConstants($arrConstants, &$strHTML) {
      for ($i=0; $i<count($arrConstants); $i++) {
        $strKeyName  = '{'.$arrConstants[$i]['key_name'].'}';
        $strKeyValue = $arrConstants[$i]['key_value'];
        $strHTML = str_replace($strKeyName, $strKeyValue, $strHTML);
      }
    }
    function MakeSQL($strQuery) {
      $arrConstants = $this->GetConstants('IFW_TBL_');
      $this->ReplaceConstants($arrConstants, $strQuery);
      $arrConstants = $this->GetConstants('C_');
      $this->ReplaceConstants($arrConstants, $strQuery);
      return $strQuery;
    }
    // ** Data handling ** //
    function SetQuery($strTempQuery) {
      $this->strQuery = $this->MakeSQL($strTempQuery);
    }
    function GetQuery() {
      return $this->strQuery;
    }
    function SetResult($strTempResult) {
      $this->strResult = $strTempResult;
    }
    function GetResult() {
      return $this->strResult;
    }
    function SplitQuery($strTempQuery) {
      if (empty($strTempQuery)) {
        return "";
      }
      $strSplitter = ";";
      $intPos = strpos($strTempQuery, $strSplitter);
      if ($intPos === false) {
        $arrQuery = array(0 => $strTempQuery);
      } else {
        $arrQuery = explode($strSplitter, $strTempQuery);
      }
      return $arrQuery;
    }
    function SplitMigrationQuery($strTempQuery) {
      if (empty($strTempQuery)) {
        return "";
      }
      // Fix up certain WordPress queries
      $strTempQuery = str_replace("; \n", ";\n", $strTempQuery);
      $strSplitter = ";\n";
      $intPos = strpos($strTempQuery, $strSplitter);
      if ($intPos === false) {
        $arrQuery = array(0 => $strTempQuery);
      } else {
        $arrQuery = explode($strSplitter, $strTempQuery);
      }
      return $arrQuery;
    }
    function DoSingleQuery($strTempQuery = "", $strSource = "", $intLineNo = 0) {
      if ($strTempQuery) {
        $this->SetQuery($strTempQuery);
      }
      if ($strSource == "") {
        $strSource = "(unknown)";
      }
      $dteStartTime = $this->MicrotimeFloat();
      if (trim($this->GetQuery())) {
        $strTempResult = mysql_query($this->GetQuery(), $this->objLink) or die($this->GetDBErrorText("mysql_query", $this->GetQuery(), $strSource, $intLineNo));
        if (!$strTempResult) {
          $dteEndTime = $this->MicrotimeFloat();
          $this->SetExecutionTime($dteStartTime, $dteEndTime, "*DB* ".$strSource, $intLineNo);
          return 0;
        }
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, $strSource, $intLineNo);
      return mysql_insert_id();
    }
    
    function DoResultQuery($strTempQuery = "", $strSource = "", $intLineNo = 0) {
        $arrResult = "";
        if (!empty($strTempQuery)) {
            $this->SetQuery($strTempQuery);
        }
        $arrQueryData = $this->SplitQuery($this->GetQuery());
        $intCounter = 0;
        $dteStartTime = $this->MicrotimeFloat();
        for ($i=0; $i<count($arrQueryData); $i++) {
            if (!empty($arrQueryData[$i])) {
                $strTempResult = mysql_query($arrQueryData[$i], $this->objLink) or die(
                    $this->GetDBErrorText(
                    "mysql_query", $arrQueryData[$i], $strSource, $intLineNo));
                while ($arrData = mysql_fetch_array($strTempResult, MYSQL_ASSOC)) {
                    $arrResult[$intCounter++] = $this->ArrayStripSlashes($arrData);
                }
            }
        }
        $dteEndTime = $this->MicrotimeFloat();
        $this->SetExecutionTime($dteStartTime, $dteEndTime, "*DB* ".$strSource, $intLineNo);
        $this->SetResult($arrResult);
        if (!empty($arrResult)) {
            return $this->GetResult();
        }
    }
    
    function DoMigrationMultiQuery($strTempQuery = "", $strSource = "", $intLineNo = 0) {
      $this->blnMigration = true;
      $this->DoMultiQuery($strTempQuery, $strSource, $intLineNo);
    }
    function DoMultiQuery($strTempQuery = "", $strSource = "", $intLineNo = 0) {
      if ($strTempQuery) {
        $this->SetQuery($strTempQuery);
      }
      if ($strSource == "") {
        $strSource = "(unknown)";
      }
      if ($this->blnMigration) {
        $arrQueryData = $this->SplitMigrationQuery($this->GetQuery());
      } else {
        $arrQueryData = $this->SplitQuery($this->GetQuery());
      }
      for ($i=0; $i<count($arrQueryData); $i++) {
        if (trim($arrQueryData[$i])) {
          $strTempResult = mysql_query($arrQueryData[$i], $this->objLink) or die($this->GetDBErrorText("mysql_query", $arrQueryData[$i], __CLASS__ . "::" . __FUNCTION__, $intLineNo));
          if (!$strTempResult) {
            return false;
          }
        }
      }
      return true;
    }
  }  
?>