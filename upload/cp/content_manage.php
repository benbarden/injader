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

  require '../sys/header.php';
  $CMS->RES->ViewManageContent();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "ViewManageContent");
  }
  $strPageTitle = "Manage Content";
  $CMS->AP->SetTitle($strPageTitle);
  
  // Admin
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $blnIsAdmin = false;
  } else {
    $blnIsAdmin = true;
  }

  // Multi-paging
  if (empty($_GET['page'])) {
    $intPageNumber = 1;
  } else {
    $intPageNumber = $_GET['page'];
    if ($intPageNumber < 1) {
      $intPageNumber = 1;
    }
  }
  
  $intAreaID = "";
  $strWhereClause = "";
  $strGetURL = "";
  $intNumPages = 0;

  if ($CMS->RES->CountTotalWriteAccess() < $CMS->AR->CountAreasByAreaType(C_AREA_CONTENT)) {
    $blnAllowEmpty = false;
  } else {
    $blnAllowEmpty = true;
  }

  $blnInvalidSearch = false;
  $strAddArticle = "<br />";
  $strStartupList = "area1";
  $blnNavOK = false;
  if (isset($_GET['navtype'])) {
    switch ($_GET['navtype']) {
      case "1":
        $strGetURL = "?navtype=1";
        if (isset($_GET['area1'])) {
          $intAreaID = $_GET['area1'];
          $strStartupList = "area1";
          $blnNavOK = true;
        }
        break;
      case "2":
        $strGetURL = "?navtype=2";
        if (isset($_GET['area2'])) {
          $intAreaID = $_GET['area2'];
          $strStartupList = "area2";
          $blnNavOK = true;
        }
        break;
      case "3":
        $strGetURL = "?navtype=3";
        if (isset($_GET['area3'])) {
          $intAreaID = $_GET['area3'];
          $strStartupList = "area3";
          $blnNavOK = true;
        }
        break;
    }
  } else {
    $strGetURL = "?navtype=";
  }

  if (!$blnNavOK) {
    if (isset($_GET['area1'])) {
      $intAreaID = $_GET['area1'];
      $strStartupList = "area1";
    } elseif (isset($_GET['area2'])) {
      $intAreaID = $_GET['area2'];
      $strStartupList = "area2";
    } elseif (isset($_GET['area3'])) {
      $intAreaID = $_GET['area3'];
      $strStartupList = "area3";
    }
  }
  
  if (isset($_GET['area1'])) {
    if ($strGetURL) {
      $strGetURL .= "&amp;area1=".$_GET['area1'];
    } else {
      $strGetURL = "?area1=".$_GET['area1'];
    }
  }
  if (isset($_GET['area2'])) {
    if ($strGetURL) {
      $strGetURL .= "&amp;area2=".$_GET['area2'];
    } else {
      $strGetURL = "?area2=".$_GET['area2'];
    }
  }
  if (isset($_GET['area3'])) {
    if ($strGetURL) {
      $strGetURL .= "&amp;area3=".$_GET['area3'];
    } else {
      $strGetURL = "?area3=".$_GET['area3'];
    }
  }
  
  if ($intAreaID) {
    $intAreaID = $CMS->FilterNumeric($intAreaID);
    if ($intAreaID == 0) {
      if ($blnAllowEmpty) {
        $strWhereClause = "";
      } else {
        $blnInvalidSearch = true;
      }
    } else {
      $strWhereClause = " AND content_area_id = $intAreaID ";
      $strAddArticle = "<p><a href=\"{FN_ADM_WRITE}?action=create&amp;area=$intAreaID\">Add an article to this area</a></p>";
    }
  }

  if (empty($_GET['status'])) {
    $strStatus = "";
  } else {
    $strStatus = $_GET['status'];
    $strWhereClause .= " AND content_status = '$strStatus' ";
    if ($strGetURL) {
      $strGetURL .= "&amp;status=$strStatus";
    } else {
      $strGetURL = "?status=$strStatus";
    }
  }
  
  $strUser = "";
  $strInvalidUsername = "";
  
  if (empty($_GET['user'])) {
    $intUserID = "";
    if (empty($strGetURL)) {
      $strGetURL = "?user=$intUserID";
    } else {
      $strGetURL .= "&amp;user=$intUserID";
    }
  } else {
    $strUser = $CMS->FilterAlphanumeric($_GET['user'], C_CHARS_USERNAME);
    $intUserID = $CMS->US->GetIDFromName($strUser);
    if ($intUserID) {
      $strWhereClause .= " AND c.author_id = $intUserID ";
      if (empty($strGetURL)) {
        $strGetURL = "?user=$strUser";
      } else {
        $strGetURL .= "&amp;user=$strUser";
      }
    } else {
      $intUserID = 0;
      $strInvalidUsername = $CMS->AC->InvalidFormData(M_ERR_SEARCH_USER_NOT_FOUND);
      $blnInvalidSearch = true;
    }
    if ($intUserID == 0) {
      if (empty($strGetURL)) {
        $strGetURL = "?user=$intUserID";
      } else {
        $strGetURL .= "&amp;user=$intUserID";
      }
    }
  }
  
  $strTagSelect1 = "checked=\"checked\"";
  $strTagSelect2 = "";
  $strTagSelect3 = "";
  $strTagClause = "";
  if (!empty($_GET['tags'])) {
    if ($_GET['tags'] == 1) {
      $strTagSelect1 = "checked=\"checked\"";
      $strTagClause = "";
    } elseif ($_GET['tags'] == 2) {
      $strTagSelect2 = "checked=\"checked\"";
      $strTagClause = "tags <> ''";
    } elseif ($_GET['tags'] == 3) {
      $strTagSelect3 = "checked=\"checked\"";
      $strTagClause = "tags = ''";
    }
    if (empty($strGetURL)) {
      $strGetURL = "?tags=".$_GET['tags'];
    } else {
      $strGetURL .= "&amp;tags=".$_GET['tags'];
    }
  }
  if ($strTagClause) {
    $strWhereClause .= " AND $strTagClause";
  }
  
  $strTagOptions = <<<TagOptions
