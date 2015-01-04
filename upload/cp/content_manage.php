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

  require '../sys/header.php';
  $cpItemsPerPage = $cmsContainer->getService('Cms.Config')->getByKey('CP.ItemsPerPage');
  if (!$CMS->RES->CanAddContent()) {
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
  
  $categoryId = "";
  $strWhereClause = "";
  $strGetURL = "";
  $intNumPages = 0;

  $blnAllowEmpty = true;

  $blnInvalidSearch = false;
  $strAddArticle = "<br />";

  if (isset($_GET['area'])) {
      $categoryId = $CMS->FilterNumeric($_GET['area']);
    if ($strGetURL) {
      $strGetURL .= "&amp;area=".$_GET['area'];
    } else {
      $strGetURL = "?area=".$_GET['area'];
    }
  }

  if ($categoryId) {
    if ($categoryId == 0) {
      if ($blnAllowEmpty) {
        $strWhereClause = "";
      } else {
        $blnInvalidSearch = true;
      }
    } else {
      $strWhereClause = " AND category_id = $categoryId ";
      $strAddArticle = "<p><a href=\"{FN_ADM_WRITE}?action=create&amp;area=$categoryId\">Add an article to this category</a></p>";
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
  
  $strTagOptions1 = <<<TagOptions1
<input id="tags1" name="tags" type="radio" value="1" $strTagSelect1 /> <label for="tags1">All content</label>

TagOptions1;

  $strTagOptions2 = <<<TagOptions2
<input id="tags2" name="tags" type="radio" value="2" $strTagSelect2 /> <label for="tags2">Content with tags</label>

TagOptions2;

  $strTagOptions3 = <<<TagOptions3
<input id="tags3" name="tags" type="radio" value="3" $strTagSelect3 /> <label for="tags3">Content without tags</label>

TagOptions3;

  $CMS->AT->arrAreaData = array();
  $CMS->DD->strEmptyItem = "All";
  $categoryDropDownHtml = $CMS->DD->CategoryList($categoryId, $blnAllowEmpty);

  $strStatusList   = $CMS->DD->ContentStatus($strStatus);
  $strSearchButton = $CMS->AC->SearchButton();

  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<form action="{FN_ADM_CONTENT_MANAGE}" method="get">
<div class="table-responsive">
<table class="table table-striped">
  <tr class="separator-row">
    <td colspan="6">Search for content</td>
  </tr>
  <tr>
    <td>
      <label><b>Area</b></label>
    </td>
    <td>
      <select id="area" name="area">
$categoryDropDownHtml
      </select>
    </td>
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
  <!--
  <tr>
    <td>
      <b>Tag Options</b>
    </td>
    <td>
$strTagOptions1
    </td>
    <td>
$strTagOptions2
    </td>
    <td>
$strTagOptions3
    </td>
  </tr>
  -->
  <tr>
    <td class="FootColour Centre" colspan="6">
      $strSearchButton
    </td>
  </tr>
</table>
</form>

END;

  // ** List all content in area ** //
  if (!$blnInvalidSearch) {
    if (isset($categoryId) && ($categoryId <> "")) {
      // Page numbers
      $intContentPerPage = $cpItemsPerPage;
      $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
      // Get content
      $strDateFormat = $CMS->SYS->GetDateFormat();
      $arrAreaContent = $CMS->ResultQuery("
        SELECT c.id, c.title, c.permalink, c.seo_title, c.content_status, c.hits, c.category_id,
        DATE_FORMAT(c.create_date, '$strDateFormat') AS create_date, c.create_date AS create_date_raw,
        cat.name AS category_name, cat.permalink AS category_permalink
        FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_CATEGORIES} cat)
        LEFT JOIN {IFW_TBL_USERS} u ON c.author_id = u.id
        WHERE c.category_id = cat.id
        $strWhereClause
        ORDER BY c.id ASC
        LIMIT $intStart, $intContentPerPage
      ", basename(__FILE__), __LINE__);
      $arrAreaCount = $CMS->ResultQuery("
        SELECT count(*) AS count FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_CATEGORIES} cat)
        WHERE c.category_id = cat.id
        $strWhereClause
        ORDER BY c.id ASC
      ", basename(__FILE__), __LINE__);
      $intAreaCount = $arrAreaCount[0]['count'];
      // Page number links
      $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intAreaCount);
      $strPageNumbers = $CMS->PNN->Make($intNumPages, $intPageNumber, basename(__FILE__)."$strGetURL");
      // Loopity loop
      for ($i=0; $i<count($arrAreaContent); $i++) {
        $intID           = $arrAreaContent[$i]['id'];
        $strCreateDate   = $arrAreaContent[$i]['create_date'];
        $strTitle        = $arrAreaContent[$i]['title'];
        $permalink       = $arrAreaContent[$i]['permalink'];
        $strStatus       = $arrAreaContent[$i]['content_status'];
        $strSEOTitle     = $arrAreaContent[$i]['seo_title'];
        $intHits         = $arrAreaContent[$i]['hits'];
        $categoryName    = $arrAreaContent[$i]['category_name'];
        $categoryLink    = $arrAreaContent[$i]['category_permalink'];
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
          <option value="editauthor">Edit Author</option>
          <option value="delete">Delete</option>
          <option value="restore">Restore</option>

AdminBulk;
            }
          } else {
            $strBulkOptions = "<option value=\"\">&nbsp;</option>";
          }
          // Build header
          $strHTML .= <<<TableHeader
$strAddArticle
$strPageNumbers
<form action="{FN_ADM_CONTENT_BULK}" method="post">
<div class="table-responsive">
<table class="table table-striped">
    <thead>
      <tr class="separator-row">
        <td>ID</td>
        <td>Title</td>
        <td>Area</td>
        <td>Created</td>
        <td>Status</td>
        <td>Hits</td>
        <td>Options</td>
        <td>
          <a href="#FooterRow1" id="js-manage-content-check-all" style="color: #fff; text-decoration: none;" title="Toggle All">+</a>
        </td>
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
        $CMS->RES->EditArticle($categoryId, $intID);
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
        <td class="Left Title"><a href="$permalink" title="View this article">$strTitle</a></td>
        <td class="Centre Area"><a href="$categoryLink">$categoryName</a></td>
        <td class="Centre Created">$strCreateDate</td>
        <td class="Centre Status">$strStatus</td>
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
</div>
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

    var checkAll = true;
    $('#js-manage-content-check-all').on('click', function() {
        ToggleCheckboxes(checkAll);
        checkAll = !checkAll;
    });
</script>

FooterScript;
  
  $CMS->AP->Display($strHTML);
