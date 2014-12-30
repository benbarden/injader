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
      Notify admin of new comments awaiting review
    */
    function ReviewCommentNotification($intArticleID, $strAuthorName, $strAuthorEmail, $strItemDesc, $strTitle, $strNotifyContent) {
      global $CMS;
      $strNotifyContent = $this->SanitiseContent($strNotifyContent);
      $intNotify     = $CMS->SYS->GetSysPref(C_PREF_COMMENT_REVIEW_EMAIL);
      $strReviewURL  = "http://".SVR_HOST.FN_ADM_COMMENTS."?type=pending";
      $strSiteTitle  = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      $strAdminEmail = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      if ($intNotify == 1) {
        $strEmailBody = "$strAuthorName just added a comment to the $strItemDesc, $strTitle:\r\n\r\n$strNotifyContent\r\n\r\nThis is a moderated user so the comment must be approved or denied. A link to the review screen is provided below. You must be logged in to view this screen.\r\n\r\n$strReviewURL\r\n\r\nTo disable future e-mail notifications, uncheck the Review Notification option in the Control Panel under Content Settings.";
        $strEmailTitle = "Please moderate: $strSiteTitle";
        $strEmailTitle = str_replace("&quot;", "\"", $strEmailTitle);
        $strEmailBody  = $CMS->RC->DoAll($strEmailBody);
        $strEmailBody  = str_replace("&quot;", "\"", $strEmailBody);
        @ $intNotifyAdmin = $CMS->SendEmail($strAdminEmail, $strEmailTitle, $strEmailBody, $strAuthorEmail);
      }
    }
    /*
      Notify admin and author of new comments once they're approved.
      Also notify anyone who's subscribed to the comments on this article.
    */
    function NewCommentNotification($intCommentID, $intArticleID, $strAuthorName, $strCommentAuthorEmail, $strItemDesc, $strTitle, $strContent, $strViewLink) {
      global $CMS;
      $strPageURL            = "http://".SVR_HOST.$strViewLink."#c".$intCommentID;
      $intNotifyAdmin        = $CMS->SYS->GetSysPref(C_PREF_COMMENT_NOTIFICATION);
      $intNotifyAuthor       = $CMS->SYS->GetSysPref(C_PREF_COMMENT_NOTIFY_AUTHOR);
      $strAdminEmail         = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      $arrArticleData        = $CMS->ART->GetArticle($intArticleID);
      $strArticleAuthorEmail = $arrArticleData['email'];
      $strContent            = $this->SanitiseContent($strContent);
      $strEmailBody = "$strAuthorName just added the following comment to the $strItemDesc, $strTitle:\r\n\r\n$strContent\r\n\r\nA link to the comment is provided below. You can reply to the comment here.\r\n\r\n$strPageURL";
      $strEmailTitle = "New comment: $strTitle";
      $strEmailTitle = str_replace("&quot;", "\"", $strEmailTitle);
      $strEmailBody  = $CMS->RC->DoAll($strEmailBody);
      $strEmailBody  = str_replace("&quot;", "\"", $strEmailBody);
      // Notify admin
      if (($intNotifyAdmin == 1) && ($strAdminEmail != $strCommentAuthorEmail)) {
        // Only admins need this section of the email
        $strAdminEmailBody = $strEmailBody."\r\n\r\nTo disable future e-mail notifications, uncheck the Comment Notification option in the Control Panel under Content Settings.";
        @ $intNotifyResult = $CMS->SendEmail($strAdminEmail, $strEmailTitle, $strAdminEmailBody, $strCommentAuthorEmail);
      }
      // Notify author
      if (($intNotifyAuthor == 1) && ($strArticleAuthorEmail != $strCommentAuthorEmail) && ($strArticleAuthorEmail != $strAdminEmail)) {
        @ $intNotifyResult = $CMS->SendEmail($strArticleAuthorEmail, $strEmailTitle, $strEmailBody, $strCommentAuthorEmail);
      }
      // Notify subscribed users
      $arrUsers = $CMS->UST->GetSubscribedUsers($intArticleID);
      if (is_array($arrUsers)) {
        $strUserEmailBody = $strEmailBody."\r\n\r\nTo unsubscribe from this article, go to the Subscription Manager: http://".SVR_HOST.FN_SUBSCRIBE;
        $strDomain = str_replace("www.", "", SVR_HOST);
        for ($i=0; $i<count($arrUsers); $i++) {
          $strUserEmail = $arrUsers[$i]['user_email'];
          if (($strUserEmail != $strArticleAuthorEmail) && ($strUserEmail != $strCommentAuthorEmail)) {
            @ $intNotifyResult = $CMS->SendEmail($strUserEmail, $strEmailTitle, $strUserEmailBody, "donotreply@".$strDomain);
          }
        }
      }
    }
    /*
      Used when bulk-approving comments.
    */
    function BulkNewCommentNotification($strCommentIDs) {
      global $CMS;
      if ($strCommentIDs) {
        $arrComments = $CMS->ResultQuery("SELECT c.*, a.title, u.username, u.email FROM ({IFW_TBL_COMMENTS} c, {IFW_TBL_CONTENT} a) LEFT JOIN {IFW_TBL_USERS} u ON c.author_id = u.id WHERE a.id = c.story_id AND c.id IN $strCommentIDs ORDER BY c.id ASC", basename(__FILE__), __LINE__);
        for ($i=0; $i<count($arrComments); $i++) {
          $intCommentID = $arrComments[$i]['id'];
          $intArticleID = $arrComments[$i]['story_id'];
          $strTitle     = $arrComments[$i]['title'];
          $intAuthorID  = $arrComments[$i]['author_id'];
          if ($intAuthorID) {
            // Registered user
            $strAuthorName  = $arrComments[$i]['username'];
            $strAuthorEmail = $arrComments[$i]['email'];
          } else {
            // Guest
            $strAuthorName  = $arrComments[$i]['guest_name'];
            $strAuthorEmail = $arrComments[$i]['guest_email'];
          }
          $strViewLink = $CMS->PL->ViewArticle($intArticleID);
          $strItemDesc = "article";
          $strContent  = $this->SanitiseContent($arrComments[$i]['content']);
          $this->NewCommentNotification($intCommentID, $intArticleID, $strAuthorName, $strAuthorEmail, $strItemDesc, $strTitle, $strContent, $strViewLink);
        }
      }
    }
    /*
      Notify admin of new articles once they're published.
    */
    function NewArticleNotification($intArticleID, $arrArticleData) {
      global $CMS;
      $strPageURL            = "http://".SVR_HOST.$CMS->PL->ViewArticle($intArticleID);
      $intNotifyAdmin        = $CMS->SYS->GetSysPref(C_PREF_ARTICLE_NOTIFY_ADMIN);
      $strAdminEmail         = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      $strArticleAuthorEmail = $arrArticleData['email'];
      $strTitle              = $this->SanitiseContent($arrArticleData['title']);
      $strAuthorName         = $this->SanitiseContent($arrArticleData['username']);
      $strEmailBody = "$strAuthorName just added a new article: $strTitle\r\n\r\nA link to the article is provided below.\r\n\r\n$strPageURL\r\n\r\nTo disable future e-mail notifications, uncheck the Article Notification option in the Control Panel under Content Settings.";
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
      $strPageURL            = "http://".SVR_HOST.FN_ADM_CONTENT_MANAGE."?navtype=1&area1=0&area2=0&area3=0&status=Review";
      $intNotifyAdmin        = $CMS->SYS->GetSysPref(C_PREF_ARTICLE_REVIEW_EMAIL);
      $strAdminEmail         = $CMS->SYS->GetSysPref(C_PREF_SITE_EMAIL);
      $strArticleAuthorEmail = $arrArticleData['email'];
      $strTitle              = $this->SanitiseContent($arrArticleData['title']);
      $strAuthorName         = $this->SanitiseContent($arrArticleData['username']);
      $strEmailBody = "$strAuthorName just added a new article: $strTitle\r\n\r\nThis article requires approval. You can approve or deny the article at the following location:\r\n\r\n$strPageURL\r\n\r\nTo disable future e-mail notifications, go to the Control Panel and click on Settings - Content.";
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

?>