<input id="tags1" name="tags" type="radio" value="1" $strTagSelect1 /><label for="tags1">All content</label>
<input id="tags2" name="tags" type="radio" value="2" $strTagSelect2 /><label for="tags2">Content with tags</label>
<input id="tags3" name="tags" type="radio" value="3" $strTagSelect3 /><label for="tags3">Content without tags</label>

TagOptions;

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
  $strAreaListPrimary = $CMS->DD->AreaHierarchy($intAreaID, 0, "Content", $blnAllowEmpty, false, C_NAV_PRIMARY);
  
  $CMS->AT->arrAreaData = array();
  $CMS->DD->strEmptyItem = "All";
  $strAreaListSecondary = $CMS->DD->AreaHierarchy($intAreaID, 0, "Content", $blnAllowEmpty, false, C_NAV_SECONDARY);
  
  $CMS->AT->arrAreaData = array();
  $CMS->DD->strEmptyItem = "All";
  $strAreaListTertiary = $CMS->DD->AreaHierarchy($intAreaID, 0, "Content", $blnAllowEmpty, false, C_NAV_TERTIARY);
    
  $strStatusList   = $CMS->DD->ContentStatus($strStatus);
  $strSearchButton = $CMS->AC->SearchButton();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<form action="{FN_ADM_CONTENT_MANAGE}" method="get">
