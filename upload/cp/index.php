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
  $strPageTitle = "Dashboard";

  $CMS->AP->SetTitle($strPageTitle);
  
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
    $strApprovedComments = "<a href=\"{FN_ADM_COMMENTS}\">Approved</a>";
    $strPendingComments  = "<a href=\"{FN_ADM_COMMENTS}?type=pending\">Pending</a>";
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
<h2 class="sub-header">Recent Comments</h2>
<div class="table-responsive">
<table class="table table-striped">
$strRecentCommentsHTML
</table>
</div>

column1;


  $htmlColumn2 = <<<column2
<h2 class="sub-header">Recent Drafts</h2>
<div class="table-responsive">
<table class="table table-striped">
$strRecentDraftsHTML
</table>
</div>

column2;

    // Build the page
    $sitemapUrl = "http://".SVR_HOST.FN_SITEMAPINDEX;
    $strVersion   = $CMS->SYS->GetSysPref(C_PREF_CMS_VERSION);
    $intYear = date('Y'); // Current year

    $strHTML = <<<END
<h1 class="page-header">Dashboard</h1>

<table width="100%;">
    <tr>
        <td style="vertical-align: top; width: 40%;">
            <h2 class="sub-header">Overview</h2>
            <div class="table-responsive">
            <table class="table table-striped">
                <tr class="separator-row">
                    <td colspan="2">Content</td>
                    <td colspan="2">Comments</td>
                </tr>
                <tr>
                    <td>$strArticleLink</td>
                    <td>$intArticleCount</td>
                    <td>$strPendingComments</td>
                    <td>$intPendingComments</td>
                </tr>
                <tr>
                    <td>$strActiveUsers</td>
                    <td>$intUserCount</td>
                    <td>$strApprovedComments</td>
                    <td>$intApprovedComments</td>
                </tr>
                <tr>
                    <td>Site Files</td>
                    <td>$intSiteFileCount</td>
                    <td>$strSpamComments</td>
                    <td>$intSpamComments</td>
                </tr>
            </table>
            </div>
        </td>
        <td style="vertical-align: top; width: 55%;">
            <h2 class="sub-header">Useful info</h2>
            <div class="table-responsive">
            <table class="table table-striped">
                <tr class="separator-row">
                    <td>Field</td>
                    <td>Details</td>
                </tr>
                <tr>
                    <td>Sitemap URL</td>
                    <td>
                        <a href="$sitemapUrl">$sitemapUrl</a>
                    </td>
                </tr>
            </table>
            </div>
        </td>
    </tr>
</table>

<table width="100%;">
    <tr>
        <td style="vertical-align: top; width: 48%;">
        $htmlColumn1
        </td>
        <td style="vertical-align: top; width: 48%;">
        $htmlColumn2
        </td>
    </tr>
</table>

<p>
Powered by <a href="http://www.injader.com/">Injader</a> $strVersion |
<a href="https://github.com/benbarden/injader" title="Github">Github</a>
<br>
Injader is free software released under the
<a href="http://www.gnu.org/licenses/gpl.html">GNU General Public Licence</a> (v3).
Copyright &copy; 2005-$intYear <a href="http://www.benbarden.com/">Ben Barden</a>.
</p>

END;
  $CMS->AP->Display($strHTML);
