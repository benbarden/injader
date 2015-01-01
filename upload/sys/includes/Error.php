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

  class Error extends Helper {
    /*
      MCATCH: Generate an error for information only.
      Example: which areas does a user have access to?
      Inaccessible areas would generate a CATCH error.
    */
    function MCatch($strErrMsg, $strErrData) {
      global $CMS;
      return true;
    }
    /*
      MWARN: Generate an error that will be displayed on-screen.
      Other processing will continue regardless.
    */
    function MWarn($strErrMsg, $strErrData) {
      global $CMS;
      $strError = $this->ErrWarn($strErrMsg, $strErrData);
      return $strError;
    }
    /*
      MFAIL: Generate an error that will be displayed on-screen.
      All other processing will terminate immediately.
    */
    function MFail($strErrMsg, $strErrData) {
      global $CMS;
      $intUserID = $CMS->RES->GetCurrentUserID();
      switch ($strErrMsg) {
        case M_ERR_NOT_LOGGED_IN:
          header("HTTP/1.1 401 Authorization Required");
          header("Status: 401 Authorization Required");
          $intHTTPErrorCode = 401;
          break;
        case M_ERR_UNAUTHORISED:
          header("HTTP/1.1 403 Forbidden");
          header("Status: 403 Forbidden");
          $intHTTPErrorCode = 403;
          break;
        case M_ERR_NO_ROWS_RETURNED:
          header("HTTP/1.1 404 Not Found");
          header("Status: 404 Not Found");
          $intHTTPErrorCode = 404;
          break;
        default:
          $intHTTPErrorCode = 0;
      }
      $CMS->SYS->CreateErrorLog($strErrMsg, "", $intUserID, $intHTTPErrorCode);
      exit($this->ErrPage($strErrMsg, $strErrData));
    }
    /* *** */
    function ErrWarn($strErrMsg, $strErrData) {
      if ($strErrData == "") {
        $strErrData = "<i>".M_ERR_PAGE_INFO_NONE."</i>.";
      }
      $strErrorText = "<div class=\"errortext\"><b>Warning:</b> $strErrMsg ($strErrData)</div>";
      return $strErrorText;
    }
    function ErrPage($strErrMsg, $strErrData) {
      global $CMS;
      if ($strErrData == "") {
        $strErrData = "<i>{M_ERR_PAGE_INFO_NONE}</i>.";
      }
      $strLoginLink = "";
      if ($strErrMsg == M_ERR_UNAUTHORISED) {
        $CMS->RES->ValidateLoggedIn();
        if ($CMS->RES->IsError()) {
          $strLoginLink = "<li>You are not logged in. <a href=\"{FN_LOGIN}\">Try logging in</a>.</li>";
        } else {
          $strLoginLink = "<li>You are already logged in. To clear your cookies, <a href=\"{FN_LOGOUT}\">try logging out</a>.</li>";
        }
      }
      $strReferrerLink = "";
      if ($strErrMsg <> M_ERR_SYSTEM_LOCKED) {
        $strReferrer = empty($_SERVER['HTTP_REFERER']) ? "" : $_SERVER['HTTP_REFERER'];
        if ($strReferrer) {
          $strReferrerLink = "<li><a href=\"$strReferrer\">{M_INFO_GO_BACK}</a>.</li>";
          $strHomePageLink = "<li>Or, you can <a href=\"{FN_INDEX}\">go to the home page</a>.</li>";
        } else {
          $strHomePageLink = "<li><a href=\"{FN_INDEX}\">Go to the home page</a>.</li>";
        }
        $strHelpLinks = <<<BackLink
<ul>
$strLoginLink
$strReferrerLink
$strHomePageLink
</ul>

BackLink;
      } else {
        $strHelpLinks = "";
      }
      $strErrorText = <<<END
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
<title>{M_ERR_PAGE_TITLE}</title>
<link rel="stylesheet" type="text/css" href="{URL_ROOT}sys/loginpage.css" />
</head>
<body>
<div id="mPage">
<h1>{M_ERR_PAGE_TITLE}</h1>
<p>$strErrMsg</p>
$strHelpLinks
<p id="SupInfo">{M_ERR_PAGE_INFO_DESC}: $strErrData</p>
</div>
</body>
</html>
END;
      $RC = new ReplaceConstants;
      return $RC->DoAll($strErrorText);
    }
  }
