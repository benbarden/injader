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

  class System extends Helper {
  	
    // Caching
    var $strDateAndTime;
    var $dteSysDate;
    var $dteCurrentDateAndTime;
    
    // ** Caching ** //
    var $arrSysPrefs;
    
    /**
     * Used to create the cache in the event it doesn't exist
     * @return void
     */
    function FirstCacheBuild() {
    	
    	global $CMS;
    	
    	if (!$CMS->CacheFile->Exists("System_SysPrefs")) {
    		$this->RebuildCache();
    	}
    	
    }
    
    /**
     * Used to forcefully rebuild the System class cache from the database
     * @return void
     */
    function RebuildCache() {
    	
    	global $CMS;
    	
    	$this->arrSysPrefs = array();
    	
      $arrResult = $CMS->ResultQuery("SELECT * FROM {IFW_TBL_SETTINGS}",
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrResult); $i++) {
        $strPrefName = $arrResult[$i]['preference'];
        $strContent  = $arrResult[$i]['content'];
        $this->arrSysPrefs[$strPrefName] = $strContent;
      }
    	
      $CMS->CacheBuild->ArrayBuild("System_SysPrefs", $this->arrSysPrefs);
    	
    }
    
    // ** Core code ** //
    function GetAllSysPrefs() {
    	
    	global $CMS;
    	
    	$this->FirstCacheBuild();
    	$this->arrSysPrefs = $CMS->CacheBuild->ArrayGet("System_SysPrefs");
    	
    }
    
    function GetSysPref($strPref) {
    	
    	if (!is_array($this->arrSysPrefs)) {
    		$this->GetAllSysPrefs();
    	} elseif (!array_key_exists($strPref, $this->arrSysPrefs)) {
        $this->GetAllSysPrefs();
      }
      return $this->arrSysPrefs[$strPref];
      
    }
    
    function WriteSysPref($strPref, $strContent) {
    	
      $this->Query("UPDATE {IFW_TBL_SETTINGS} ".
        "SET content = '$strContent' WHERE preference = '$strPref'", 
        __CLASS__ . "::" . __FUNCTION__ . "; Pref = $strPref", __LINE__);
      
      $this->RebuildCache();
      
    }
    
    // Logging
    function Log($strData) {
      $dteStartTime = $this->MicrotimeFloat();
      @ $fp = fopen(ABS_SYS_ROOT."debug.log", "a");
      @ fwrite($fp, $strData);
      @ fclose($fp);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // *** Access Logs - Core Code *** //
    // AJAX handler should override the IP address
    function CreateAccessLog($strDetail, $strTag, $intUserID = 0, $intUserIP = "") {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strDetail  = $CMS->AddSlashesIFW($strDetail);
      $strTag     = $CMS->AddSlashesIFW($strTag);
      $dteLogDate = $this->GetCurrentDateAndTime();
      if (!$intUserID) {
        $intUserID = 0;
      }
      if (!$intUserIP) {
        $intUserIP = $_SERVER['REMOTE_ADDR'];
      }
      $this->Query("INSERT INTO {IFW_TBL_ACCESS_LOG}(user_id, detail, tag, log_date, ip_address) VALUES($intUserID, '$strDetail', '$strTag', '$dteLogDate', '$intUserIP')", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // *** Error Logs - Core Code *** //
    function CreateErrorLog($strDetail, $strTag, $intUserID = 0, $intHTTPErrorCode = 0) {
      global $CMS;
      // Build file path
      $strLogFile = ABS_ROOT."data/logs/error-".date('Y').date('m').".log";
      // Store data
      $dteLogDate = $this->GetCurrentDateAndTime();
      if (!$intHTTPErrorCode) {
        $intHTTPErrorCode = "-";
      }
      if (!$strDetail) {
        $strDetail = "-";
      }
      if (!$intUserID) {
        $intUserID = 0;
      }
      $strUserIP      = empty($_SERVER['REMOTE_ADDR']) ? "-" : $_SERVER['REMOTE_ADDR'];
      $strPageURL     = empty($_SERVER['REQUEST_URI']) ? "-" : $_SERVER['REQUEST_URI'];
      $strPageURL     = $CMS->AddSlashesIFW($strPageURL);
      $strPageURL     = strip_tags($strPageURL);
      $strReferrerURL = empty($_SERVER['HTTP_REFERER']) ? "-" : $_SERVER['HTTP_REFERER'];
      $strReferrerURL = $CMS->AddSlashesIFW($strReferrerURL);
      $strReferrerURL = strip_tags($strReferrerURL);
      // Check existing file
      @ $strEData = file_get_contents($strLogFile);
      if ($strEData) {
        if (strpos($strEData, "\r\n") !== false) {
          $strLogData = "\r\n"; // Windows
        } elseif (strpos($strEData, "\r") !== false) {
          $strLogData = "\r"; // Mac
        } elseif (strpos($strEData, "\n") !== false) {
          $strLogData = "\n"; // Linux
        } else {
          $strLogData = "\r\n"; // Default
        }
      } else {
        $strLogData = "DATE:ERRCODE:DETAIL:USER:URL:REFERRER:IP:\r\n";
      }
      // Delimeter
      $strDelim = "\t"; // Tab
      // Build log data
      $strLogData .= $dteLogDate.$strDelim.$intHTTPErrorCode.$strDelim
        .$strDetail.$strDelim.$intUserID.$strDelim
        .$strPageURL.$strDelim.$strReferrerURL.$strDelim.$strUserIP;
      // Write to logfile
      @ $fp = fopen($strLogFile, "a");
      @ fwrite($fp, $strLogData);
      @ fclose($fp);
    }
    // ** Cookies ** //
    function GetCookieDuration() {
      $intCookieDays = $this->GetSysPref(C_PREF_COOKIE_DAYS);
      $intCookieDuration = time()+60*60*24*$intCookieDays;
      return $intCookieDuration;
    }
    function GetGuestCookieDuration() {
      $intCookieDuration = time()+60*60*24*365; // Expires after a year
      return $intCookieDuration;
    }
    function GetCookieExpiry() {
      $dteStartTime = $this->MicrotimeFloat();
      $now = time();
      $intOffset = $this->GetSysPref(C_PREF_SERVER_TIME_OFFSET);
      $intCookieDays = $this->GetSysPref(C_PREF_COOKIE_DAYS);
      $dteExpiryDate = mktime(date("H")+$intOffset, date("i"), date("s"), date("m", $now), date("d", $now)+$intCookieDays, date("Y", $now));
      $dteExpiryDate = date('Y-m-d H:i:s', $dteExpiryDate);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $dteExpiryDate;
    }
    // ** Date and time ** //
    function GetSysDate() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->dteSysDate)) {
        $intOffset = $this->GetSysPref(C_PREF_SERVER_TIME_OFFSET);
        $dteDate = mktime(date("H")+$intOffset, date("i"), date("s"), date("m"), date("d"), date("Y"));
        $this->dteSysDate = $dteDate;
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->dteSysDate;
    }
    function GetCurrentDateAndTime() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->dteCurrentDateAndTime)) {
        $dteDate = $this->GetSysDate();
        $dteCurrentDate = date('Y-m-d H:i:s', $dteDate);
        $this->dteCurrentDateAndTime = $dteCurrentDate;
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->dteCurrentDateAndTime;
    }
    function GetCurrentDate() {
      $dteStartTime = $this->MicrotimeFloat();
      $dteDate = $this->GetSysDate();
      $dteCurrentDate = date('Y-m-d', $dteDate);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $dteCurrentDate;
    }
    function GetDateFormat() {
      $dteStartTime = $this->MicrotimeFloat();
      if (!isset($this->strDateAndTime)) {
        $intDateFormatID = $this->GetSysPref(C_PREF_DATE_FORMAT);
        if (empty($intDateFormatID) || ($intDateFormatID == 0)) {
          $intDateFormatID = 1;
        }
        switch ($intDateFormatID) {
          case 1: $strDateFormat = "%M %d, %Y"; break; // September 16, 2007
          case 2: $strDateFormat = "%d %M, %Y"; break; // 16 September, 2007
          case 3: $strDateFormat = "%d/%m/%Y";  break; // 16/09/2007
          case 4: $strDateFormat = "%m/%d/%Y";  break; // 09/16/2007
          case 5: $strDateFormat = "%Y/%m/%d";  break; // 2007/09/16
          case 6: $strDateFormat = "%Y-%m-%d";  break; // 2007-09-16
          case 7: $strDateFormat = "%Y/%d/%m";  break; // 2007/16/09
          case 8: $strDateFormat = "%Y-%d-%m";  break; // 2007-16-09
        }
        $intTimeFormatID = $this->GetSysPref(C_PREF_TIME_FORMAT);
        if (empty($intTimeFormatID)) {
          $intTimeFormatID = 0;
        }
        switch ($intTimeFormatID) {
          case 0: $strTimeFormat = "";          break; // Do not display time
          case 1: $strTimeFormat = " %H:%i";    break; // 24H
          case 2: $strTimeFormat = " %H:%i:%s"; break; // 24H with seconds
          case 3: $strTimeFormat = " %l:%i %p"; break; // 12H followed by AM or PM
        }
        $strDateAndTime = $strDateFormat.$strTimeFormat;
        $this->strDateAndTime = $strDateAndTime;
      }
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $this->strDateAndTime;
    }
    // ** Directories ** //
    function GetCurrentDir($strFile){
      $dteStartTime = $this->MicrotimeFloat();
      $strDirName = dirname($strFile);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strDirName;
    }
  }
?>