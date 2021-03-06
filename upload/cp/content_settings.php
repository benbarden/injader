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
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
  }
  $strPageTitle = "Content Settings";

  $CMS->AP->SetTitle($strPageTitle);

  $blnSubmitForm = false;
  $strBreachedRSSCountText = "";
  
  if ($_POST) {
    $intRSSCount = empty($_POST['txtRSSCount']) ? "0" : $CMS->FilterNumeric($_POST['txtRSSCount']);
    $intTagThreshold = empty($_POST['txtTagThreshold']) ? "0" : $CMS->FilterNumeric($_POST['txtTagThreshold']);
    if (!$intTagThreshold) {
      $intTagThreshold = 0;
    }
    $strUseCAPTCHA          = !empty($_POST['chkUseCAPTCHA']) ? 1 : 0;
    $strArticleNotifyAdmin  = !empty($_POST['chkArticleNotifyAdmin']) ? 1 : 0;
    $strArticleReviewEmail  = !empty($_POST['chkArticleReviewEmail']) ? 1 : 0;
    $strReviewEmail         = !empty($_POST['chkReviewEmail']) ? 1 : 0;
    $strCommentNotify       = !empty($_POST['chkCommentNotification']) ? 1 : 0;
    $strCommentNotifyAuthor = !empty($_POST['chkCommentNotifyAuthor']) ? 1 : 0;
    $strUseNoFollow         = !empty($_POST['chkUseNoFollow']) ? 1 : 0;
    $intNoFollowLimit       = !empty($_POST['txtNoFollowLimit']) ? $CMS->FilterNumeric($_POST['txtNoFollowLimit']) : 0;
    if (!$intNoFollowLimit) {
      $intNoFollowLimit = '0';
    }
    // Validation
    $blnSubmitForm = true;
    if (($intRSSCount < 5) || ($intRSSCount > 30)) {
      $blnSubmitForm = false;
      $strBreachedRSSCountText = $CMS->AC->InvalidFormData(M_ERR_BREACHED_RSS_COUNT);
    }
    // Update database
    if ($blnSubmitForm) {
      if ($CMS->SYS->GetSysPref(C_PREF_RSS_COUNT) != $intRSSCount) {
        $CMS->SYS->WriteSysPref(C_PREF_RSS_COUNT, $intRSSCount);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_TAG_THRESHOLD) != $intTagThreshold) {
        $CMS->SYS->WriteSysPref(C_PREF_TAG_THRESHOLD, $intTagThreshold);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COMMENT_CAPTCHA) != $strUseCAPTCHA) {
        $CMS->SYS->WriteSysPref(C_PREF_COMMENT_CAPTCHA, $strUseCAPTCHA);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_ARTICLE_NOTIFY_ADMIN) != $strArticleNotifyAdmin) {
        $CMS->SYS->WriteSysPref(C_PREF_ARTICLE_NOTIFY_ADMIN, $strArticleNotifyAdmin);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_ARTICLE_REVIEW_EMAIL) != $strArticleReviewEmail) {
        $CMS->SYS->WriteSysPref(C_PREF_ARTICLE_REVIEW_EMAIL, $strArticleReviewEmail);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COMMENT_REVIEW_EMAIL) != $strReviewEmail) {
        $CMS->SYS->WriteSysPref(C_PREF_COMMENT_REVIEW_EMAIL, $strReviewEmail);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COMMENT_NOTIFICATION) != $strCommentNotify) {
        $CMS->SYS->WriteSysPref(C_PREF_COMMENT_NOTIFICATION, $strCommentNotify);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COMMENT_NOTIFY_AUTHOR) != $strCommentNotifyAuthor) {
        $CMS->SYS->WriteSysPref(C_PREF_COMMENT_NOTIFY_AUTHOR, $strCommentNotifyAuthor);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COMMENT_USE_NOFOLLOW) != $strUseNoFollow) {
        $CMS->SYS->WriteSysPref(C_PREF_COMMENT_USE_NOFOLLOW, $strUseNoFollow);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_COMMENT_NOFOLLOW_LIMIT) != $intNoFollowLimit) {
        $CMS->SYS->WriteSysPref(C_PREF_COMMENT_NOFOLLOW_LIMIT, $intNoFollowLimit);
      }
      // Rebuild the cache
      $CMS->SYS->RebuildCache();
    }
  } else {
    if (!isset($CMS->SYS->arrSysPrefs[C_PREF_SITE_TITLE])) {
      $CMS->SYS->GetAllSysPrefs();
    }
    $arrSysPrefs = $CMS->SYS->arrSysPrefs;
    foreach ($arrSysPrefs as $strKey => $strValue) {
      switch ($strKey) {
        case C_PREF_TAG_THRESHOLD:          $intTagThreshold        = $strValue; break;
        case C_PREF_RSS_COUNT:              $intRSSCount            = $strValue; break;
        case C_PREF_COMMENT_CAPTCHA:        $strUseCAPTCHA          = $strValue; break;
        case C_PREF_ARTICLE_NOTIFY_ADMIN:   $strArticleNotifyAdmin  = $strValue; break;
        case C_PREF_ARTICLE_REVIEW_EMAIL:   $strArticleReviewEmail  = $strValue; break;
        case C_PREF_COMMENT_REVIEW_EMAIL:   $strReviewEmail         = $strValue; break;
        case C_PREF_COMMENT_NOTIFICATION:   $strCommentNotify       = $strValue; break;
        case C_PREF_COMMENT_NOTIFY_AUTHOR:  $strCommentNotifyAuthor = $strValue; break;
        case C_PREF_COMMENT_USE_NOFOLLOW:   $strUseNoFollow         = $strValue; break;
        case C_PREF_COMMENT_NOFOLLOW_LIMIT: $intNoFollowLimit       = $strValue; break;
      }
    }
  }
  
  if ($blnSubmitForm) {
    $strConfirmMsg = "<p><b>Settings updated successfully.</b></p>\n\n";
  } else {
    $strConfirmMsg = "";
  }
  
  $strUseCAPTCHAChecked          = $strUseCAPTCHA          == 1 ? " checked=\"checked\"" : "";
  $strArticleNotifyAdminChecked  = $strArticleNotifyAdmin  == 1 ? " checked=\"checked\"" : "";
  $strArticleReviewEmailChecked  = $strArticleReviewEmail  == 1 ? " checked=\"checked\"" : "";
  $strReviewEmailChecked         = $strReviewEmail         == 1 ? " checked=\"checked\"" : "";
  $strCommentNotifyChecked       = $strCommentNotify       == 1 ? " checked=\"checked\"" : "";
  $strCommentNotifyAuthorChecked = $strCommentNotifyAuthor == 1 ? " checked=\"checked\"" : "";
  $strUseNoFollowChecked         = $strUseNoFollow         == 1 ? " checked=\"checked\"" : "";

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  // Main form
  $strHTML = <<<END
