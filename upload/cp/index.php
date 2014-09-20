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

  // New Releases
  $strNewReleases = "";
  @ $rss = fetch_rss("http://feeds.feedburner.com/InjaderNewReleases");
  if ($rss) {
    $i = 0;
    foreach ($rss->items as $item) {
      if ($i >= 5) {
        break;
      }
      if ($i == 0) {
        $strRowClass = "first";
      } else {
        $strRowClass = "";
      }
      $strLink  = $item['link'];
      $strTitle = $item['title'];
      $dteDate  = date('M d', strtotime($item['pubdate']));
      $strNewReleases .= <<<ReleaseData
          <tr class="$strRowClass">
            <td class="column1 list"><a href="$strLink">$strTitle</a></td>
            <td class="column2">$dteDate</td>
          </tr>

ReleaseData;
      $i++;
    }
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
    $strApprovedComments = "Approved";
    $strPendingComments  = "Pending";
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
  $arrRecentComments = $CMS->ResultQuery("SELECT id, content, create_date FROM maj_comments WHERE comment_status = 'Approved' ORDER BY id DESC LIMIT 5", basename(__FILE__), __LINE__);
  if ((count($arrRecentComments) > 0) && (is_array($arrRecentComments))) {
    for ($i=0; $i<count($arrRecentComments); $i++) {
      $intRecentCommentID   = $arrRecentComments[$i]['id'];
      $strRecentCommentBody = $arrRecentComments[$i]['content'];
      $strRecentCommentItem = "<a href=\"".$CMS->PL->ViewComment($intRecentCommentID)."\">".$strRecentCommentBody."</a>";
      $dteRecentCommentDate = date('d M Y H:i:s', strtotime($arrRecentComments[$i]['create_date']));
      $intRecentCommentNum  = $i + 1;
      $strRecentCommentsHTML .= <<<RecentDrafts
          <tr>
            <td class="column1 comments">
              $strRecentCommentItem
              <br /><i>$dteRecentCommentDate</i>
            </td>
            <td class="column2">$intRecentCommentNum</td>
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
      $dteRecentDraftDate = date('d M Y H:i:s', strtotime($arrRecentDrafts[$i]['create_date']));
      $intRecentCommentNum = $i + 1;
      $strRecentDraftsHTML .= <<<RecentDrafts
          <tr>
            <td class="column1 page">
              $strRecentDraftItem
              <br /><i>$dteRecentDraftDate</i>
            </td>
            <td class="column2">$intRecentCommentNum</td>
          </tr>
    
RecentDrafts;
    }
  } else {
    $strRecentDraftsHTML = <<<RecentDrafts
          <tr>
            <td class="column1 page"><i>No drafts</i></td>
            <td class="column2">-</td>
          </tr>
    
RecentDrafts;
  }
  
  // Build the page
  $strHTML = <<<END
<h1>Injader Control Panel</h1>
<div id="content">
  <div class="panel">
    <div class="column-x2">
      <ul class="tabs top">
        <li id="LeftTab1" class="overview first on"><a id="LeftTab1A" href="#" onclick="SwitchLeftTab('1');" class="nostyle">Overview</a></li>
        <li id="LeftTab2" class="comments"><a id="LeftTab2A" href="#" onclick="SwitchLeftTab('2');">Recent Comments</a></li>
        <li id="LeftTab3" class="page-edit"><a id="LeftTab3A" href="#" onclick="SwitchLeftTab('3');">Recent Drafts</a></li>
      </ul>
      <!-- DashBodyA1 -->
      <div id="DashBodyA1" class="body" style="display: block;">
        <table class="dashboard-overview">
          <tr class="first">
            <td class="column1 title"><h3>Content</h3></td>
            <td class="column2">&nbsp;</td>
          </tr>
          <tr>
            <td class="column1 page">$strArticleLink</td>
            <td class="column2">$intArticleCount</td>
          </tr>
          <tr>
            <td class="column1 page">Site Files</td>
            <td class="column2">$intSiteFileCount</td>
          </tr>
        </table>
        <table class="dashboard-overview-b">
          <tr>
            <td class="column1 title"><h3>Comments</h3></td>
            <td class="column2">&nbsp;</td>
          </tr>
          <tr>
            <td class="column1 comments">$strApprovedComments</td>
            <td class="column2">$intApprovedComments</td>
          </tr>
          <tr>
            <td class="column1 comments">$strPendingComments</td>
            <td class="column2">$intPendingComments</td>
          </tr>
          <tr>
            <td class="column1 comments">$strSpamComments</td>
            <td class="column2">$intSpamComments</td>
          </tr>
        </table>
        <table class="dashboard-overview">
          <tr>
            <td class="column1 title"><h3>Users</h3></td>
            <td class="column2">&nbsp;</td>
          </tr>
          <tr>
            <td class="column1">$strActiveUsers</td>
            <td class="column2">$intUserCount</td>
          </tr>
        </table>
        <div class="btn-primary"><a href="{FN_ADM_EDIT_PROFILE}"><span><span><span>Edit Profile</span></span></span></a></div>
        <div class="btn-primary"><a href="$strViewMyProfile"><span><span><span>View Profile</span></span></span></a></div>
        <br />
        $strChangePass
        $strManageAvatars
      </div>
      <!-- /DashBodyA1 -->
      <!-- DashBodyA2 -->
      <div id="DashBodyA2" class="body" style="display: none;">
        <table class="dashboard-overview">
          <tr class="first">
            <td class="column1 title" colspan="2"><h3>Recent Comments - click to view</h3></td>
          </tr>
$strRecentCommentsHTML
        </table>
      </div>
      <!-- /DashBodyA2 -->
      <!-- DashBodyA3 -->
      <div id="DashBodyA3" class="body" style="display: none;">
        <table class="dashboard-overview">
          <tr class="first">
            <td class="column1 title" colspan="2"><h3>Recent Drafts - click to edit</h3></td>
          </tr>
$strRecentDraftsHTML
        </table>
      </div>
      <!-- /DashBodyA3 -->
    </div>
    <div class="column-x3">
      <ul class="tabs top">
        <li class="news first on">Injader News</li>
        <!--
        <li class="widget"><a href="#">New Widgets</a></li>
        <li class="tools"><a href="#">New Tools</a></li>
        <li class="layout"><a href="#">New Themes</a></li>
        -->
      </ul>
      <div class="body">
        <table class="dashboard-news">
$strNewReleases
        </table>
        <div class="btn-primary"><a href="http://www.injader.com"><span><span><span>More News</span></span></span></a></div>
        <div class="btn-primary"><a href="http://help.injader.com"><span><span><span>Help Docs</span></span></span></a></div>
        <div class="btn-primary"><a href="http://forums.injader.com"><span><span><span>Support Forum</span></span></span></a></div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
/* <![CDATA[ */
  function SwitchLeftTab(intWhich) {
    document.getElementById('DashBodyA1').style.display = 'none';
    document.getElementById('DashBodyA2').style.display = 'none';
    document.getElementById('DashBodyA3').style.display = 'none';
    document.getElementById('LeftTab1').className = 'overview first';
    document.getElementById('LeftTab2').className = 'comments';
    document.getElementById('LeftTab3').className = 'page-edit';
    document.getElementById('LeftTab1A').className = '';
    document.getElementById('LeftTab2A').className = '';
    document.getElementById('LeftTab3A').className = '';
    if (intWhich == "1") {
      document.getElementById('DashBodyA1').style.display = 'block';
      document.getElementById('LeftTab1').className = 'overview first on';
      document.getElementById('LeftTab1A').className = 'nostyle';
    } else if (intWhich == "2") {
      document.getElementById('DashBodyA2').style.display = 'block';
      document.getElementById('LeftTab2').className = 'comments on';
      document.getElementById('LeftTab2A').className = 'nostyle';
    } else if (intWhich == "3") {
      document.getElementById('DashBodyA3').style.display = 'block';
      document.getElementById('LeftTab3').className = 'page-edit on';
      document.getElementById('LeftTab3A').className = 'nostyle';
    }
  }
/* ]]> */
</script>

END;
  $CMS->AP->Display($strHTML);
?>