<table class="DefaultTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup>
  <tr>
    <th colspan="4">Search for content</th>
  </tr>
  <tr>
    <td>
      <b>Nav Type</b>
    </td>
    <td colspan="3">
      <input type="radio" id="navtype1" name="navtype" onclick="SwitchDropDown('area1');" value="1"$strNavType1Checked /><label for="navtype1">Primary</label>
      <input type="radio" id="navtype2" name="navtype" onclick="SwitchDropDown('area2');" value="2"$strNavType2Checked /><label for="navtype2">Secondary</label>
      <input type="radio" id="navtype3" name="navtype" onclick="SwitchDropDown('area3');" value="3"$strNavType3Checked /><label for="navtype3">Tertiary</label>
    </td>
  </tr>
  <tr>
    <td>
      <b>Area</b>
    </td>
    <td colspan="3">
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
    <td>
      <label for="status"><b>Status</b></label>
    </td>
    <td>
      $strStatusList
    </td>
    <td>
      <label for="user"><b>User</b></label>
    </td>
    <td>
      $strInvalidUsername
      <input type="text" id="user" name="user" size="20" maxlength="100" value="$strUser" />
    </td>
  </tr>
  <tr>
    <td>
      <b>Tag Options</b>
    </td>
    <td colspan="3">
$strTagOptions
    </td>
  </tr>
  <tr>
    <td class="FootColour Centre" colspan="4">
      $strSearchButton
    </td>
  </tr>
</table>
</form>

