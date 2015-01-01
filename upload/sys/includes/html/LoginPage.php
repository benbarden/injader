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

  class LoginPage extends Helper {
    var $strTitle = "Error";
    var $strContent;
    // Main functions
    function SetTitle($strNewTitle) {
      $this->strTitle = $strNewTitle;
    }
    function GetTitle() {
      return $this->strTitle;
    }
    function Display($strContentOverride = "") {
      $dteStartTime = $this->MicrotimeFloat();
      global $CMS;
      $RC = new ReplaceConstants;
      if ($strContentOverride) {
        $strBody = $strContentOverride;
      } else {
        $strBody = $this->strContent;
      }
      $strHTMLToPrint = $this->GetHeader().$strBody.$this->GetFooter();
      $strHTMLToPrint = $RC->DoAll($strHTMLToPrint);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      global $strExecutionTime; // Set in header.php
      if ($strExecutionTime) {
        $strQueryTimeData = <<<ExecTime
<div id="majQueryTimeData">
<p>Query Execution Time</p>
<ul>
$strExecutionTime
</ul>
</div>
ExecTime;
      } else {
        $strQueryTimeData = "";
      }
      global $blnOverrideQT; // Allows the variable to be put in sys templates without being evaluated
      if (!$blnOverrideQT) {
        $strHTMLToPrint = str_replace('$cmsQueryTime', $strQueryTimeData, $strHTMLToPrint);
      }
      print($strHTMLToPrint);
      $CMS->IQ->Disconnect();
      exit;
    }
    function GetHeader() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strPageTitle = $this->GetTitle();
      // Index
      $strIndexURL   = str_replace("index.php", "", FN_INDEX);
      $strHeaderHTML = <<<CMSHeader
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
<title>$strPageTitle</title>
<link rel="stylesheet" type="text/css" href="{URL_ROOT}sys/loginpage.css" />
</head>
<body>
<div id="mPage">

CMSHeader;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHeaderHTML;
    }
    // ** Footer ** //
    function GetFooter() {
      $dteStartTime = $this->MicrotimeFloat();
      $strFooter = <<<Footer
<p style="text-align: center;">Powered by <a href="http://www.injader.com">Injader</a></p>
</div>
</body>
</html>
Footer;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strFooter;
    }
  }
?>