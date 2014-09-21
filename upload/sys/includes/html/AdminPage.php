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

  class AdminPage extends Helper {
    var $strTitle = "Injader";
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
<ol>
$strExecutionTime
</ol>
</div>
ExecTime;
      } else {
        $strQueryTimeData = "";
      }
      global $blnOverrideQT; // Allows the variable to be put in sys templates without being evaluated
      if ($blnOverrideQT) {
        $strHTMLToPrint = str_replace('$cmsQueryTime', "", $strHTMLToPrint);
      } else {
        if (C_TEST_MODE <> "") {
          $strHTMLToPrint = str_replace('$cmsQueryTime', $strQueryTimeData, $strHTMLToPrint);
        } else {
          $strHTMLToPrint = str_replace('$cmsQueryTime', "", $strHTMLToPrint);
        }
      }
      print($strHTMLToPrint);
      $CMS->IQ->Disconnect();
      exit;
    }
    function GetHeader() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strPageTitle = $this->GetTitle();
      $strSystemLock = $CMS->SYS->GetSysPref(C_PREF_SYSTEM_LOCK);
      if ($strSystemLock == "Y") {
        $strSystemLocked = <<<AdminNotice
<div id="notice-locked">
ADMIN NOTICE: SITE LOCKED - Currently, only administrators can view this web site. You must unlock the system to remove this alert.
</div>

AdminNotice;
      } else {
        $strSystemLocked = "";
      }
      // Index
      $strSiteTitle  = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      $strIndexURL   = str_replace("index".F_EXT_PHP, "", FN_INDEX);
      // New article
      $strNewArticleLink = "<a href=\"{FN_ADM_WRITE}?action=create\" title=\"Add new content to the site\">New Article</a>";
      // Content
      $CMS->RES->ViewManageContent();
      if ($CMS->RES->IsError()) {
        $strManageContent = "";
      } else {
        // Create article?
        if ($CMS->RES->CountTotalWriteAccess() > 0) {
          $strManageContent = $strNewArticleLink." | <a href=\"{FN_ADM_CONTENT_MANAGE}\" title=\"Manage Content\">Content</a>";
        } else {
          $strManageContent = "";
        }
      }
      // Admin
      $CMS->RES->Admin();
      if ($CMS->RES->IsError()) {
        $strAdminLinks = $strManageContent;
      } else {
        $strAdminLinks = <<<AdminLinks
$strNewArticleLink | 
<a href="{FN_ADM_AREAS}" title="Manage your site areas">Areas</a> | 
<a href="{FN_ADM_CONTENT_MANAGE}" title="Manage Content">Content</a> | 
<a href="{FN_ADM_FILES}" title="Manage site files, attachments and avatars">Files</a> | 
<a href="{FN_ADM_COMMENTS}" title="Manage comments">Comments</a> | 
<a href="{FN_ADM_WIDGETS}" title="Manage your Widgets">Widgets</a> | 
<a href="{FN_ADM_THEMES}" title="Manage your themes">Themes</a> | 
<a href="{FN_ADM_TOOLS}" title="Plugins and more">Tools</a> | 
<a href="{FN_ADM_GENERAL_SETTINGS}" title="Configure website settings">Settings</a> | 
<a href="{FN_ADM_USERS}" title="Manage user accounts, roles, and permissions">Access</a>

AdminLinks;
      }
      // Meta generator
      $strMetaGenerator = "<meta name=\"generator\" content=\"".PRD_PRODUCT_NAME." - ".PRD_PRODUCT_URL."\" />";
      // Build code
      $strHeaderHTML = <<<CMSHeader
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
$strMetaGenerator
<title>$strPageTitle</title>
<link href="{URL_SYS_ROOT}cp.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{URL_SYS_ROOT}scripts.js"></script>
<script type="text/javascript" src="{URL_SYS_JQUERY}jquery-1.11.1.min.js"></script>
</head>
<body id="cp-body">
$strSystemLocked
<!-- Begin header -->

<div id="tplPageWrapper">
  <div id="topnav">
    <a href="$strIndexURL" title="Back to $strSiteTitle">Back to $strSiteTitle</a> | <a href="{FN_ADM_INDEX}" title="Go to the Control Panel Index">Control Panel Index</a>
  </div>
  <div id="tplHeadContent">
    <img src="{URL_SYS_IMAGES}ij_header.jpg" alt="Injader" border="0" />
  </div>
  <div id="navbar">
    $strAdminLinks
  </div>
  <div id="cp">

CMSHeader;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHeaderHTML;
    }
    // ** Footer ** //
    function GetFooter() {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $strVersion   = $CMS->SYS->GetSysPref(C_PREF_CMS_VERSION);
      $intYear = date('Y'); // Current year
      $strFooter = <<<Footer
</div> <!-- /cp -->
\$cmsQueryTime
<div id="footer">
<a href="http://www.injader.com/">Injader</a> $strVersion |
<a href="https://github.com/benbarden/injader" title="Github">Github</a> |
Injader is free software released under the
<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public Licence</a> (v3).
Copyright &copy; 2005-$intYear <a href="http://www.benbarden.com/">Ben Barden</a>.
</div>
</div> <!-- /tplPageWrapper -->
</body>
</html>
Footer;
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strFooter;
    }
  }
?>