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

  require 'sys/header.php';
  $strFeed = empty($_GET['name']) ? "" : $CMS->FilterAlphanumeric($_GET['name'], "");
  $RSS = new RSSBuilder;
  switch ($strFeed) {
    case "articles":
      $intAreaID = empty($_GET['id']) ? "" : $CMS->FilterNumeric($_GET['id']);
      if (!$intAreaID) {
        $strFeedburnerURL = $CMS->SYS->GetSysPref(C_PREF_RSS_ARTICLES_URL);
        if ($strFeedburnerURL) {
          $strUserAgent = empty($_SERVER['HTTP_USER_AGENT']) ? "" : $_SERVER['HTTP_USER_AGENT'];
          if (strpos(strtoupper($strUserAgent), "FEEDBURNER") !== false) {
            // Feedburner is OK to access this URL
          } else {
            httpRedirect($strFeedburnerURL);
            exit;
          }
        }
      }
      exit($RSS->GetArticleRSS($intAreaID));
      break;
    case "comments":
      $intArticleID = empty($_GET['id']) ? "" : $CMS->FilterNumeric($_GET['id']);
      exit($RSS->GetCommentRSS($intArticleID));
      break;
    default: $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "name");
  }
  
?>