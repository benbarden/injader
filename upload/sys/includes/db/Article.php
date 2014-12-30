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
class Article extends Helper {
    
    var $blnDeleteAll = false;
    // Bulk actions
    var $blnBulk;
    // Caching
    var $arrArticle;
    
    // Insert, Update, Delete //
    function Create($intAuthorID, $dteDate, $strTitle, $strContent, $intAreaID, 
        $strTags, $strContURL, $strContStatus, $strUserGroups, 
        $strExcerpt, $intOrder) {
        
        global $CMS;
        
        $strSEOTitle = $CMS->MakeSEOTitle($strTitle);
        
        $strQuery = sprintf("
            INSERT INTO {IFW_TBL_CONTENT}(
                author_id, create_date, title, content, content_area_id, last_updated, 
                read_userlist, tags, seo_title, link_url, content_status, user_groups, 
                tags_deleted, article_excerpt, article_order
            ) VALUES(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', 
                '%s', '%s', '%s', '%s'
            )
        ",
            mysql_real_escape_string($intAuthorID),
            mysql_real_escape_string($dteDate),
            mysql_real_escape_string($strTitle),
            mysql_real_escape_string($strContent),
            mysql_real_escape_string($intAreaID),
            mysql_real_escape_string($dteDate),
            "",
            mysql_real_escape_string($strTags),
            mysql_real_escape_string($strSEOTitle),
            mysql_real_escape_string($strContURL),
            mysql_real_escape_string($strContStatus),
            mysql_real_escape_string($strUserGroups),
            "",
            mysql_real_escape_string($strExcerpt),
            mysql_real_escape_string($intOrder)
        );
        
        $intID = $this->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        
        switch ($strContStatus) {
            case C_CONT_PUBLISHED:
                $CMS->AL->Build(AL_TAG_ARTICLE_PUBLISH, $intID, $strTitle);
                break;
            case C_CONT_REVIEW:
                $CMS->AL->Build(AL_TAG_ARTICLE_REVIEW, $intID, $strTitle);
                break;
            case C_CONT_DRAFT:
                $CMS->AL->Build(AL_TAG_ARTICLE_SAVEDRAFT, $intID, $strTitle);
                break;
            case C_CONT_SCHEDULED:
                $CMS->AL->Build(AL_TAG_ARTICLE_SCHEDULE, $intID, $strTitle);
                break;
            default:
                $CMS->AL->Build(AL_TAG_ARTICLE_CREATE, $intID, $strTitle);
                break;
        }
        
        // Update mapping table
        $CMS->PL->SetTitle($strSEOTitle);
        $strLink = $CMS->PL->ViewArticle($intID, $intAreaID);
        $CMS->PL->SetTitle("");
        $CMS->UM->addLink($strLink, $intID, 0);
        
        // The end!
        return $intID;
    }
    
    function Edit($intID, $intAuthorID, $strTitle, $strContent, $dteCreateDate, 
        $intAreaID, $strTags, $strContURL, $strContStatus, $strUserGroups,
        $strExcerpt, $intOrder) {
        
        global $CMS;
        $dteEditDate = $CMS->SYS->GetCurrentDateAndTime();
        $strSEOTitle = $CMS->MakeSEOTitle($strTitle);
        
        $strQuery = sprintf("
            UPDATE {IFW_TBL_CONTENT}
            SET author_id = %s,
            title = '%s',
            content = '%s', 
            content_area_id = %s, 
            create_date = '%s', 
            edit_date = '%s', 
            last_updated = '%s', 
            tags = '%s', 
            seo_title = '%s', 
            link_url = '%s', 
            content_status = '%s', 
            user_groups = '%s',
            article_excerpt = '%s',
            article_order = '%s'
            WHERE id = %s
        ",
            mysql_real_escape_string($intAuthorID),
            mysql_real_escape_string($strTitle),
            mysql_real_escape_string($strContent),
            mysql_real_escape_string($intAreaID),
            mysql_real_escape_string($dteCreateDate),
            mysql_real_escape_string($dteEditDate),
            mysql_real_escape_string($dteEditDate),
            mysql_real_escape_string($strTags),
            mysql_real_escape_string($strSEOTitle),
            mysql_real_escape_string($strContURL),
            mysql_real_escape_string($strContStatus),
            mysql_real_escape_string($strUserGroups),
            mysql_real_escape_string($strExcerpt),
            mysql_real_escape_string($intOrder),
            mysql_real_escape_string($intID)
        );
        
        $this->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        
        switch ($strContStatus) {
            case C_CONT_PUBLISHED:
                $CMS->AL->Build(AL_TAG_ARTICLE_PUBLISH, $intID, $strTitle);
                break;
            case C_CONT_DRAFT:
                $CMS->AL->Build(AL_TAG_ARTICLE_SAVEDRAFT, $intID, $strTitle);
                break;
            case C_CONT_SCHEDULED:
                $CMS->AL->Build(AL_TAG_ARTICLE_SCHEDULE, $intID, $strTitle);
                break;
            default:
                $CMS->AL->Build(AL_TAG_ARTICLE_EDIT, $intID, $strTitle);
                break;
        }
        
        // Update mapping table
        $CMS->PL->SetTitle($strSEOTitle);
        $strLink = $CMS->PL->ViewArticle($intID, $intAreaID);
        $CMS->PL->SetTitle("");
        $CMS->UM->addLink($strLink, $intID, 0);
    }
    
