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

  class Messaging extends Helper {
    /*
      Sanitise incoming content
    */
    function SanitiseContent($strContent) {
      global $CMS;
      $strContent = strip_tags($strContent);
      $strContent = $CMS->StripSlashesIFW($strContent);
      return $strContent;
    }
    /*
      Notify admin of new articles once they're published.
    */
    function NewArticleNotification($intArticleID, $arrArticleData) {
      global $CMS;
      $strPageURL            = "http://".SVR_HOST.$arrArticleData['permalink'];
      $intNotifyAdmin        = $CMS->SYS->GetSysPref(C_PREF_ARTICLE_NOTIFY_ADMIN);
      $strAdminEmail         = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      $strArticleAuthorEmail = $arrArticleData['email'];
      $strTitle              = $this->SanitiseContent($arrArticleData['title']);
      $strAuthorName         = $this->SanitiseContent($arrArticleData['username']);
      $strEmailBody = "$strAuthorName just added a new article: $strTitle\r\n\r\n".
          "A link to the article is provided below.\r\n\r\n$strPageURL\r\n\r\n".
          "To disable future e-mail notifications, uncheck the Article Notification option in the Control Panel under Content Settings.";
      $strEmailTitle = "New article: $strTitle";
      $strEmailTitle = str_replace("&quot;", "\"", $strEmailTitle);
      $strEmailBody  = $CMS->RC->DoAll($strEmailBody);
      $strEmailBody  = str_replace("&quot;", "\"", $strEmailBody);
      // Notify admin
      if (($intNotifyAdmin == 1) && ($strAdminEmail != $strArticleAuthorEmail)) {
        @ $intNotifyResult = $CMS->SendEmail($strAdminEmail, $strEmailTitle, $strEmailBody, $strAdminEmail);
      }
    }
    /*
      Notify admin of articles awaiting approval.
    */
    function ReviewArticleNotification($intArticleID, $arrArticleData) {
      global $CMS;
      $strPageURL            = "http://".SVR_HOST.FN_ADM_CONTENT_MANAGE."?area=0&status=Review";
      $intNotifyAdmin        = $CMS->SYS->GetSysPref(C_PREF_ARTICLE_REVIEW_EMAIL);
      $strAdminEmail         = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      $strArticleAuthorEmail = $arrArticleData['email'];
      $strTitle              = $this->SanitiseContent($arrArticleData['title']);
      $strAuthorName         = $this->SanitiseContent($arrArticleData['username']);
      $strEmailBody = "$strAuthorName just added a new article: $strTitle\r\n\r\n".
          "This article requires approval. You can approve or deny the article at the following location:\r\n\r\n".
          "$strPageURL\r\n\r\nTo disable future e-mail notifications, go to the Control Panel and click on Settings - Content.";
      $strEmailTitle = "Please review: $strTitle";
      $strEmailTitle = str_replace("&quot;", "\"", $strEmailTitle);
      $strEmailBody  = $CMS->RC->DoAll($strEmailBody);
      $strEmailBody  = str_replace("&quot;", "\"", $strEmailBody);
      // Notify admin
      if (($intNotifyAdmin == 1) && ($strAdminEmail != $strArticleAuthorEmail)) {
        @ $intNotifyResult = $CMS->SendEmail($strAdminEmail, $strEmailTitle, $strEmailBody, $strAdminEmail);
      }
    }
  }

