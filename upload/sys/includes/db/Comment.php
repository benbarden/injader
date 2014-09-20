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

  class Comment extends Helper {
    // Caching
    var $arrComment;
    // ** Insert, Update, Delete ** //
    function Edit($intCommentID, $strContent, $dteDate, $strLogLink) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_COMMENTS} SET content = '$strContent', edit_date = '$dteDate' WHERE id = $intCommentID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_COMMENT_EDIT, $intCommentID, "");
    }
    function Delete($intCommentID, $intCurrentUserID) {
      global $CMS;
      $this->Query("DELETE FROM {IFW_TBL_COMMENTS} WHERE id = $intCommentID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_COMMENT_DELETE, $intCommentID, "");
    }
    // ** Select ** //
    function GetComment($intCommentID) {
      global $CMS;
      if (empty($this->arrComment[$intCommentID])) {
        $strDateFormat = $CMS->SYS->GetDateFormat();
        $arrCommentData = $this->ResultQuery("SELECT story_id, comment_count, content, comment_status, DATE_FORMAT(create_date, '$strDateFormat') AS create_date, create_date AS create_date_raw, DATE_FORMAT(edit_date, '$strDateFormat') AS edit_date, edit_date AS edit_date_raw, author_id, username, u.email, guest_email, upload_id, c.ip_address FROM {IFW_TBL_COMMENTS} c LEFT JOIN {IFW_TBL_USERS} u ON u.id = c.author_id WHERE c.id = $intCommentID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $this->arrComment[$intCommentID] = $arrCommentData[0];
      }
      return $this->arrComment[$intCommentID];
    }
    function GetAuthor($intCommentID) {
      if (!isset($this->arrComment[$intCommentID])) {
        $this->GetComment($intCommentID);
      }
      return $this->arrComment[$intCommentID]['username'];
    }
    function GetArticleComments($intArticleID) {
      global $CMS;
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $arrComments = $this->ResultQuery("SELECT c.id, c.story_id AS article_id, c.author_id, u.username, u.avatar_id, u.homepage_link, u.homepage_text, c.content, DATE_FORMAT(create_date, '$strDateFormat') AS create_date, create_date AS create_date_raw, DATE_FORMAT(edit_date, '$strDateFormat') AS edit_date, edit_date AS edit_date_raw, c.ip_address, r.rating_value, u.email, c.guest_name, c.guest_url, c.guest_email FROM {IFW_TBL_COMMENTS} c LEFT JOIN {IFW_TBL_USERS} u ON u.id = c.author_id LEFT JOIN {IFW_TBL_RATINGS} r ON r.comment_id = c.id WHERE story_id = $intArticleID AND comment_status = 'Approved' ORDER BY id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      // Caching
      for ($i=0; $i<count($arrComments); $i++) {
        $intID = $arrComments[$i]['id'];
        if (empty($this->arrComment[$intID])) {
          $this->arrComment[$intID] = $arrComments[$i];
        }
      }
      // Return
      return $arrComments;
    }
    // ** Comment count ** //
    function CountArticleComments($intArticleID) {
      $arrNumComments = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_COMMENTS} WHERE story_id = $intArticleID AND comment_status = 'Approved'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrNumComments[0]['count'];
    }
    // ** Used in User Stats ** //
    function CountUserComments($intUserID) {
      global $CMS;
      if ($intUserID == 0) {
        $intCount = 0;
      } else {
        $arrUserContent = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_COMMENTS} WHERE author_id = $intUserID AND comment_status = 'Approved'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $intCount = $arrUserContent[0]['count'];
      }
      return $intCount;
    }
    // ** Single-field select ** //
    function GetArticleID($intCommentID) {
      $arrItem = $this->ResultQuery("SELECT story_id FROM {IFW_TBL_COMMENTS} WHERE id = $intCommentID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrItem[0]['story_id'];
    }
    // ** Last commenter ** //
    function GetArticleLastComment($intArticleID) {
      $arrItem = $this->ResultQuery("SELECT * FROM {IFW_TBL_COMMENTS} WHERE story_id = $intArticleID AND comment_status = 'Approved' ORDER BY id DESC LIMIT 1", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return empty($arrItem[0]) ? "" : $arrItem[0];
    }
    // ** Title of parent ** //
    function GetThreadTitle($intID) {
      global $CMS;
      $arrItem = $this->ResultQuery("SELECT con.title FROM ({IFW_TBL_COMMENTS} com, {IFW_TBL_CONTENT} con) WHERE con.id = com.story_id AND com.id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrItem[0]['title'];
    }
    // ** Bulk Comments ** //
    function BulkCommentAuthors($strCommentIDs) {
      $arrAuthors = $this->ResultQuery("SELECT author_id FROM {IFW_TBL_COMMENTS} WHERE id IN $strCommentIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAuthors;
    }
    function BulkApprove($strCommentIDs) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_COMMENTS} SET comment_status = 'Approved' WHERE id IN $strCommentIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strCommentIDs = str_replace("(", "", $strCommentIDs);
      $strCommentIDs = str_replace(")", "", $strCommentIDs);
      $arrCommentIDs = explode(",", $strCommentIDs);
      for ($i=0; $i<count($arrCommentIDs); $i++) {
        $arrComment = $this->GetComment($arrCommentIDs[$i]);
        if ($arrComment['email']) {
          $strEmail = $arrComment['email'];
        } elseif ($arrComment['guest_email']) {
          $strEmail = $arrComment['guest_email'];
        } else {
          $strEmail = "";
        }
        if ($strEmail) {
          $CMS->UST->Plus($strEmail, "");
        }
      }
    }
    function BulkDeny($strCommentIDs) {
      global $CMS;
      // Get data.
      $strCommentIDs = str_replace("(", "", $strCommentIDs);
      $strCommentIDs = str_replace(")", "", $strCommentIDs);
      $arrCommentIDs = explode(",", $strCommentIDs);
      for ($i=0; $i<count($arrCommentIDs); $i++) {
        $arrComment = $this->GetComment($arrCommentIDs[$i]);
        // When denying comments in moderation or spam,
        // we don't need to change the comment count.
        if ($arrComment['comment_status'] == "Approved") {
          if ($arrComment['email']) {
            $strEmail = $arrComment['email'];
          } elseif ($arrComment['guest_email']) {
            $strEmail = $arrComment['guest_email'];
          } else {
            $strEmail = "";
          }
          if ($strEmail) {
            $CMS->UST->Minus($strEmail);
          }
        }
      }
      // Now delete the comments.
      $strCommentIDs = "(".$strCommentIDs.")";
      $this->Query("DELETE FROM {IFW_TBL_COMMENTS} WHERE id IN $strCommentIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
  }

?>