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

  class RSSBuilder extends Helper {
    
    var $blnDisplay = false; // If true, RSS data is returned
    
    /************************* CUSTOM **************************/
    
    function DisplayArticleRSS($strAreaClause, $strWhereClause, $strRSSURL) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $dteToday     = $CMS->SYS->GetCurrentDateAndTime();
      $strSiteTitle = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      $strSiteDesc  = $CMS->SYS->GetSysPref(C_PREF_SITE_DESCRIPTION);
      $intRSSCount  = $CMS->SYS->GetSysPref(C_PREF_RSS_COUNT);
      $strRSSPath      = ABS_ROOT.$strRSSURL;
      $strRSSLinkAbout = "http://{SVR_HOST}{URL_ROOT}";
      $strRSSLinkRoot  = "http://{SVR_HOST}";
      $strSQL = "SELECT c.id, c.title, c.seo_title, c.content, c.author_id, c.content_area_id, DATE_FORMAT(c.create_date,'%d/%m/%y') AS con_created, c.create_date AS rss_date FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a) WHERE c.content_area_id = a.id $strAreaClause $strWhereClause ORDER BY c.create_date DESC";
      $arrNewContent = $this->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__);
      $arrNewContentCount = count($arrNewContent);
      if ($arrNewContentCount > $intRSSCount) {
        $intArrayLimit = $intRSSCount;
      } else {
        $intArrayLimit = $arrNewContentCount;
      }
      $strRSSFileContents = $CMS->AC->RSSHeader($strSiteTitle, $strRSSLinkAbout, $strSiteDesc, $dteToday);
      for ($i=0; $i<$intArrayLimit; $i++) {
        $intContentID    = $arrNewContent[$i]['id'];
        $strContentTitle = $arrNewContent[$i]['title'];
        //$strContentTitle = $this->DoEntities($strContentTitle);
        $strSEOTitle     = $arrNewContent[$i]['seo_title'];
        $strContentBody  = $arrNewContent[$i]['content'];
        $strContentBody  = strip_tags($strContentBody);
        $strContentBody  = $this->DoEntities($strContentBody);
        $strContentBody  = str_replace("&amp;nbsp;", " ", $strContentBody);
        $strContentBody  = substr($strContentBody, 0, 300);
        if (strlen($arrNewContent[$i]['content']) > 300) {
          $strContentBody .= "...";
        }
        //$strContentDesc   = $this->DoEntities($strContentBody);
        $dteRSS           = $arrNewContent[$i]['rss_date'];
        $dteRSS           = date('r', strtotime($dteRSS));
        $intAreaID        = $arrNewContent[$i]['content_area_id'];
        $intAuthorID      = $arrNewContent[$i]['author_id'];
        $strAuthorName    = $CMS->US->GetNameFromID($intAuthorID);
        $CMS->RES->ViewArea($intAreaID);
        if (!$CMS->RES->IsError()) {
          $CMS->PL->SetTitle($strSEOTitle);
          $strArticleLink = $strRSSLinkRoot.$CMS->PL->ViewArticle($intContentID);
          $strCommentRSS = $strRSSLinkRoot.FN_FEEDS."?name=comments&amp;id=$intContentID";
          $strRSSFileContents .= <<<RSSFile
    <item>
      <title>$strContentTitle</title>
      <link>$strArticleLink</link>
      <guid>$strArticleLink</guid>
      <description>
      <![CDATA[
      $strContentBody
      ]]>
      </description>
      <pubDate>$dteRSS</pubDate>
      <dc:creator>$strAuthorName</dc:creator>
      <wfw:commentRss>$strCommentRSS</wfw:commentRss>
    </item>

RSSFile;
        }
      }
      $strRSSFileContents .= "  </channel>\n</rss>\n";
      $strHTML = $CMS->RC->DoAll($strRSSFileContents);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }

    /************************* ARTICLES **************************/

    function GetArticleRSS($intAreaID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->blnDisplay = true;
      $strHTML = $this->BuildArticleRSS($intAreaID);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }

    function BuildArticleRSS($intAreaID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      $dteToday     = $CMS->SYS->GetCurrentDateAndTime();
      $strAreaName  = "";
      $strAreaDesc  = "";
      if ($intAreaID) {
        $arrArea      = $CMS->AR->GetArea($intAreaID);
        $strAreaName  = $arrArea['name'];
        $strAreaDesc  = $arrArea['area_description'];
      }
      if (!$strAreaName) {
        $strAreaName = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      }
      if ($strAreaDesc) {
        $strAreaDesc = strip_tags($strAreaDesc);
      } else {
        $strSiteDesc = $CMS->SYS->GetSysPref(C_PREF_SITE_DESCRIPTION);
        $strAreaDesc = strip_tags($strSiteDesc);
      }
      $intRSSCount  = $CMS->SYS->GetSysPref(C_PREF_RSS_COUNT);
      $strRSSLinkAbout = "http://{SVR_HOST}{URL_ROOT}";
      $strRSSLinkRoot  = "http://{SVR_HOST}";
      if ($intAreaID) {
        $strAreaClause = " AND a.id = $intAreaID ";
      } else {
        $strAreaClause = " AND include_in_rss_feed = 'Y' ";
      }
      $arrNewContent = $this->ResultQuery("SELECT con.id, con.author_id, title, seo_title, content, content_area_id, DATE_FORMAT(create_date,'%d/%m/%y') AS con_created, create_date AS rss_date, a.include_in_rss_feed FROM ({IFW_TBL_CONTENT} con, {IFW_TBL_AREAS} a) WHERE con.content_area_id = a.id AND content_status = '{C_CONT_PUBLISHED}' $strAreaClause ORDER BY create_date DESC LIMIT $intRSSCount", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      // Sun, 19 May 2002 15:21:36 GMT
      
      $strRSSFileContents = $CMS->AC->RSSHeader($strAreaName, $strRSSLinkAbout, $strAreaDesc, $dteToday);
      
      for ($i=0; $i<count($arrNewContent); $i++) {
        $intContentID     = $arrNewContent[$i]['id'];
        $strContentTitle  = $arrNewContent[$i]['title'];
        $strSEOTitle      = $arrNewContent[$i]['seo_title'];
        
        $strContentBody   = $arrNewContent[$i]['content'];
        $strContentBody   = str_replace("<br><br>", " ", $strContentBody);
        $strContentBody   = str_replace("\r", " ", $strContentBody);
        
        $strReadMoreEditor = $CMS->AC->ReadMoreEditor();
        $strReadMorePublic = $CMS->AC->ReadMorePublic();
        if (strpos($strContentBody, $strReadMorePublic) !== false) {
            $strContentBody = str_replace($strReadMorePublic, "", $strContentBody);
        } elseif (strpos($strContentBody, $strReadMoreEditor) !== false) {
            $strContentBody = str_replace($strReadMoreEditor, "", $strContentBody);
        }
        //$strContentBody   = $this->DoEntities($strContentBody);
        
        $dteRSS           = $arrNewContent[$i]['rss_date'];
        // D, j M Y H:i:s e
        $dteRSS           = date('r', strtotime($dteRSS));
        $intAreaID        = $arrNewContent[$i]['content_area_id'];
        $intAuthorID      = $arrNewContent[$i]['author_id'];
        $strAuthorName    = $CMS->US->GetNameFromID($intAuthorID);
        $strIncludeInFeed = $intAreaID ? "Y" : $arrNewContent[$i]['include_in_rss_feed'];
        if ($strIncludeInFeed == "Y") {
          $CMS->RES->ViewArea($intAreaID);
          if (!$CMS->RES->IsError()) {
            $CMS->PL->SetTitle($strSEOTitle);
            $strArticleLink = $strRSSLinkRoot.$CMS->PL->ViewArticle($intContentID);
            $strCommentRSS = $strRSSLinkRoot.FN_FEEDS."?name=comments&amp;id=$intContentID";
            $strRSSFileContents .= <<<RSSFile
    <item>
      <title>$strContentTitle</title>
      <link>$strArticleLink</link>
      <guid>$strArticleLink</guid>
      <description>
      <![CDATA[
      $strContentBody
      ]]>
      </description>
      <pubDate>$dteRSS</pubDate>
      <dc:creator>$strAuthorName</dc:creator>
      <wfw:commentRss>$strCommentRSS</wfw:commentRss>
    </item>

RSSFile;
          }
        }
      }
      $strRSSFileContents .= "  </channel>\n</rss>\n";
      $strRSSFileContents = $CMS->RC->DoAll($strRSSFileContents);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strRSSFileContents;

    }
    
    /************************* COMMENTS **************************/
    
    function GetCommentRSS($intArticleID) {
      $dteStartTime = $this->MicrotimeFloat();
      $this->blnDisplay = true;
      $strHTML = $this->BuildCommentRSS($intArticleID);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strHTML;
    }

    function BuildCommentRSS($intArticleID) {
      global $CMS;
      if (!$intArticleID) {
        exit;
      }
      $dteStartTime = $this->MicrotimeFloat();
      $dteToday     = $CMS->SYS->GetCurrentDateAndTime();
      $strSiteTitle = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      $arrArticle   = $CMS->ART->GetArticle($intArticleID);
      $strContTitle = $arrArticle['title'];
      $intRSSCount  = $CMS->SYS->GetSysPref(C_PREF_RSS_COUNT);
      $strRSSLinkAbout = "http://{SVR_HOST}{URL_ROOT}";
      $strRSSLinkRoot  = "http://{SVR_HOST}";
      if ($intArticleID) {
        $strArticleClause = " AND story_id = $intArticleID ";
      } else {
        $strArticleClause = "";
      }
      $arrNewComments = $this->ResultQuery("SELECT com.id, com.story_id, com.content, con.content_area_id, con.title AS con_title, con.seo_title AS con_seo_title, com.create_date AS rss_date, content_status FROM {IFW_TBL_COMMENTS} com LEFT JOIN {IFW_TBL_CONTENT} con ON com.story_id = con.id WHERE comment_status = 'Approved' $strArticleClause GROUP BY rss_date DESC LIMIT $intRSSCount", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      
      $strRSSFileContents = $CMS->AC->RSSHeader("Comments Feed - $strContTitle", $strRSSLinkAbout, "", $dteToday);

      for ($j=0; $j<count($arrNewComments); $j++) {
        $CMS->RES->ClearErrors();
        $intCommentID   = $arrNewComments[$j]['id'];
        $intArticleID   = $arrNewComments[$j]['story_id'];
        //$strContentTitle  = $this->DoEntities($strContentTitle);
        $strCommentBody = $arrNewComments[$j]['content'];
        $strCommentBody = str_replace("<br><br>", " ", $strCommentBody);
        $strCommentBody = str_replace("\r", " ", $strCommentBody);
        //$strCommentBody = $this->DoEntities($strCommentBody);
        $dteRSS           = $arrNewComments[$j]['rss_date'];
        // D, j M Y H:i:s e
        $dteRSS           = date('r', strtotime($dteRSS));
        $intContentAreaID = $arrNewComments[$j]['content_area_id'];
        if ($intArticleID > 0) {
          $CMS->RES->ViewArea($intContentAreaID);
          if (!$CMS->RES->IsError()) {
            $CMS->PL->SetTitle($arrNewComments[$j]['con_seo_title']);
            $strNewItemURL = $strRSSLinkRoot.$CMS->PL->ViewArticle($intArticleID)."#c".$intCommentID;
            $strCommentTitle = $arrNewComments[$j]['con_title'];
            //$strCommentTitle = $this->DoEntities($strCommentTitle);
          } else {
            $strNewItemURL = "";
          }
        }
        if ($strNewItemURL <> "") {
          $strRSSFileContents .= <<<RSSFile
    <item>
      <title>$strCommentTitle</title>
      <link>$strNewItemURL</link>
      <guid>$strNewItemURL</guid>
      <description>
      <![CDATA[
      $strCommentBody
      ]]>
      </description>
      <pubDate>$dteRSS</pubDate>
    </item>

RSSFile;
        }
      }
      $strRSSFileContents .= "  </channel>\n</rss>\n";
      //$strRSSFileContents .= "</rdf:RDF>\n";
      $strRSSFileContents = $CMS->RC->DoAll($strRSSFileContents);
      $dteEndTime = $this->MicrotimeFloat();
      $this->SetExecutionTime($dteStartTime, $dteEndTime, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $strRSSFileContents;
    }
    
  } 
