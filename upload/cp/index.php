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
  $CMS->RES->ValidateLoggedIn();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_NOT_LOGGED_IN, "");
  }
  $strSiteTitle = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
  $strPageTitle = $strSiteTitle." - Control Panel";

  $CMS->AP->SetTitle($strPageTitle);
  
  // ** Settings ** //
  // Profile links
  $intUserID = $CMS->RES->GetCurrentUserID();
  $strViewMyProfile = $CMS->PL->ViewUser($intUserID);
  
  // Are avatars allowed?
  if ($CMS->SYS->GetSysPref(C_PREF_AVATARS_PER_USER) > 0) {
    $strManageAvatars = "<div class=\"btn-primary\"><a href=\"{FN_ADM_MANAGE_AVATARS}\"><span><span><span>Manage Avatars</span></span></span></a></div>";
  } else {
    $strManageAvatars = "";
  }
  // Can users change their password?
  if ($CMS->SYS->GetSysPref(C_PREF_USER_CHANGE_PASS) == "Y") {
    $strChangePass = "<div class=\"btn-primary\"><a href=\"{FN_ADM_CHANGE_PASSWORD}\"><span><span><span>Change Password</span></span></span></a></div>";
  } else {
    $strChangePass = "";
  }
  
  // ** Quick Stats ** //
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $intArticleCount     = "-";
    $intCommentCount     = "-";
    $intSiteFileCount    = "-";
    $intUserCount        = "-";
    $strNewestUser       = "-";
    $intApprovedComments = "-";
    $intPendingComments  = "-";
    $intSpamComments     = "-";
  } else {
    // Article count
    $arrArticleCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_CONTENT}", basename(__FILE__), __LINE__);
    $intArticleCount = $arrArticleCount[0]['count'];
    
    // Comment count
    $arrCommentCount = $CMS->ResultQuery("SELECT count(*) AS count, comment_status FROM {IFW_TBL_COMMENTS} GROUP BY comment_status", basename(__FILE__), __LINE__);
    $intPendingComments = 0; $intApprovedComments = 0; $intSpamComments = 0;
    for ($i=0; $i<count($arrCommentCount); $i++) {
      switch ($arrCommentCount[$i]['comment_status']) {
        case "Pending":
          $intPendingComments = $arrCommentCount[$i]['count'];
          break;
        case "Approved":
          $intApprovedComments = $arrCommentCount[$i]['count'];
          break;
        case "Spam":
          $intSpamComments = $arrCommentCount[$i]['count'];
          break;
      }
    }
    
    // Site file count
    $arrSiteFileCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_UPLOADS} WHERE is_siteimage = 'Y' AND is_avatar = 'N'", basename(__FILE__), __LINE__);
    $intSiteFileCount = $arrSiteFileCount[0]['count'];
    
    // Member count
    $arrUserCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USERS}", basename(__FILE__), __LINE__);
    $intUserCount = $arrUserCount[0]['count'];
    
  }
  
  // Content
  $CMS->RES->ViewManageContent();
  if ($CMS->RES->IsError()) {
    $strArticleLink = "Articles";
  } else {
    // Create article?
    if ($CMS->RES->CountTotalWriteAccess() > 0) {
      $strArticleLink = "<a href=\"{FN_ADM_CONTENT_MANAGE}?navtype=1&amp;area1=0&amp;area2=0&amp;area3=0\">Articles</a>";
    } else {
      $strArticleLink = "Articles";
    }
  }
  
  // Comments / Users
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $strApprovedComments = "Approved Comments";
    $strPendingComments  = "Pending Comments";
    $strSpamComments     = "Spam";
    $strActiveUsers      = "Active Users";
  } else {
    $strApprovedComments = "<a href=\"{FN_ADM_COMMENTS}\">Approved Comments</a>";
    $strPendingComments  = "<a href=\"{FN_ADM_COMMENTS}?type=pending\">Pending Comments</a>";
    $strSpamComments     = "<a href=\"{FN_ADM_COMMENTS}?type=spam\">Spam</a>";
    $strActiveUsers      = "<a href=\"{FN_ADM_USERS}\">Active Users</a>";
  }
  
  // Recent Comments
  $strRecentCommentsHTML = "";
  $arrRecentComments = $CMS->ResultQuery("
    SELECT com.id, com.content, com.create_date, con.title
    FROM maj_comments com
    JOIN maj_content con ON com.story_id = con.id
    WHERE com.comment_status = 'Approved'
    ORDER BY com.id DESC LIMIT 5
  ", basename(__FILE__), __LINE__);
  if ((count($arrRecentComments) > 0) && (is_array($arrRecentComments))) {
    for ($i=0; $i<count($arrRecentComments); $i++) {
      $intRecentCommentID   = $arrRecentComments[$i]['id'];
      $strRecentCommentBody = strip_tags($arrRecentComments[$i]['content']);
      $commentLink = $CMS->PL->ViewComment($intRecentCommentID);
      //$strRecentCommentItem = "<a href=\"".$CMS->PL->ViewComment($intRecentCommentID)."\">".$strRecentCommentBody."</a>";
      $dteRecentCommentDate = date('d M Y H:i', strtotime($arrRecentComments[$i]['create_date']));
      $intRecentCommentNum  = $i + 1;
      $articleTitle = $arrRecentComments[$i]['title'];
      $strRecentCommentsHTML .= <<<RecentDrafts
          <tr>
            <td><a href="$commentLink">$articleTitle</a></td>
            <td>$dteRecentCommentDate</td>
          </tr>
          <tr>
            <td colspan="2">
              $strRecentCommentBody
            </td>
          </tr>
    
RecentDrafts;
    }
  } else {
    $strRecentCommentsHTML = <<<RecentDrafts
          <tr>
            <td class="column1 comments"><i>No recent comments</i></td>
            <td class="column2">-</td>
          </tr>
    
RecentDrafts;
  }
  
  // Recent Drafts
  $strRecentDraftsHTML = "";
  $arrRecentDrafts = $CMS->ResultQuery("SELECT id, title, create_date FROM maj_content WHERE content_status = 'Draft' ORDER BY id DESC LIMIT 5", basename(__FILE__), __LINE__);
  if ((count($arrRecentDrafts) > 0) && (is_array($arrRecentDrafts))) {
    for ($i=0; $i<count($arrRecentDrafts); $i++) {
      $strRecentDraftItem = "<a href=\"".FN_ADM_WRITE."?action=edit&amp;id=".$arrRecentDrafts[$i]['id']."\">".$arrRecentDrafts[$i]['title']."</a>";
      $dteRecentDraftDate = date('d M Y H:i', strtotime($arrRecentDrafts[$i]['create_date']));
      $intRecentCommentNum = $i + 1;
      $strRecentDraftsHTML .= <<<RecentDrafts
          <tr>
            <td>$strRecentDraftItem</td>
            <td>$dteRecentDraftDate</td>
          </tr>
    
RecentDrafts;
    }
  } else {
    $strRecentDraftsHTML = <<<RecentDrafts
          <tr>
            <td><i>No drafts</i></td>
            <td>-</td>
          </tr>
    
RecentDrafts;
  }

  // Columns
  $htmlColumn1 = <<<column1
<h2>Overview</h2>
<table class="DefaultTable">
    <colgroup>
        <col class="InfoColour MediumCell">
        <col class="BaseColour MediumCell">
    </colgroup>
    <tr>
        <td>$strArticleLink</td>
        <td>$intArticleCount</td>
    </tr>
    <tr>
        <td>Site Files</td>
        <td>$intSiteFileCount</td>
    </tr>
    <tr>
        <td>$strActiveUsers</td>
        <td>$intUserCount</td>
    </tr>
    <tr>
        <td>$strApprovedComments</td>
        <td>$intApprovedComments</td>
    </tr>
    <tr>
        <td>$strPendingComments</td>
        <td>$intPendingComments</td>
    </tr>
    <tr>
        <td>$strSpamComments</td>
        <td>$intSpamComments</td>
    </tr>
</table>

<h2>Options</h2>

<table class="DefaultTable">
    <colgroup>
        <col class="BaseColour MediumCell">
        <col class="BaseColour MediumCell">
    </colgroup>
    <tr>
        <td>
            <a href="{FN_ADM_EDIT_PROFILE}">Edit Profile</a>
        </td>
        <td>
            <a href="$strViewMyProfile">View Profile</a>
        </td>
    </tr>
    <tr>
        <td>
            $strChangePass
        </td>
        <td>
            $strManageAvatars
        </td>
    </tr>
</table>

column1;


  $htmlColumn2 = <<<column2
<h2>Recent Comments</h2>
<table class="DefaultTable">
    <colgroup>
        <col class="InfoColour MediumCell">
        <col class="BaseColour MediumCell">
    </colgroup>
$strRecentCommentsHTML
</table>

<h2>Recent Drafts</h2>
<table class="DefaultTable">
    <colgroup>
        <col class="InfoColour MediumCell">
        <col class="BaseColour MediumCell">
    </colgroup>
$strRecentDraftsHTML
</table>

column2;

  // Build the page
  $strHTML = <<<END
<h1>Injader Control Panel</h1>

<div id="cp">

<table width="100%;">
    <tr>
        <td style="vertical-align: top; width: 50%;">
        $htmlColumn1
        </td>
        <td style="vertical-align: top; width: 50%;">
        $htmlColumn2
        </td>
    </tr>
</table>

</div>

END;
  $CMS->AP->Display($strHTML);
?>