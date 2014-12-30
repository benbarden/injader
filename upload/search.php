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

  require 'sys/header.php';
  $strPageTitle = "Search";

  $blnSubmitForm = false;
  $strFreeTextSQL = "";
  $strTags = "";
  $strQuery = "";
  $strReQuery = "";
  $strAreaClause = "";
  $strWhereClause = "";
  $strMissingSearchString = ""; $strMissingTags = "";
  $strMasterList = ""; $strMultiList = "";
  
  // Multi-paging
  if (!empty($_GET['page'])) {
    $intPageNumber = $_GET['page'];
    if ($intPageNumber < 1) {
      $intPageNumber = 1;
    }
  } else {
    $intPageNumber = 1;
  }
  
  $strReQuery = "?go=yes";
  
  //$intAreaID = empty($_GET['a']) ? 0 : $CMS->FilterNumeric($_GET['a']);
  $intAreaID = 0;
  
  $strStartupList = "area1";
  $blnNavOK = false;
  if (isset($_GET['navtype'])) {
    switch ($_GET['navtype']) {
      case "1":
        if (isset($_GET['area1'])) {
          $intAreaID = $CMS->FilterNumeric($_GET['area1']);
          $strStartupList = "area1";
          $blnNavOK = true;
        }
        break;
      case "2":
        if (isset($_GET['area2'])) {
          $intAreaID = $CMS->FilterNumeric($_GET['area2']);
          $strStartupList = "area2";
          $blnNavOK = true;
        }
        break;
      case "3":
        if (isset($_GET['area3'])) {
          $intAreaID = $CMS->FilterNumeric($_GET['area3']);
          $strStartupList = "area3";
          $blnNavOK = true;
        }
        break;
    }
  }
  if (!$blnNavOK) {
    if (isset($_GET['area1'])) {
      $intAreaID = $CMS->FilterNumeric($_GET['area1']);
      $strStartupList = "area1";
    } elseif (isset($_GET['area2'])) {
      $intAreaID = $CMS->FilterNumeric($_GET['area2']);
      $strStartupList = "area2";
    } elseif (isset($_GET['area3'])) {
      $intAreaID = $CMS->FilterNumeric($_GET['area3']);
      $strStartupList = "area3";
    }
  }
  if (isset($intAreaID)) {
    if ($intAreaID > 0) {
      $strAreaClause = "AND a.id = $intAreaID ";
    }
    $strReQuery .= "&amp;a=$intAreaID";
  }

  if (!empty($_GET['go'])) {
    if (!empty($_GET['q'])) {
      $blnSubmitForm = true;
      $strQuery = $CMS->FilterAlphanumeric($_GET['q'], C_CHARS_SEARCH);
      if ($strQuery) {
        if ($strReQuery) {
          $strReQuery .= "&amp;q=$strQuery";
        } else {
          $strReQuery = "?q=$strQuery";
        }
      }
    }
    if (!empty($_GET['t'])) {
      $blnSubmitForm = true;
      $strTags = $CMS->FilterAlphanumeric($_GET['t'], C_CHARS_SEARCH);
      if ($strTags) {
        if ($strReQuery) {
          $strReQuery .= "&amp;t=$strTags";
        } else {
          $strReQuery = "?t=$strTags";
        }
      }
    }
    if ((!$blnSubmitForm) && ($_GET)) {
      $strMissingSearchString = $CMS->AC->InvalidFormData(M_ERR_MISSING_SEARCH_PARAMS);
    }
    // Get tags
    $blnValidTags = false;
    if ($strTags) {
      $blnValidTags = true;
      $strTagSQL = $CMS->AddSlashesIFW($strTags);
      if (strpos($strTags, ",") !== false) {
        // Multiple tags
        $arrTagSQL = explode(",", $strTagSQL);
        for ($i=0; $i<count($arrTagSQL); $i++) {
          $strTagTemp = trim($arrTagSQL[$i]);
          if ($strTagTemp) {
            if ($CMS->TG->Exists($strTagTemp)) {
              $strArticleList = $CMS->TG->GetArticleList($strTagTemp);
              if (!empty($strArticleList)) {
                if ($strMasterList) {
                  $strMasterList .= ",".$strArticleList;
                } else {
                  $strMasterList = $strArticleList;
                }
                if ($strMultiList) {
                  $strMultiList .= "AND c.id IN ($strArticleList) ";
                } else {
                  $strMultiList = "AND c.id IN ($strArticleList) ";
                }
              }
            }
          }
        }
        $blnMatchAll = true;
        if ($blnMatchAll) {
          $strWhereClause = $strMultiList;
        } else {
          $strWhereClause = $strMasterList;
        }
      } else {
        if ($CMS->TG->Exists($strTagSQL)) {
          $strArticleList = $CMS->TG->GetArticleList($strTagSQL);
          if (!empty($strArticleList)) {
            $strWhereClause = "AND c.id IN ($strArticleList) ";
          }
        }
      }
      if (!$strWhereClause) {
        $blnValidTags = false;
      }
    }
    // Free text search
    if ($strQuery) {
      $strSearchString = $CMS->AddSlashesIFW($strQuery);
      if (strpos($strSearchString, ",") !== false) {
        // Multiple words
        $arrSearchSQL = explode(",", $strSearchString);
        for ($i=0; $i<count($arrSearchSQL); $i++) {
          $strSearchTemp = trim($arrSearchSQL[$i]);
          if ($strSearchTemp) {
            if ($strFreeTextSQL) {
              $strFreeTextSQL .= " OR (MATCH(c.content, c.title) AGAINST('$strSearchTemp')) ";
            } else {
              $strFreeTextSQL = "AND (MATCH(c.content, c.title) AGAINST('$strSearchTemp') ";
            }
          }
        }
        $strFreeTextSQL .= ")";
      } else {
        // One word
        if ($strSearchString) {
          $strFreeTextSQL = "AND (MATCH(c.content, c.title) AGAINST('$strSearchString')) ";
        } else {
          $strFreeTextSQL = "";
        }
      }
      $strWhereClause .= $strFreeTextSQL;
    }
    if (!$strWhereClause) {
      // We can't just have an area ID
      $blnSubmitForm = false;
      if (!$blnValidTags) {
        $strMissingTags = $CMS->AC->InvalidFormData(M_ERR_TAGS_NOT_FOUND);
      }
    }
  }
  
  $strNavType1Checked = "";
  $strNavType2Checked = "";
  $strNavType3Checked = "";
  switch ($strStartupList) {
    case "area1":
      $strNavType1Checked = " checked=\"checked\"";
      break;
    case "area2":
      $strNavType2Checked = " checked=\"checked\"";
      break;
    case "area3":
      $strNavType3Checked = " checked=\"checked\"";
      break;
    default:
      $strStartupList = "area1";
      $strNavType1Checked = " checked=\"checked\"";
      break;
  }

  $CMS->AT->arrAreaData = array();
  $CMS->DD->strEmptyItem = "All";
  $strAreaListPrimary = $CMS->DD->AreaHierarchy($intAreaID, "", "Content", true, true, C_NAV_PRIMARY);
  $CMS->AT->arrAreaData = array();
  $CMS->DD->strEmptyItem = "All";
  $strAreaListSecondary = $CMS->DD->AreaHierarchy($intAreaID, "", "Content", true, true, C_NAV_SECONDARY);
  $CMS->AT->arrAreaData = array();
  $CMS->DD->strEmptyItem = "All";
  $strAreaListTertiary = $CMS->DD->AreaHierarchy($intAreaID, "", "Content", true, true, C_NAV_TERTIARY);
  
  $strSearchButton = $CMS->AC->SearchButton();
  
  $strSearch = FN_SEARCH;
  $strHTML = <<<END
