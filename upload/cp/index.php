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
  $CMS->RES->ValidateLoggedIn();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_NOT_LOGGED_IN, "");
  }
  $strSiteTitle = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
  $strPageTitle = "Dashboard";

// Twig templating for CPanel
$cpBindings = array(); //array_merge($globalBindings, $userBindings);
$themeFile = 'index.twig';

$cpBindings['CP']['Title'] = $strPageTitle;


$CMS->RES->Admin();
if ($CMS->RES->IsError()) {
    $isAdmin = false;
} else {
    $isAdmin = true;
}
$cpBindings['Auth']['IsAdmin'] = $isAdmin;

  // ** Quick Stats ** //
  if (!$isAdmin) {
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
    $arrArticleCount = $CMS->ResultQuery("
    SELECT count(*) AS count FROM {IFW_TBL_CONTENT}
    ", basename(__FILE__), __LINE__);
      $cpBindings['Page']['ArticleCount'] = $arrArticleCount[0]['count'];
    
    // Comment count
    $arrCommentCount = $CMS->ResultQuery("
    SELECT count(*) AS count, comment_status FROM {IFW_TBL_COMMENTS}
    GROUP BY comment_status
    ", basename(__FILE__), __LINE__);
    $pendingCommentCount = 0;
    $approvedCommentCount = 0;
    $spamCommentCount = 0;
    for ($i=0; $i<count($arrCommentCount); $i++) {
      switch ($arrCommentCount[$i]['comment_status']) {
        case "Pending":
            $pendingCommentCount = $arrCommentCount[$i]['count'];
          break;
        case "Approved":
            $approvedCommentCount = $arrCommentCount[$i]['count'];
          break;
        case "Spam":
            $spamCommentCount = $arrCommentCount[$i]['count'];
          break;
      }
    }
      $cpBindings['Page']['PendingCommentCount'] = $pendingCommentCount;
      $cpBindings['Page']['ApprovedCommentCount'] = $approvedCommentCount;
      $cpBindings['Page']['SpamCommentCount'] = $spamCommentCount;

          // Site file count
    $arrSiteFileCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_UPLOADS} WHERE is_siteimage = 'Y' AND is_avatar = 'N'", basename(__FILE__), __LINE__);
    $cpBindings['Page']['SiteFileCount'] = $arrSiteFileCount[0]['count'];
    
    // Member count
    $arrUserCount = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USERS}", basename(__FILE__), __LINE__);
    $cpBindings['Page']['UserCount'] = $arrUserCount[0]['count'];
    
  }
  
  // Content
$canWriteContent = false;
  $CMS->RES->ViewManageContent();
  if (!$CMS->RES->IsError()) {
    if ($CMS->RES->CountTotalWriteAccess() > 0) {
        $canWriteContent = true;
    }
  }
$cpBindings['Auth']['CanWriteContent'] = $canWriteContent;
  
  // Recent Comments
  $recentComments = $CMS->ResultQuery("
    SELECT com.id, com.content, com.create_date, con.title
    FROM maj_comments com
    JOIN maj_content con ON com.story_id = con.id
    WHERE com.comment_status = 'Approved'
    ORDER BY com.id DESC LIMIT 5
  ", basename(__FILE__), __LINE__);
  $cpBindings['Page']['RecentComments'] = $recentComments;

  // Recent Drafts
  $recentDrafts = $CMS->ResultQuery("
    SELECT id, title, create_date FROM maj_content WHERE content_status = 'Draft'
    ORDER BY id DESC LIMIT 5
  ", basename(__FILE__), __LINE__);
  $cpBindings['Page']['RecentDrafts'] = $recentDrafts;

    // Build the page
    $cpBindings['Page']['SitemapUrl'] = "http://".SVR_HOST.FN_SITEMAPINDEX;
    $cpBindings['Page']['CmsVersion'] = $CMS->SYS->GetSysPref(C_PREF_CMS_VERSION);
    $cpBindings['Page']['ThisYear'] = date('Y'); // Current year
    $cpBindings['Page']['SiteTitle'] = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);

  //$CMS->AP->Display($strHTML);

// Twig templating for CPanel
$engine = $cmsContainer->getService('Theme.EngineCPanel');
$outputHtml = $engine->render($themeFile, $cpBindings);
print($outputHtml);
exit;
