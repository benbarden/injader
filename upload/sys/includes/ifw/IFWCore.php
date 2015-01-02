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

  class IFWCore {
    // Current Dir
    var $strCurrentDir;
    // Toggle performance testing //
    var $blnTestingMode;
    function SetTestingMode($blnToggle) {
      $this->blnTestingMode = $blnToggle;
    }
    function GetTestingMode() {
      return $this->blnTestingMode;
    }
    // ** Execution Time ** //
    function SetExecutionTime($dteStartTime, $dteEndTime, $strSource, $intLineNo) {
      // Variables defined in header.php
      global $strExecutionTime;
      global $intNumQueries;
      global $intMaxLevel;
      // Reset
      $strValue = "";
      if ((C_TEST_MODE == "Q_ALL") ||
          (C_TEST_MODE == "Q_DB") && (substr($strSource, 0, 4) == "*DB*")) {
        $dteDuration = sprintf('%.6f', $dteEndTime - $dteStartTime);
        if ($dteDuration > 0.1) {
          if ($intMaxLevel <= 4) {
            $strValue = "<li style=\"background-color: #fff; color: #f00;\"><b>L4: $strSource :: Line $intLineNo :: $dteDuration</b></li>\n";
          }
        } elseif ($dteDuration > 0.03) {
          if ($intMaxLevel <= 3) {
            $strValue = "<li style=\"background-color: #fff; color: #00f;\"><b>L3: $strSource :: Line $intLineNo :: $dteDuration</b></li>\n";
          }
        } elseif ($dteDuration > 0.01) {
          if ($intMaxLevel <= 2) {
            $strValue = "<li style=\"background-color: #fff; color: #090;\"><b>L2: $strSource :: Line $intLineNo :: $dteDuration</b></li>\n";
          }
        } elseif ($dteDuration > 0.002) {
          if ($intMaxLevel <= 1) {
            $strValue = "<li style=\"background-color: #fff; color: #000;\">L1: $strSource :: Line $intLineNo :: $dteDuration</li>\n";
          }
        } else {
          if ($intMaxLevel == 0) {
            $strValue = "<li>L0: $strSource :: Line $intLineNo :: $dteDuration</li>\n";
          }
        }
        if ($strValue) {
          $strExecutionTime .= $strValue;
          $intNumQueries++;
        }
      }
    }
    function MicrotimeFloat() {
      list($usec, $sec) = explode(" ", microtime());
      return ((float)$usec + (float)$sec);
    }
    // ** Directory Listing ** //
    function DirFile($strItem) {
      $strThisItem = $this->strCurrentDir.$strItem;
      return !is_dir($strThisItem);
    }
    function DirFolder($strItem) {
      $strThisItem = $this->strCurrentDir.$strItem;
      return is_dir($strThisItem);
    }
    function Dir($strDir, $strSwitch, $blnAddSlash) {
      $current_dir = $strDir;
      $dir = opendir($current_dir);
      $arrDirList = array();
      $i = 0;
      while ($file = readdir($dir)) {
        if (($file != ".") && 
            ($file != "..")) {
          $arrDirList[$i++] = $file;
        }
      }
      closedir($dir);
      if ($blnAddSlash) {
        $this->strCurrentDir = $strDir."/";
      } else {
        $this->strCurrentDir = $strDir;
      }
      if ($strSwitch == "files") {
        $arrDirList = array_filter($arrDirList, array($this, "DirFile"));
      } elseif ($strSwitch == "folders") {
        $arrDirList = array_filter($arrDirList, array($this, "DirFolder"));
      }
      return array_values($arrDirList);
    }
    // ** Filenames ** //
    function GetFileNameFromPath($strPath) {
  		$intLength = strlen($strPath);
  		$intStart = 0;
  		$i = 0;
      // Search the string in reverse - the slash is the folder
  		for ($i = $intLength; $i > 0; $i--) {
  			if ((substr($strPath, $i, 1) == "/") || (substr($strPath, $i, 1) == "\\")) {
  				$intStart = $i + 1;
  				$i = 0;
  			}
  		}
      return ($intStart > 0) ? substr($strPath, $intStart) : "";
    }
    
    function GetFileNameWithoutExtension($strFileName) {
        $intLength = strlen($strFileName);
        $intEnd = 0;
        $i = 0;
        // Search the string in reverse - the first period is the extension delimeter
        for ($i = $intLength; $i > 0; $i--) {
            if (substr($strFileName, $i, 1) == ".") {
                $intEnd = $i;
                $i = 0;
                break;
            }
        }
        return ($intEnd < $intLength) ? substr($strFileName, 0, $intEnd) : $strFileName;
    }
    
    function GetExtensionFromPath($strPath) {
  		$intLength = strlen($strPath);
  		$intStart = 0;
  		$i = 0;
      // Search the string in reverse - the first period is the extension delimeter
  		for ($i = $intLength; $i > 0; $i--) {
  			if (substr($strPath, $i, 1) == ".") {
  				$intStart = $i + 1;
  				$i = 0;
  			}
  		}
      return ($intStart > 0) ? substr($strPath, $intStart) : "";
    }
    // ** Emails ** //
    function IsValidEmail($strEmail) {
      return (eregi('^([0-9a-z]+[-._+&])*[0-9a-z]+@([-0-9a-z]+[.])+[a-z]{2,6}$', $strEmail)) ? true : false;
    }
    function IsEmailCrackAttempt($strBody) {
      return (eregi("(\r|\n)(to:|from:|cc:|bcc:)", $strBody)) ? true : false;
    }
  	function SendEmail($strTo, $strSubject, $strBody, $strFrom) {
      if ($this->IsEmailCrackAttempt($strBody)) {
  			$intReturn = 3; // Cracking attempt
      } elseif ($this->IsValidEmail($strFrom)) {
        $intReturn = (mail($strTo, $strSubject, $strBody, "From: $strFrom\r\n")) ? 1 : 2;
      } else {
        $intReturn = 0; // Invalid email
      }
      return $intReturn;
  	}
    // ** Hyperlinks ** //
    function AutoLink($strURL) {
      if ($strURL) {
        if (strpos($strURL, "http://") === false) {
          $strURL = "http://".$strURL;
        }
      }
      return $strURL;
    }
    // ** Sanitise form data ** //
    function FilterNumeric($strData) {
      $strData = preg_replace("/[^0-9]/", "", $strData);
      $strData = strip_tags($strData);
      return $strData;
    }
    function FilterAlphanumeric($strData, $strExtraChars = "") {
      $strData = preg_replace("/[^a-zA-Z0-9".$strExtraChars."]/", "", $strData);
      $strData = strip_tags($strData);
      return $strData;
    }
    function DoEntities($strData) {
      return htmlspecialchars($strData); // converts < to &lt; and so on
    }
    function ArrayAddSlashes($arrData) {
      $arrOutputData = array();
      foreach($arrData as $key => $value) {
        if (!is_array($value)) {
          $arrOutputData[$key] = $this->AddSlashesIFW($value);
        }
      }
      return $arrOutputData;
    }
    function ArrayStripSlashes($arrData) {
      $arrOutputData = array();
      foreach($arrData as $key => $value) {
        if (!is_array($value)) {
          $arrOutputData[$key] = $this->StripSlashesIFW($value);
        }
      }
      return $arrOutputData;
    }
    // ** Slashes ** //
    function HasSlashes($strData) {
      $blnResult = false;
      if (strpos($strData, '\"') !== false) {
        $blnResult = true;
      } elseif (strpos($strData, "\'") !== false) {
        $blnResult = true;
      } elseif (strpos($strData, "\&apos;") !== false) {
        $blnResult = true;
      } elseif (strpos($strData, "\&quot;") !== false) {
        $blnResult = true;
      }
      return $blnResult;
    }
    function AddSlashesIFW($strData) {
      if (!$this->HasSlashes($strData)) {
        $strData = addslashes($strData);
      }
      return $strData;
    }
    function StripSlashesIFW($strData) {
      if ($this->HasSlashes($strData)) {
        $strData = stripslashes($strData);
      }
      if (strpos($strData, "\\\\") !== false) {
        $strData = str_replace("\\\\", "\\", $strData);
      }
      return $strData;
    }
    // ** Page editing (WYSIWYG) ** //
    function PreparePageForEditing($strHTML) {
      $strHTML = str_replace("{", "{".ZZZ_TEMP, $strHTML);
      $strHTML = str_replace("}", ZZZ_TEMP."}", $strHTML);
      $strHTML = str_replace('$plg', '$plg'.ZZZ_TEMP, $strHTML);
      $strHTML = str_replace('$usr', '$usr'.ZZZ_TEMP, $strHTML);
      $strHTML = $this->DoEntities($strHTML);
      return $strHTML;
    }
    // ** Template editing ** //
    function PrepareTemplateForEditing($strHTML) {
      $strHTML = $this->StripSlashesIFW($strHTML);
      $strHTML = $this->DoEntities($strHTML);
      $strHTML = str_replace("{", "{".ZZZ_TEMP, $strHTML);
      $strHTML = str_replace("}", ZZZ_TEMP."}", $strHTML);
      return $strHTML;
    }
    function PrepareTemplateForSaving($strHTML) {
      $strHTML = $this->AddSlashesIFW($strHTML);
      $strHTML = str_replace("&lt;", "<", $strHTML);
      $strHTML = str_replace("&gt;", ">", $strHTML);
      return $strHTML;
    }
    // ** Search engine optimisation ** //
    function MakeSEOTitle($strTitle) {
      $strTitle = str_replace("&quot;", "", $strTitle);
      $strTitle = str_replace(" ", "-", $strTitle);
      $strTitle = str_replace("_", "-", $strTitle);
      $strTitle = str_replace("---", "-", $strTitle);
      $strTitle = strtolower($strTitle);
      $strTitle = $this->FilterAlphanumeric($strTitle, "-");
      return $strTitle;
    }
    function MakeMetaDesc($strOrigContent) {
      $strContent = strip_tags($strOrigContent);
      $strContent = str_replace("\r", " ", $strContent);
      $strContent = str_replace("\n", " ", $strContent);
      $strContent = substr($strContent, 0, 200);
      if ($strContent != $strOrigContent) {
        $strContent .= "...";
      }
      return $strContent;
    }
    // ** Social bookmarking links ** //
    function MakeStumbleLink() {
      $strSanitisedURL = str_replace(":", "%3A", "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
      $strSanitisedURL = str_replace("/", "%2F", $strSanitisedURL);
      $strStumbleThis = "http://www.stumbleupon.com/submit?url=".$strSanitisedURL;
      return $strStumbleThis;
    }
    // ** Calculations ** //
    function MakeFileSize($strBytes) {
      $fltFileSizeKB = (float) $strBytes / 1024;
      if (round($fltFileSizeKB, 2) < 500) { // KB
        $strFileSize = round($fltFileSizeKB, 2)."KB";
      } else { // MB
        $fltFileSizeMB = round((float) $fltFileSizeKB / 1024, 2);
        $strFileSize = $fltFileSizeMB."MB";
      }
      return $strFileSize;
    }
    // ** Sort rule ** //
    // Build sort rule array //
    function GetSortRuleFields() {
      $arrRuleFields =
        array(
          0 => array(
            "value" => "author_name",
            "text"  => "Author Name"
          ),
          1 => array(
            "value" => "create_date",
            "text"  => "Creation Date"
          ),
          2 => array(
            "value" => "last_updated",
            "text"  => "Last Updated"
          ),
          3 => array(
            "value" => "article_title",
            "text"  => "Article Title"
          ),
          4 => array(
            "value" => "random",
            "text"  => "Random"
          ),
          5 => array(
            "value" => "custom",
            "text"  => "Custom"
          )
        );
      return $arrRuleFields;
    }
    function GetSortRuleOrder() {
      $arrRuleOrder =
        array(
          0 => array(
            "value" => "asc",
            "text"  => "Ascending"
          ),
          1 => array(
            "value" => "desc",
            "text"  => "Descending"
          )
        );
      return $arrRuleOrder;
    }
    // Build sort rule SQL //
    function BuildAreaSortRule($strSortRule) {
      $arrSortRule = explode("|", $strSortRule);
      $strSortRuleField = empty($arrSortRule[0]) ? "" : $arrSortRule[0];
      $strSortRuleOrder = empty($arrSortRule[1]) ? "" : $arrSortRule[1];
      $strSQL = " ORDER BY ";
      switch ($strSortRuleField) {
        case "author_name":   $strSQL .= "username";        break;
        case "create_date":   $strSQL .= "create_date_raw"; break;
        case "last_updated":  $strSQL .= "last_updated";    break;
        case "article_title": $strSQL .= "title";           break;
        case "random":        $strSQL .= "rand()";          break;
        case "custom":        $strSQL .= "article_order";   break;
        default:              $strSQL .= "create_date_raw"; break;
      }
      switch ($strSortRuleOrder) {
        case "asc":  $strSQL .= " ASC ";  break;
        case "desc": $strSQL .= " DESC "; break;
        default:     $strSQL .= " DESC "; break;
      }
      return $strSQL;
    }
    // Home page
    function GetHomePageURL() {
      return "http://".SVR_HOST.URL_ROOT;
    }
  }

?>