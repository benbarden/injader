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
    $strArticleNotifyAdmin  = !empty($_POST['chkArticleNotifyAdmin']) ? 1 : 0;
    $strArticleReviewEmail  = !empty($_POST['chkArticleReviewEmail']) ? 1 : 0;
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
      if ($CMS->SYS->GetSysPref(C_PREF_ARTICLE_NOTIFY_ADMIN) != $strArticleNotifyAdmin) {
        $CMS->SYS->WriteSysPref(C_PREF_ARTICLE_NOTIFY_ADMIN, $strArticleNotifyAdmin);
      }
      if ($CMS->SYS->GetSysPref(C_PREF_ARTICLE_REVIEW_EMAIL) != $strArticleReviewEmail) {
        $CMS->SYS->WriteSysPref(C_PREF_ARTICLE_REVIEW_EMAIL, $strArticleReviewEmail);
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
        case C_PREF_ARTICLE_NOTIFY_ADMIN:   $strArticleNotifyAdmin  = $strValue; break;
        case C_PREF_ARTICLE_REVIEW_EMAIL:   $strArticleReviewEmail  = $strValue; break;
      }
    }
  }
  
  if ($blnSubmitForm) {
    $strConfirmMsg = "<p><b>Settings updated successfully.</b></p>\n\n";
  } else {
    $strConfirmMsg = "";
  }
  
  $strArticleNotifyAdminChecked  = $strArticleNotifyAdmin  == 1 ? " checked=\"checked\"" : "";
  $strArticleReviewEmailChecked  = $strArticleReviewEmail  == 1 ? " checked=\"checked\"" : "";

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  // Main form
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strConfirmMsg
<form id="frmSystemPrefs" action="{FN_ADM_CONTENT_SETTINGS}" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td>
      <b><label for="txtRSSCount">Feed item count</label></b>
      <br /><i>Must be between 5 and 30 items</i>
    </td>
    <td>
      $strBreachedRSSCountText
      <input id="txtRSSCount" name="txtRSSCount" type="text" size="2" maxlength="2" value="$intRSSCount" />
    </td>
  </tr>
  <tr>
    <td>
      <b><label for="txtTagThreshold">Minimum usage count to show on the Tag Map</label></b>
    </td>
    <td>
      <input id="txtTagThreshold" name="txtTagThreshold" type="text" size="2" maxlength="2" value="$intTagThreshold" />
    </td>
  </tr>
  <tr class="separator-row">
    <td colspan="2">Email site admin when:</td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkArticleReviewEmail">an article requires approval</label></b>
    </td>
    <td>
      <input id="chkArticleReviewEmail" name="chkArticleReviewEmail" type="checkbox"$strArticleReviewEmailChecked />
    </td>
  </tr>
  <tr>
    <td>
      <input type="hidden" name="dummy" value="dummy" />
      <b><label for="chkArticleNotifyAdmin">a new article is published</label></b>
    </td>
    <td>
      <input id="chkArticleNotifyAdmin" name="chkArticleNotifyAdmin" type="checkbox"$strArticleNotifyAdminChecked />
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</div>
</form>

END;
  $CMS->AP->Display($strHTML);
