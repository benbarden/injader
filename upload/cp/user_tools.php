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
  $intUserID = $CMS->RES->GetCurrentUserID();
  
  $blnCheckID = true;
  $blnCheckAvatar = false;
  $blnDeleteArticle = false; $blnUnmarkArticle = false;
  $blnLockArticle = false; $blnUnlockArticle = false;

  $strAction = $_GET['action'];
  switch ($strAction) {
    case "setavatar":     $strPageTitle = "Set Avatar";     $blnCheckAvatar = true; break;
    case "clearavatar":   $strPageTitle = "Clear Avatar";   $blnCheckID = false; $blnCheckAvatar = false; break;
    case "deleteavatar":  $strPageTitle = "Delete Avatar";  $blnCheckAvatar = true; break;
    case "deletearticle": $strPageTitle = "Delete Article"; $blnDeleteArticle = true; break;
    case "lockarticle":   $strPageTitle = "Lock Article";   $blnLockArticle = true; break;
    case "unlockarticle": $strPageTitle = "Unlock Article"; $blnUnlockArticle = true; break;
    default: $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction"); break;
  }
  
  if ($blnCheckID) {
    $intItemID = $CMS->FilterNumeric($_GET['id']);
    if (!$intItemID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
  }
  if ($blnCheckAvatar) {
    if (!$CMS->FL->IsAvatar($intItemID, $intUserID)) {
      $CMS->Err_MFail(M_ERR_NOT_AN_AVATAR, "");
    }
    if (!$CMS->FL->IsFileAuthor($intItemID, $intUserID)) {
      $CMS->Err_MFail(M_ERR_AVATAR_NOT_YOURS, "");
    }
  }
  
  if ($blnDeleteArticle) {
    // Validate access
    $intAreaID = $CMS->ART->GetArticleAreaID($intItemID);
    $CMS->RES->DeleteArticle($intAreaID);
    if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "");
    }
    // Check if this is a valid action
    $blnDeleted = $CMS->ART->IsDeleted($intItemID);
    if (($blnDeleteArticle) && ($blnDeleted)) {
      $CMS->Err_MFail(M_ERR_ARTICLE_MARKED, "");
    }
  }
  if (($blnLockArticle) || ($blnUnlockArticle)) {
    // Validate access
    $intAreaID = $CMS->ART->GetArticleAreaID($intItemID);
    $CMS->RES->LockArticle($intAreaID);
    if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "");
    }
    // Check if this is a valid action
    $blnLocked = $CMS->ART->IsLocked($intItemID);
    if (($blnLockArticle) && ($blnLocked)) {
      $CMS->Err_MFail(M_ERR_ARTICLE_LOCKED, "");
    } elseif (($blnUnlockArticle) && (!$blnLocked)) {
      $CMS->Err_MFail(M_ERR_ARTICLE_UNLOCKED, "");
    }
  }
  
  $strReturnURL = empty($_GET['back']) ? "" : $_GET['back'];
  
  $intUserID = $CMS->RES->GetCurrentUserID();
  $strFormAction = "?action=$strAction&amp;id=$intItemID&amp;back=$strReturnURL";
  $strSubmitButton = $CMS->AC->Submit(M_BTN_PROCEED);
  $strCancelButton = $CMS->AC->CancelButton();
  
  $blnUCPRedirect = false;
  switch ($strAction) {
    case "clearavatar":
      $CMS->Query("UPDATE {IFW_TBL_USERS} SET avatar_id = 0 WHERE id = $intUserID", basename(__FILE__), __LINE__);
      $CMS->SYS->CreateAccessLog("Cleared avatar", AL_TAG_AVATAR_UNSET, $intUserID);
      $blnUCPRedirect = true;
      break;
    case "setavatar":
      $CMS->Query("UPDATE {IFW_TBL_USERS} SET avatar_id = $intItemID WHERE id = $intUserID", basename(__FILE__), __LINE__);
      $CMS->SYS->CreateAccessLog("Set avatar (File ID: $intItemID)", AL_TAG_AVATAR_SET, $intUserID);
      $blnUCPRedirect = true;
      break;
    case "deleteavatar":
      if ($_POST) {
        $CMS->FL->Delete($intItemID, $intUserID, "");
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Avatar deleted. <a href=\"$strReturnURL\">Return</a></p>\n";
      } else {
        $strFormMsg = "<p>You are about to delete your avatar with file ID: $intItemID.</p>";
      }
      break;
    case "deletearticle":
      if ($_POST) {
        $CMS->ART->Mark($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Article deleted. <a href=\"$strReturnURL\">Return</a></p>\n";
      } else {
        $strArticleTitle = $CMS->ART->GetTitle($intItemID);
        $strFormMsg = <<<DeleteArticle
<p>You are about to delete the following article:</p>
<ul>
<li>Title: $strArticleTitle</li>
<li>ID: $intItemID</li>
</ul>

DeleteArticle;
      }
      break;
    case "lockarticle":
      if ($_POST) {
        $CMS->ART->Lock($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Article locked. <a href=\"$strReturnURL\">Return</a></p>\n";
      } else {
        $strArticleTitle = $CMS->ART->GetTitle($intItemID);
        $strFormMsg = <<<LockArticle
<p>You are about to lock the following article:</p>
<ul>
<li>Title: $strArticleTitle</li>
<li>ID: $intItemID</li>
</ul>

LockArticle;
      }
      break;
    case "unlockarticle":
      if ($_POST) {
        $CMS->ART->Unlock($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Article unlocked. <a href=\"$strReturnURL\">Return</a></p>\n";
      } else {
        $strArticleTitle = $CMS->ART->GetTitle($intItemID);
        $strFormMsg = <<<UnlockArticle
<p>You are about to unlock the following article:</p>
<ul>
<li>Title: $strArticleTitle</li>
<li>ID: $intItemID</li>
</ul>

UnlockArticle;
      }
      break;
  }

  if ($blnUCPRedirect) {
    httpRedirect("http://".SVR_HOST.FN_ADM_MANAGE_AVATARS);
  }

  $CMS->AP->SetTitle($strPageTitle);

  if (!$_POST) {
    $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strFormMsg
<form action="{FN_USER_TOOLS}$strFormAction" method="post">
<p><input type="hidden" name="dummy" value="$intItemID" /></p>
<p>$strSubmitButton $strCancelButton</p>
</form>
</div>

END;
  }

  $CMS->AP->Display($strHTML);