<div id="pagecontent">
<h1>$strPageTitle</h1>
<form action="$strSearch" method="get">
<div style="text-align: left;">
<table class="DefaultTable MediumTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>Search</b></td>
  </tr>
  <tr>
    <td class="InfoColour NarrowCell">
      <label for="q"><b>Search query</b></label>
    </td>
    <td class="BaseColour">
      <input id="go" name="go" type="hidden" value="yes" />
      $strMissingSearchString
      <input id="q" name="q" type="text" size="30" maxlength="100" value="$strQuery" />
    </td>
  </tr>
  <tr>
    <td class="InfoColour NarrowCell">
      <label for="t"><b>Tag search</b></label>
    </td>
    <td class="BaseColour">
      $strMissingTags
      <input id="t" name="t" type="text" size="30" value="$strTags" />
    </td>
  </tr>
  <tr>
    <td>
      <b>Nav Type</b>
    </td>
    <td>
      <input type="radio" id="navtype1" name="navtype" onclick="SwitchDropDown('area1');" value="1"$strNavType1Checked /><label for="navtype1">Primary</label>
      <input type="radio" id="navtype2" name="navtype" onclick="SwitchDropDown('area2');" value="2"$strNavType2Checked /><label for="navtype2">Secondary</label>
      <input type="radio" id="navtype3" name="navtype" onclick="SwitchDropDown('area3');" value="3"$strNavType3Checked /><label for="navtype3">Tertiary</label>
    </td>
  </tr>
  <tr>
    <td>
      <b>Area</b>
    </td>
    <td>
      <select id="area1" name="area1">
$strAreaListPrimary
      </select>
      <select id="area2" name="area2">
$strAreaListSecondary
      </select>
      <select id="area3" name="area3">
$strAreaListTertiary
      </select>
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">$strSearchButton</td>
  </tr>
</table>
</div>
</form>
<script type="text/javascript">
//<![CDATA[
  document.getElementById("q").focus();
//]]>
</script>

