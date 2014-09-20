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

  class Ratings extends Helper {
    // ** Core functions ** //
    function Create($intArticleID, $intCommentID, $intRatingValue, $strUserIP, $intUserID) {
      $intID = $this->Query("INSERT INTO {IFW_TBL_RATINGS}(article_id, comment_id, rating_value, ip_address, user_id) VALUES($intArticleID, $intCommentID, $intRatingValue, '$strUserIP', $intUserID)", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $intID;
    }
    function Delete($intArticleID) {
      $this->Query("DELETE FROM {IFW_TBL_RATINGS} WHERE article_id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** Checking ** //
    function HasUserAlreadyVoted($intArticleID, $intUserID) {
      if (!$intUserID) {
        return false;
      } else {
        $arrResult = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_RATINGS} WHERE article_id = $intArticleID AND user_id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrResult[0]['count'] > 0 ? true : false;
      }
    }
    // ** Get ** //
    function GetArticleRating($intArticleID, $intUserID) {
      $arrResult = $this->ResultQuery("SELECT rating_value FROM {IFW_TBL_RATINGS} WHERE article_id = $intArticleID AND user_id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return empty($arrResult[0]['rating_value']) ? "" : $arrResult[0]['rating_value'];
    }
    function GetCommentRating($intCommentID, $intUserID) {
      $arrResult = $this->ResultQuery("SELECT rating_value FROM {IFW_TBL_RATINGS} WHERE comment_id = $intCommentID AND user_id = $intUserID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return empty($arrResult[0]['rating_value']) ? "" : $arrResult[0]['rating_value'];
    }
    function GetNumberOfRatings($intArticleID) {
      $arrResult = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_RATINGS} WHERE article_id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['count'];
    }
    function GetAverageRating($intArticleID) {
      $arrResult = $this->ResultQuery("SELECT avg(rating_value) AS average_rating FROM {IFW_TBL_RATINGS} WHERE article_id = $intArticleID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return round((float) $arrResult[0]['average_rating'], 2);
    }
  }

?>