    function Delete($intArticleID) {
        global $CMS;
        $arrArticle = $this->ResultQuery("SELECT title FROM {IFW_TBL_CONTENT} WHERE id = $intArticleID", 
            __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $strTitle = $arrArticle[0]['title'];
        // Delete attached comments
        $this->Query("DELETE FROM {IFW_TBL_COMMENTS} WHERE story_id = $intArticleID", 
            __CLASS__ . "::" . __FUNCTION__, __LINE__);
        // Delete URL mappings
        $this->Query("DELETE FROM {IFW_TBL_URL_MAPPING} WHERE article_id = $intArticleID", 
            __CLASS__ . "::" . __FUNCTION__, __LINE__);
        // Delete the article
        $this->Query("DELETE FROM {IFW_TBL_CONTENT} WHERE id = $intArticleID", 
            __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $CMS->AL->Build(AL_TAG_ARTICLE_DELETE, $intArticleID, $strTitle);
    }
    
    function Mark($intArticleID) {
      global $CMS;
      $strTitle = $this->GetTitle($intArticleID);
      // Delete article tags
      $arrTags = $this->ResultQuery("SELECT tags FROM {IFW_TBL_CONTENT} WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strTags = "";
      if ($arrTags[0]['tags']) {
        $strTags = $CMS->TG->BuildNameList($arrTags[0]['tags']);
        $CMS->TG->RemoveArticleTags($arrTags[0]['tags'], $intArticleID);
      }
      // Flag for deletion
      $strTags = $this->AddSlashesIFW($strTags);
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET content_status = '{C_CONT_DELETED}', tags = '', tags_deleted = '$strTags' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if (!$this->blnBulk) {
        $CMS->AL->Build(AL_TAG_ARTICLE_MARK, $intArticleID, $strTitle);
      }
    }
    
    function Restore($intArticleID) {
      global $CMS;
      $strTitle = $this->GetTitle($intArticleID);
      $strTags  = $this->arrArticle[$intArticleID]['tags_deleted'];
      // Recreate tags
      $strTagList = $CMS->TG->BuildIDList($strTags, $intArticleID);
      $CMS->ART->SetTags($intArticleID, $strTagList);
      // Restore article and tags
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET content_status = '{C_CONT_PUBLISHED}', tags = '$strTagList', tags_deleted = '' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if (!$this->blnBulk) {
        $CMS->AL->Build(AL_TAG_ARTICLE_RESTORE, $intArticleID, $strTitle);
      }
    }
    
    function Lock($intArticleID) {
      global $CMS;
      $strTitle = $this->GetTitle($intArticleID);
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET locked = 'Y' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_ARTICLE_LOCK, $intArticleID, $strTitle);
    }
    
    function Unlock($intArticleID) {
      global $CMS;
      $strTitle = $this->GetTitle($intArticleID);
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET locked = 'N' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_ARTICLE_UNLOCK, $intArticleID, $strTitle);
    }
    function IncrementHits($intArticleID) {
      $arrContent = $this->ResultQuery("SELECT hits FROM {IFW_TBL_CONTENT} WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $intHits = $arrContent[0]['hits'];
      $intNewHits = $intHits + 1;
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET hits = $intNewHits WHERE id = $intArticleID");
    }
    function UpdateUserList($intArticleID, $intUserID) {
      $arrArticle = $this->ResultQuery("SELECT read_userlist FROM {IFW_TBL_CONTENT} WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strUserList = $arrArticle[0]['read_userlist'];
      if (!$strUserList) {
        $this->SetUserList($intArticleID, $intUserID);
      } else {
        $arrUserlist = explode("|", $strUserList);
        $blnAlreadyInList = false;
        for ($i=0; $i<count($arrUserlist); $i++) {
          if ($arrUserlist[$i] == $intUserID) {
            $blnAlreadyInList = true;
            break;
          }
        }
        if (!$blnAlreadyInList) {
          $strNewListEntry = "$strUserList|$intUserID";
          $this->SetUserList($intArticleID, $strNewListEntry);
        }
      }
    }
    function SetUserList($intArticleID, $strUserlist) {
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET read_userlist = '$strUserlist' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function ClearUserlist($intArticleID) {
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET read_userlist = '' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function MarkAsNew($intArticleID) {
      global $CMS;
      $dteDate = $CMS->SYS->GetCurrentDateAndTime();
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET read_userlist = '', last_updated = '$dteDate' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function BulkMarkAsNew($strArticleIDs) {
      global $CMS;
      $dteDate = $CMS->SYS->GetCurrentDateAndTime();
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET read_userlist = '', last_updated = '$dteDate' WHERE id IN $strArticleIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function SetTags($intArticleID, $strTags) {
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET tags = '$strTags' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Get ** //
    function GetArticle($intArticleID) {
      global $CMS;
      if (!isset($this->arrArticle[$intArticleID])) {
        $strDateFormat = $CMS->SYS->GetDateFormat();
        $arrArticle = $this->ResultQuery("
          SELECT con.*, u.username, u.email, 
          u.avatar_id, a.name AS area_name,
          DATE_FORMAT(con.create_date, '$strDateFormat') AS create_date, 
          con.create_date AS create_date_raw, 
          DATE_FORMAT(con.edit_date, '$strDateFormat') AS edit_date, 
          con.edit_date AS edit_date_raw
          FROM ({IFW_TBL_CONTENT} con, {IFW_TBL_AREAS} a)
          LEFT JOIN {IFW_TBL_USERS} u ON con.author_id = u.id
          WHERE con.id = $intArticleID AND con.content_area_id = a.id
        ", __CLASS__ . "::" . __FUNCTION__ . " (Article $intArticleID)", __LINE__);
        $this->arrArticle[$intArticleID] = $arrArticle[0];
      }
      return $this->arrArticle[$intArticleID];
    }
    // ** SEO getter ** //
    function GetIDFromSEOTitle($strSEOTitle) {
      $strQuery = sprintf("SELECT id FROM {IFW_TBL_CONTENT} WHERE seo_title = '%s'",
              mysql_real_escape_string($strSEOTitle));
      $arrResult = $this->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['id'];
    }
    // ** Get (these use GetArticle) ** //
    function GetArticleAreaID($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['content_area_id'];
    }
    function GetTitle($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['title'];
    }
    function GetSEOTitle($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['seo_title'];
    }
    function GetAuthor($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['username'];
    }
    function IsPublished($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['content_status'] == C_CONT_PUBLISHED ? true : false;
    }
    function IsLocked($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['locked'] == "Y" ? true : false;
    }
    function IsDeleted($intArticleID) {
      if (!isset($this->arrArticle[$intArticleID])) {
        $this->GetArticle($intArticleID);
      }
      return $this->arrArticle[$intArticleID]['content_status'] == C_CONT_DELETED ? true : false;
    }
    // ** Content in each area ** //
    function GetContentAreaArticles($intAreaID, $strSQLSortRule, $intStart, $intEnd) {
      global $CMS;
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $strSQL = "SELECT con.*, author_id, u.username, u.email, u.avatar_id, 
          u.seo_username, title, seo_title, 
          DATE_FORMAT(con.create_date, '$strDateFormat') AS create_date, 
          con.create_date AS create_date_raw, 
          DATE_FORMAT(con.edit_date, '$strDateFormat') AS edit_date, 
          con.edit_date AS edit_date_raw, 
          DATE_FORMAT(con.last_updated, '$strDateFormat') AS updated_date, 
          con.last_updated AS updated_date_raw
          FROM ({IFW_TBL_CONTENT} con, {IFW_TBL_AREAS} a) 
          LEFT JOIN {IFW_TBL_USERS} u ON con.author_id = u.id 
          WHERE content_area_id = $intAreaID AND con.content_area_id = a.id 
          AND content_status = '{C_CONT_PUBLISHED}' 
          $strSQLSortRule LIMIT $intStart, $intEnd";
      $arrContentItems = $this->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrContentItems); $i++) {
        $intArticleID = $arrContentItems[$i]['id'];
        $this->arrArticle[$intArticleID] = $arrContentItems[$i];
      }
      return $arrContentItems;
    }
    
    function GetIndexContent($intAreaID, $arrArea, $strSQLSortRule, $intStart, $intEnd) {
      global $CMS;
      if ($arrArea['subarea_content_on_index'] == "Y") {
          $strAreaClause = 
            " AND a.hier_left BETWEEN ".$arrArea['hier_left']." AND ".$arrArea['hier_right'];
      } else {
          $strAreaClause = "AND content_area_id = $intAreaID";
      }
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $strSQL = "SELECT con.*, author_id, u.username, u.email, u.avatar_id, 
          u.seo_username, title, seo_title, 
          DATE_FORMAT(con.create_date, '$strDateFormat') AS create_date, 
          con.create_date AS create_date_raw, 
          DATE_FORMAT(con.edit_date, '$strDateFormat') AS edit_date, 
          con.edit_date AS edit_date_raw, 
          DATE_FORMAT(con.last_updated, '$strDateFormat') AS updated_date, 
          con.last_updated AS updated_date_raw
          FROM ({IFW_TBL_CONTENT} con, {IFW_TBL_AREAS} a) 
          LEFT JOIN {IFW_TBL_USERS} u ON con.author_id = u.id 
          WHERE con.content_area_id = a.id
          $strAreaClause
          AND content_status = '{C_CONT_PUBLISHED}' 
          $strSQLSortRule LIMIT $intStart, $intEnd";
      $arrContentItems = $this->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrContentItems); $i++) {
        $intArticleID = $arrContentItems[$i]['id'];
        $this->arrArticle[$intArticleID] = $arrContentItems[$i];
      }
      return $arrContentItems;
    }
    
    function GetSmartAreaArticles($strArticleIDs, $strSQLSortRule, $intStart, $intEnd) {
      global $CMS;
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $strSQL = "SELECT con.*, author_id, u.username, u.email, u.avatar_id, 
        u.seo_username, title, seo_title, 
        DATE_FORMAT(con.create_date, '$strDateFormat') AS create_date, 
        con.create_date AS create_date_raw, 
        DATE_FORMAT(con.edit_date, '$strDateFormat') AS edit_date, 
        con.edit_date AS edit_date_raw, 
        DATE_FORMAT(con.last_updated, '$strDateFormat') AS updated_date, 
        con.last_updated AS updated_date_raw
        FROM {IFW_TBL_CONTENT} con
        LEFT JOIN {IFW_TBL_USERS} u ON con.author_id = u.id
        WHERE con.id IN ($strArticleIDs) AND content_status = '{C_CONT_PUBLISHED}'
        $strSQLSortRule LIMIT $intStart, $intEnd";
      $arrContentItems = $this->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrContentItems); $i++) {
        $intArticleID = $arrContentItems[$i]['id'];
        $this->arrArticle[$intArticleID] = $arrContentItems[$i];
      }
      return $arrContentItems;
    }
    
    // ** Site-wide What's New - used in New For You ** //
    function GetNewestArticles() {
      global $CMS;
      // Core calls
      $strWhereClause = "WHERE content_status = '{C_CONT_PUBLISHED}'";
      $strUnreadSQL = $CMS->UG->BuildUserUnreadSQL("con", $CMS->RES->GetCurrentUserID(), false);
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $strSQL = "SELECT con.id, title, seo_title, DATE_FORMAT(con.last_updated, '$strDateFormat') AS con_updated, con.content_area_id FROM {IFW_TBL_CONTENT} con $strWhereClause $strUnreadSQL ORDER BY last_updated DESC LIMIT 10";
      $arrContentItems = $CMS->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrContentItems;
    }
    // ** Used in My Content and User Stats ** //
    function CountUserContent($intUserID, $strWhereClause) {
      global $CMS;
      $arrUserContent = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_CONTENT} WHERE author_id = $intUserID $strWhereClause", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUserContent[0]['count'];
    }
    function GetUserContent($intUserID, $intContentPerPage, $intPageNumber, $strWhereClause) {
      global $CMS;
      $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $arrUserContent = $CMS->ResultQuery("SELECT c.id, c.title, c.content_area_id, a.name AS area_name, a.seo_name AS area_seo_name, DATE_FORMAT(c.create_date, '$strDateFormat') AS create_date, c.create_date AS create_date_raw, c.seo_title, c.hits, c.content_status, c.comment_count FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a) LEFT JOIN {IFW_TBL_USERS} u ON c.author_id = u.id WHERE c.content_area_id = a.id AND author_id = $intUserID $strWhereClause ORDER BY create_date_raw DESC LIMIT $intStart, $intContentPerPage", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrUserContent;
    }
    // ** Count deleted content ** //
    function CountDeletedContent() {
      $arrContent = $this->ResultQuery("SELECT count(*) AS content_count FROM {IFW_TBL_CONTENT} con WHERE content_status = '{C_CONT_DELETED}'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrContent[0]['content_count'];
    }
    // ** Get scheduled content ** //
    function ReleaseScheduledContent() {
      global $CMS;
      $dteTodaysDate = $CMS->SYS->GetCurrentDateAndTime();
      $arrContent = $this->ResultQuery("SELECT id, create_date FROM {IFW_TBL_CONTENT} con WHERE content_status = '{C_CONT_SCHEDULED}' AND create_date <= '$dteTodaysDate'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if (is_array($arrContent)) {
        for ($i=0; $i<count($arrContent); $i++) {
          $intArticleID  = $arrContent[$i]['id'];
          $dteCreateDate = $arrContent[$i]['create_date'];
          $this->Query("UPDATE {IFW_TBL_CONTENT} SET content_status = '{C_CONT_PUBLISHED}', last_updated = '$dteCreateDate' WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
          $arrCurrentData = $this->GetArticle($intArticleID);
          // Send notifications
          $CMS->MSG->NewArticleNotification($intArticleID, $arrCurrentData);
        }
      }
    }
    // ** Refresh comment count ** //
    function RefreshCommentCount() {
      global $CMS;
      $arrResult = $this->ResultQuery("SELECT id FROM {IFW_TBL_CONTENT} ORDER BY id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrResult); $i++) {
        $intID    = $arrResult[$i]['id'];
        $intCount = $CMS->COM->CountArticleComments($intID);
        $this->Query("UPDATE {IFW_TBL_CONTENT} SET comment_count = $intCount WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
    }
    function RefreshArticleCommentCount($intArticleID) {
      global $CMS;
      $intCount = $CMS->COM->CountArticleComments($intArticleID);
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET comment_count = $intCount WHERE id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    function BulkRefreshArticleCommentCount($strArticleIDs) {
      global $CMS;
      $strArticleIDs = str_replace("(", "", $strArticleIDs);
      $strArticleIDs = str_replace(")", "", $strArticleIDs);
      $arrArticleIDs = explode(",", $strArticleIDs);
      for ($i=0; $i<count($arrArticleIDs); $i++) {
        $intArticleID = $arrArticleIDs[$i];
        $this->RefreshArticleCommentCount($intArticleID);
      }
    }
    // ** Select ** //
    function GetFirstID() {
      $arrItems = $this->ResultQuery("SELECT id FROM {IFW_TBL_CONTENT} LIMIT 1", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if (empty($arrItems)) {
        $intFirstID = 0;
      } else {
        $intFirstID = $arrItems[0]['id'];
      }
      return $intFirstID;
    }
    // ** Bulk Manage Content ** //
    function BulkArticleTitles($strArticleIDs) {
      $arrArticles = $this->ResultQuery("SELECT title FROM {IFW_TBL_CONTENT} WHERE id IN $strArticleIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrArticles;
    }
    function BulkMove($intAreaID, $strArticleIDs) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET content_area_id = $intAreaID WHERE id IN $strArticleIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_ARTICLE_BULKMOVE, $intAreaID, $strArticleIDs);
    }
    function BulkLock($strArticleIDs) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET locked = 'Y' WHERE id IN $strArticleIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_ARTICLE_BULKLOCK, "", $strArticleIDs);
    }
    function BulkUnlock($strArticleIDs) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET locked = 'N' WHERE id IN $strArticleIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_ARTICLE_BULKUNLOCK, "", $strArticleIDs);
    }
    function BulkEditAuthor($strArticleIDs, $intUserID) {
      global $CMS;
      $this->Query("UPDATE {IFW_TBL_CONTENT} SET author_id = $intUserID WHERE id IN $strArticleIDs", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_ARTICLE_BULKEDITAUTHOR, "", $strArticleIDs);
    }
    function BulkPublish($strArticleIDs) {
      global $CMS;
      $dteCurrentDateAndTime = $CMS->SYS->GetCurrentDateAndTime();
      $arrArticleData = $this->ResultQuery("SELECT id, create_date FROM {IFW_TBL_CONTENT} WHERE id IN $strArticleIDs ORDER BY id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrArticleData); $i++) {
        $intID   = $arrArticleData[$i]['id'];
        $dteDate = $arrArticleData[$i]['create_date'];
        if ($dteDate > $dteCurrentDateAndTime) {
          $strContStatus = C_CONT_SCHEDULED;
        } else {
          $strContStatus = C_CONT_PUBLISHED;
        }
        $this->Query("UPDATE {IFW_TBL_CONTENT} SET content_status = '$strContStatus' WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      }
      //$CMS->AL->Build(AL_TAG_ARTICLE_BULKLOCK, "", $strArticleIDs);
    }
    function BulkDelete($strArticleIDs) {
      global $CMS;
      $strArticleIDs = str_replace("(", "", $strArticleIDs);
      $strArticleIDs = str_replace(")", "", $strArticleIDs);
      $arrArticles = explode(",", $strArticleIDs);
      $this->blnBulk = true;
      for ($i=0; $i<count($arrArticles); $i++) {
        $intID = $arrArticles[$i];
        $this->Mark($intID);
      }
      $CMS->AL->Build(AL_TAG_ARTICLE_BULKDELETE, "", $strArticleIDs);
    }
    function BulkRestore($strArticleIDs) {
      global $CMS;
      $strArticleIDs = str_replace("(", "", $strArticleIDs);
      $strArticleIDs = str_replace(")", "", $strArticleIDs);
      $arrArticles = explode(",", $strArticleIDs);
      $this->blnBulk = true;
      for ($i=0; $i<count($arrArticles); $i++) {
        $intID = $arrArticles[$i];
        $this->Restore($intID);
      }
      $CMS->AL->Build(AL_TAG_ARTICLE_BULKRESTORE, "", $strArticleIDs);
    }
    
    /**
     * Gets a summary of the site content for the Archives page
     * @param integer $year
     * @param integer $month
     * @return array
     */
    function getArchivesSummary($year = 0, $month = 0) {
        
        global $CMS;
        
        $year  = $CMS->FilterNumeric($year);
        $month = $CMS->FilterNumeric($month);
        
        if (($year > 0) && ($month > 0)) {
            $havingClause = "HAVING content_yyyy_mm = '$year-$month'";
        } elseif ($year > 0) {
            $havingClause = "HAVING content_yyyy = '$year'";
        } else {
            $havingClause = "";
        }
        
        $result = $CMS->ResultQuery("
            SELECT DATE_FORMAT(create_date, '%Y-%m') AS content_yyyy_mm,
            DATE_FORMAT(create_date, '%Y') AS content_yyyy,
            DATE_FORMAT(create_date, '%m') AS content_mm,
            DATE_FORMAT(create_date, '%M %Y') AS content_date_desc,
            count(*) AS count
            FROM {IFW_TBL_CONTENT}
            WHERE content_status = 'Published'
            GROUP BY content_yyyy_mm
            $havingClause
            ORDER BY create_date DESC
        ", basename(__FILE__), __LINE__);
        
        return $result;
        
    }
    
    /**
     * Gets a list of the site content for the Archives page
     * @param $year
     * @param $month
     * @return array
     */
    function getArchivesContent($year = 0, $month = 0) {
        
        global $CMS;
        
        $year  = $CMS->FilterNumeric($year);
        $month = $CMS->FilterNumeric($month);
        
        if (($year > 0) && ($month > 0)) {
            $whereClause = "AND DATE_FORMAT(create_date, '%Y-%m') = '$year-$month'";
        } elseif ($year > 0) {
            $whereClause = "AND DATE_FORMAT(create_date, '%Y') = '$year'";
        } else {
            $whereClause = "";
        }
        
        $dateFormat = $CMS->SYS->GetDateFormat();
        
        $result = $CMS->ResultQuery("
            SELECT DATE_FORMAT(create_date, '%M %Y') AS content_yyyy_mm,
            DATE_FORMAT(create_date, '%Y') AS content_yyyy,
            DATE_FORMAT(create_date, '%m') AS content_mm,
            id, content_area_id, title,
            DATE_FORMAT(create_date, '$dateFormat') AS content_date_full
            FROM {IFW_TBL_CONTENT}
            WHERE content_status = 'Published'
            $whereClause
            ORDER BY create_date DESC
        ", basename(__FILE__), __LINE__);
        
        return $result;
        
    }
    
}
?>