END;

  // ** Final submission ** //
  if ($blnSubmitForm) {
    // RSS mode
    $strRSSURL = FN_SEARCH."$strReQuery&amp;mode=rss";
    if (!empty($_GET['mode']) && ($_GET['mode'] == "rss")) {
      $RSS = new RSSBuilder;
      $strFeed = $RSS->DisplayArticleRSS($strAreaClause, $strWhereClause, $strRSSURL);
      exit($strFeed);
    }
    // Page numbers
    $intContentPerPage = 10;
    $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
    // ** Per-article permissions ** //
    $CMS->RES->Admin();
    if (!$CMS->RES->IsError()) {
      $strUserGroupSQL = "";
    } else {
      $intCurrentUserID = $CMS->RES->GetCurrentUserID();
      if ($intCurrentUserID) {
        $strCurrentUserGroups = $CMS->US->GetUserGroups($intCurrentUserID);
      } else {
        $strCurrentUserGroups = "";
      }
      $strUserGroupSQL = $CMS->UG->BuildUserGroupSQL("c", $strCurrentUserGroups, false, true);
    }
    $CMS->RES->ClearErrors();
    // Standard mode
    $strSQL = "SELECT c.id, c.title, c.content, a.name, a.seo_name, c.content_area_id, c.tags, c.seo_title FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a) WHERE c.content_area_id = a.id AND content_status = '{C_CONT_PUBLISHED}' $strAreaClause $strWhereClause $strUserGroupSQL LIMIT $intStart, $intContentPerPage";
    $arrResult = $CMS->ResultQuery($strSQL, basename(__FILE__), __LINE__);
    $arrItemCount = $CMS->ResultQuery("SELECT count(*) AS count FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a) WHERE c.content_area_id = a.id $strAreaClause $strWhereClause $strUserGroupSQL", basename(__FILE__), __LINE__);
    $intItemCount = $arrItemCount[0]['count'];
    // Page number links
    $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intItemCount);
    $strPageNumbers = $CMS->PNN->Make($intNumPages, $intPageNumber, FN_SEARCH.$strReQuery);
    // Loopity loop
    $intViewableItems = 0;
    for ($i=0; $i<count($arrResult); $i++) {
      $intID       = $arrResult[$i]['id'];
      $strTitle    = $arrResult[$i]['title'];
      $strBody     = $arrResult[$i]['content'];
      $strBody     = strip_tags($strBody);
      $strArea     = $arrResult[$i]['name'];
      $strSEOAName = $arrResult[$i]['seo_name'];
      $intAreaID   = $arrResult[$i]['content_area_id'];
      $strTags     = $arrResult[$i]['tags'];
      $strSEOTitle = $arrResult[$i]['seo_title'];
      $strIntro = substr($strBody, 0, 200);
      if (strlen($strBody) > 200) {
        $strIntro .= "...";
      }
      $blnAddToResults = false;
      // Is this the tag that was searched for?
      if (!empty($strTag)) {
        if ($CMS->TG->MatchTag($strTags, $strTag) == true) {
          $blnAddToResults = true;
        } else {
          $blnAddToResults = false;
        }
      } else {
        $blnAddToResults = true;
      }
      // Can user view this article?
      $CMS->RES->ViewArea($intAreaID);
      if ((!$CMS->RES->IsError()) && ($blnAddToResults == true)) {
        $intViewableItems++;
        if ($intViewableItems == 1) {
          $strHTML .= <<<ResultHeader
<h2>Results</h2>
$strPageNumbers
<p><a href="$strRSSURL">Bookmark search results as an RSS feed</a></p>
<div id="search-results">
ResultHeader;
        }
        // Make page link
        $CMS->PL->SetTitle($strSEOTitle);
        $strViewLink = $CMS->PL->ViewArticle($intID);
        $CMS->PL->SetTitle($strSEOAName);
        $strAreaLink = $CMS->PL->ViewArea($intAreaID);
        // Build item HTML
        $strHTML .= <<<SearchItem
<div class="search-item">
<div class="title"><a href="$strViewLink">$strTitle</a></div> <div class="area">in area: <a href="$strAreaLink">$strArea</a></div>
<div class="intro">$strIntro</div>
</div>

SearchItem;
      }
    }
    if ($intViewableItems > 0) {
      $strHTML .= "</div>\n";
    } else {
      $strHTML .= "<p>No results found! Try refining your search criteria.</p>\n";
    }
  }
  $strHTML .= "</div>\n";

  // ** SCRIPT ** //
  $strHTML .= <<<FooterScript
<script type="text/javascript">
  function SwitchDropDown(strWhich) {
    document.getElementById('area1').style.display  = 'none';
    document.getElementById('area2').style.display  = 'none';
    document.getElementById('area3').style.display  = 'none';
    document.getElementById(strWhich).style.display = 'block';
  }
  SwitchDropDown('$strStartupList'); // do on startup
</script>

FooterScript;
  
  $CMS->MV->DefaultPage($strPageTitle, $strHTML);
?>