<p>Settings: <a href="{FN_ADM_GENERAL_SETTINGS}" title="General Settings">General</a> | <b>Content</b> | <a href="{FN_ADM_FILES_SETTINGS}" title="File Settings">Files</a> | <a href="{FN_ADM_URL_SETTINGS}" title="URLs">URLs</a></p>
<h1>$strPageTitle</h1>
$strConfirmMsg
<form id="frmSystemPrefs" action="{FN_ADM_CONTENT_SETTINGS}" method="post">
<table class="DefaultTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour" />
    <col class="BaseColour NarrowCell" />
  </colgroup> 
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Content Settings</th>
  </tr>
  <tr>
    <td>
      <b><label for="txtRSSCount">Number of items in your feeds</label></b>
      <br /><i>Must be between 5 and 30 items</i>
    </td>
    <td>
      $strBreachedRSSCountText
      <input id="txtRSSCount" name="txtRSSCount" type="text" size="2" maxlength="2" value="$intRSSCount" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtTagThreshold">Show tags on the Tag Map if they have been used this often</label></b>
    </td>
    <td>
      <input id="txtTagThreshold" name="txtTagThreshold" type="text" size="2" maxlength="2" value="$intTagThreshold" />
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Comment Settings</th>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkUseCAPTCHA">Use CAPTCHA for comments</label></b>
    </td>
    <td>
      <input id="chkUseCAPTCHA" name="chkUseCAPTCHA" type="checkbox"$strUseCAPTCHAChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkUseNoFollow">Use nofollow for comment URLs</label></b>
    </td>
    <td>
      <input id="chkUseNoFollow" name="chkUseNoFollow" type="checkbox"$strUseNoFollowChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="txtNoFollowLimit">Don't use nofollow for commenters with at least this many comments</label></b>
    </td>
    <td>
      <input id="txtNoFollowLimit" name="txtNoFollowLimit" type="text" size="4" maxlength="3" value="$intNoFollowLimit" />
    </td>
  </tr>
  <tr>
    <th class="HeadColour SpanCell Left" colspan="2">Notification Settings</th>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkArticleReviewEmail">Email site admin when an article requires approval</label></b>
    </td>
    <td>
      <input id="chkArticleReviewEmail" name="chkArticleReviewEmail" type="checkbox"$strArticleReviewEmailChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkArticleNotifyAdmin">Email site admin when a new article is published</label></b>
      <br /><i>Articles created using the site admin email will not send a notification</i>
    </td>
    <td>
      <input id="chkArticleNotifyAdmin" name="chkArticleNotifyAdmin" type="checkbox"$strArticleNotifyAdminChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkReviewEmail">Email site admin when a comment is in moderation</label></b>
    </td>
    <td>
      <input id="chkReviewEmail" name="chkReviewEmail" type="checkbox"$strReviewEmailChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkCommentNotification">Email site admin when a new comment is posted</label></b>
      <br /><i>Comments posted using the site admin email will not send a notification</i>
    </td>
    <td>
      <input id="chkCommentNotification" name="chkCommentNotification" type="checkbox"$strCommentNotifyChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkCommentNotifyAuthor">Email article author when a comment is posted</label></b>
      <br /><i>The author's own comments will not be emailed to them</i>
    </td>
    <td>
      <input id="chkCommentNotifyAuthor" name="chkCommentNotifyAuthor" type="checkbox"$strCommentNotifyAuthorChecked />
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</form>

END;
  $CMS->AP->Display($strHTML);
?>