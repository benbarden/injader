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
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
  }
  $strPageTitle = "Manage Comments";

  // Comment type
  $strSearchType = empty($_GET['type']) ? "" : $_GET['type'];
  $blnApproved = false; $blnPending = false; $blnSpam = false;
  switch ($strSearchType) {
    case "approved": $blnApproved = true; $strPageTitle .= " - Approved"; break;
    case "pending":  $blnPending  = true; $strPageTitle .= " - Pending"; break;
    case "spam":     $blnSpam     = true; $strPageTitle .= " - Spam"; break;
    default:         $blnApproved = true; $strPageTitle .= " - Approved";
                     $strSearchType = "approved"; break;
  }
  $CMS->AP->SetTitle($strPageTitle);
  
  // Multi-paging
  if (!empty($_GET['page'])) {
    $intPageNumber = $_GET['page'];
    if ($intPageNumber < 1) {
      $intPageNumber = 1;
    }
  } else {
    $intPageNumber = 1;
  }
  
  /* to be reinstated later
<ul>
<li>Approve and trust: Approve the selected comments. Future comments from all of the selected users will be automatically approved</li>
<li>Deny and suspend: Deny the selected comments. The selected users will have their accounts suspended. This prevents future comments and also stops the user from being able to log in.</li>
</ul>
  */
  
  // Build page
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>

END;

// Switch links
if ($blnApproved) {
    $strWhereClause = "AND comment_status = 'Approved'";
} elseif ($blnPending) {
    $strWhereClause = "AND comment_status = 'Pending'";
} elseif ($blnSpam) {
    $strWhereClause = "AND comment_status = 'Spam'";
}

// Page numbers
  $intContentPerPage = $cpItemsPerPage;
  $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
  // Get comments
  $arrComments = $CMS->ResultQuery("SELECT c.*, con.title, con.seo_title, u.username, u.seo_username, u.email, u.id AS user_id FROM ({IFW_TBL_COMMENTS} c, {IFW_TBL_CONTENT} con) LEFT JOIN {IFW_TBL_USERS} u ON c.author_id = u.id WHERE con.id = c.story_id $strWhereClause ORDER BY c.id DESC LIMIT $intStart, $intContentPerPage", basename(__FILE__), __LINE__);
  $arrCount = $CMS->ResultQuery("SELECT count(*) AS count FROM ({IFW_TBL_COMMENTS} c, {IFW_TBL_CONTENT} con) LEFT JOIN {IFW_TBL_USERS} u ON c.author_id = u.id WHERE con.id = c.story_id $strWhereClause", basename(__FILE__), __LINE__);
  $intCount = $arrCount[0]['count'];
  // Page number links
  $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intCount);
  $strPageNumbers = $CMS->PNN->Make($intNumPages, $intPageNumber, basename(__FILE__)."?type=$strSearchType");
  // Loopity loop
  for ($i=0; $i<count($arrComments); $i++) {
    $intID         = $arrComments[$i]['id'];
    $strContent    = $arrComments[$i]['content'];
    $intUserID     = $arrComments[$i]['user_id'];
    $strUserIP     = $arrComments[$i]['ip_address'];
    $dteCreateDate = $arrComments[$i]['create_date'];
    $intContentID  = $arrComments[$i]['story_id'];
    $strTitle      = $arrComments[$i]['title'];
    $permalink     = $arrComments[$i]['permalink'];
    $strSEOTitle   = $arrComments[$i]['seo_title'];
    $strGuestURL = "";
    // Make user link
    if ($intUserID) {
      $strUser     = $arrComments[$i]['username'];
      $strSEOUser  = $arrComments[$i]['seo_username'];
      $strEmail    = $arrComments[$i]['email'];
      $CMS->PL->SetTitle($strSEOUser);
      $strViewLink = $CMS->PL->ViewUser($intUserID);
      $strUserHTML = "<a href=\"$strViewLink\" title=\"View profile\">$strUser</a>";
    } else {
      $strUser     = $arrComments[$i]['guest_name'];
      $strUserHTML = "$strUser";
      $strEmail    = $arrComments[$i]['guest_email'];
      $strURL      = trim($arrComments[$i]['guest_url']);
      if ($strURL) {
        $strGuestURL = "<b>URL:</b> $strURL ";
      }
    }
    // Table header
    if ($i == 0) {
      // Bulk options
      $strBulkOptions = "";
      if (!$blnApproved) {
      /*
        <option value="approvetrust">Approve and trust</option>
      */
        $strBulkOptions .= <<<BulkApprove
        <option value="approve">Approve</option>

BulkApprove;
      }
      /*
        <option value="denysuspend">Deny and suspend</option>
      */
      $strBulkOptions .= <<<BulkOptions
        <option value="deny">Delete</option>

BulkOptions;
      // Table header
      $strHTML .= <<<TableHeader
$strPageNumbers
<form action="{FN_ADM_COMMENTS_BULK}" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <thead>
    <tr class="separator-row">
      <td>&nbsp;</td>
      <td>Content</td>
      <td>Article</td>
    </tr>
  </thead>
  <tfoot>
    <tr id="FooterRow1">
      <td class="FootColour" style="text-align: left;" colspan="3">
        <input type="hidden" id="txtCommentType" name="txtCommentType" value="$strSearchType" />
        <a href="#FooterRow1" onclick="ToggleCheckboxes(true);">Check All</a> / 
        <a href="#FooterRow1" onclick="ToggleCheckboxes(false);">Uncheck All</a>
        <br />
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
    
    if ($blnSpam) {
      
    	if (strlen($strContent) > 500) {
    		
    	  $strItemContent = <<<ItemContent
<p id="clickhere$intID"><em>This comment has been hidden. 
<a href="javascript:void(0);" onclick="
 document.getElementById('content$intID').style.display = 'block';
 document.getElementById('clickhere$intID').style.display = 'none';
 ">Click here to show it</a>.</em></p>
<div id="content$intID" style="display: none;">
$strContent
</div>

ItemContent;
    	  
    	} else {
    		
    	  $strItemContent = $strContent;
    	  
    	}
    	
    } else {
    	
      $strItemContent = $strContent;
      
    }
    
    if (($i % 2) == 0) {
      $strRowClass = "even";
    } else {
      $strRowClass = "odd";
    }
    
    $strRowID = "mRow".$i;
    
    $strHTML .= <<<AreaContent
    <tr id="$strRowID" class="$strRowClass">
      <td class="Centre Checkbox" style="vertical-align: top;">
        <input type="checkbox" name="chkBulkOptions[]" id="chkBulkOptions$intID" value="$intID" />
      </td>
      <td class="Left Content" style="vertical-align: top;">
        $strItemContent
        <p style="font-size: 95%; font-style: italic; margin-bottom: 0;">
        <b>Posted on</b>: $dteCreateDate - <b>ID</b>: $intID
        <br /><b>By</b>: $strUserHTML - $strGuestURL
        <b>Email</b>: $strEmail <b>IP</b>: $strUserIP</p>
      </td>
      <td class="Centre Title" style="vertical-align: top;">
        <a href="$permalink" title="View article">$strTitle</a>
      </td>
    </tr>

AreaContent;
  }
  if (count($arrComments) > 0) {
    $strHTML .= <<<TableFooter
  </tbody>
</table>
</div>
</form>

TableFooter;
  } else {
    $strHTML .= "<p>No comments found.</p>";
  }

  $CMS->AP->Display($strHTML);