END;

  // ** List all content in area ** //
  if (!$blnInvalidSearch) {
    if (isset($intAreaID) && ($intAreaID <> "")) {
      // Page numbers
      $intContentPerPage = $CMS->SYS->GetSysPref(C_PREF_SYSTEM_PAGE_COUNT);
      $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
      // Get content
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $strContentSQL = "
        SELECT c.id, c.title, c.seo_title, c.content_status, c.hits, c.comment_count, c.content_area_id,
        DATE_FORMAT(c.create_date, '$strDateFormat') AS create_date, c.create_date AS create_date_raw,
        a.name AS area_name, a.seo_name AS area_seo_name
        FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a)
        LEFT JOIN {IFW_TBL_USERS} u ON c.author_id = u.id
        WHERE c.content_area_id = a.id $strWhereClause
        ORDER BY c.id ASC
        LIMIT $intStart, $intContentPerPage
      ";
      // LIMIT $intStart, $intContentPerPage
      $arrAreaContent = $CMS->ResultQuery($strContentSQL, basename(__FILE__), __LINE__);
      $strCountSQL = "SELECT count(*) AS count FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a) WHERE c.content_area_id = a.id $strWhereClause ORDER BY c.id ASC";
      $arrAreaCount = $CMS->ResultQuery($strCountSQL, basename(__FILE__), __LINE__);
      $intAreaCount = $arrAreaCount[0]['count'];
      // Page number links
      $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intAreaCount);
      $strPageNumbers = $CMS->PNN->Make($intNumPages, $intPageNumber, basename(__FILE__)."$strGetURL");
      // Loopity loop
      for ($i=0; $i<count($arrAreaContent); $i++) {
        $intID           = $arrAreaContent[$i]['id'];
        $intAreaID       = $arrAreaContent[$i]['content_area_id'];
        $strAreaName     = $arrAreaContent[$i]['area_name'];
        $strSEOAreaName  = $arrAreaContent[$i]['area_seo_name'];
        $CMS->PL->SetTitle($strSEOAreaName);
        $strAreaLink     = $CMS->PL->ViewArea($intAreaID);
        $strCreateDate   = $arrAreaContent[$i]['create_date'];
        $strTitle        = $arrAreaContent[$i]['title'];
        $strStatus       = $arrAreaContent[$i]['content_status'];
        $strSEOTitle     = $arrAreaContent[$i]['seo_title'];
        $intHits         = $arrAreaContent[$i]['hits'];
        $intComments     = $arrAreaContent[$i]['comment_count'];
        // Make page link
        $CMS->PL->SetTitle($strSEOTitle);
        $strViewLink = $CMS->PL->ViewArticle($intID);
        // Table header
        if ($i == 0) {
          // Bulk options
          if ($blnIsAdmin) {
            if ($strStatus == C_CONT_REVIEW) {
              $strBulkOptions = <<<AdminBulk
          <option value="publish">Publish</option>
          <option value="delete">Delete</option>

AdminBulk;
            } else {
              $strBulkOptions = <<<AdminBulk
          <option value="move">Move</option>
          <option value="lock">Lock</option>
          <option value="unlock">Unlock</option>
          <option value="editauthor">Edit Author</option>
          <option value="delete">Delete</option>
          <option value="restore">Restore</option>

AdminBulk;
            }
          } else {
            $strBulkOptions = "<option value=\"\">&nbsp;</option>";
          }
          // No longer used
          // <!-- $strPageNumbers -->
          // Build header
          $strHTML .= <<<TableHeader
$strAddArticle
$strPageNumbers
  <!--<form action="{FN_ADM_CONTENT_MANAGE}" method="post">-->
  <!--</form>-->
<form action="{FN_ADM_CONTENT_BULK}" method="post">
<table id="tblArticles" class="DefaultTable PageTable" cellspacing="1">
    <colgroup>
      <col class="BaseColour TinyCell" />
      <col class="BaseColour WideCell" />
      <col class="BaseColour MediumCell" />
      <col class="BaseColour MediumCell" />
      <col class="BaseColour NarrowCell" />
      <col class="BaseColour TinyCell" />
      <col class="BaseColour TinyCell" />
      <col class="BaseColour NarrowCell" />
      <col class="BaseColour TinyCell" />
    </colgroup>
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Area</th>
        <th>Created</th>
        <th>Status</th>
        <th>Comments</th>
        <th>Hits</th>
        <th>Options</th>
        <th>
          <a href="#FooterRow1" id="js-manage-content-check-all" style="color: #fff; text-decoration: none;" title="Toggle All">+</a>
        </th>
      </tr>
    </thead>
    <tfoot>
      <tr id="FooterRow1">
        <td class="FootColour" colspan="5">
        </td>
        <td class="FootColour" style="text-align: right;" colspan="4">
          <select name="optBulk" style="margin-top: 5px;">
$strBulkOptions
          </select>
          <input type="submit" value="{M_BTN_PROCEED}" />
        </td>
      </tr>
    </tfoot>
    <tbody id="tblArticlesBody">

TableHeader;
        }
        // Permissions
        $CMS->RES->ClearErrors();
        $CMS->RES->EditArticle($intAreaID, $intID);
        if ($CMS->RES->IsError()) {
          $strEditArticle = "";
        } else {
          $strEditArticle = "<a href=\"{FN_ADM_WRITE}?action=edit&amp;id=$intID\" title=\"Edit this article\">Edit</a>";
        }
        if ($blnIsAdmin) {
          $strEditTags = "<a href=\"{FN_ADM_CONTENT_EDITTAGS}?id=$intID\">Tags</a>";
        } else {
          $strEditTags = "";
        }
        // Build row
        $strRowClass = "even";
        /*
        if (($i % 2) == 0) {
          $strRowClass = "even";
        } else {
          $strRowClass = "odd";
        }
        */
        $strRowID = "mRow".$i;
        $strHTML .= <<<AreaContent
      <tr id="$strRowID" class="$strRowClass">
        <td class="Centre ID">$intID</td>
        <td class="Left Title"><a href="$strViewLink" title="View this article">$strTitle</a></td>
        <td class="Centre Area"><a href="$strAreaLink">$strAreaName</a></td>
        <td class="Centre Created">$strCreateDate</td>
        <td class="Centre Status">$strStatus</td>
        <td class="Centre Comments">$intComments</td>
        <td class="Centre Hits">$intHits</td>
        <td class="Centre Options">$strEditArticle $strEditTags</td>
        <td class="Centre Checkbox"><input type="checkbox" name="chkBulkOptions[]" id="chkBulkOptions$intID" value="$intID" /></td>
      </tr>

AreaContent;
      }
      if (count($arrAreaContent) > 0) {
        $strHTML .= <<<TableFooter
    </tbody>
</table>
</form>

TableFooter;
      } else {
        $strHTML .= "<p>No content found with the specified search criteria.</p>$strAddArticle";
      }
    }
  }

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

    var checkAll = true;
    $('#js-manage-content-check-all').on('click', function() {
        ToggleCheckboxes(checkAll);
        checkAll = !checkAll;
    });
</script>

FooterScript;
  
  $CMS->AP->Display($strHTML);
?>