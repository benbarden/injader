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
  $strPageTitle = "Manage Comments - Bulk Tools";
  $CMS->AP->SetTitle($strPageTitle);
  
  if (!$_POST) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "PostData");
  }
  
  $strAction = $_POST['optBulk'];
  $blnMove = false; $blnApprove = false; $blnApproveTrust = false; $blnDeny = false; $blnDenySuspend = false;
  if ($strAction == "approve") {
    $blnApprove = true;
    $strActionMsg = "You are about to approve \$intCommentCount comment(s).";
  } elseif ($strAction == "approvetrust") {
    $blnApproveTrust = true;
    $strActionMsg = "You are about to approve \$intCommentCount comment(s) and allow the authors to post additional comments in future without requiring your approval.";
  } elseif ($strAction == "deny") {
    $blnDeny = true;
    $strActionMsg = "You are about to deny \$intCommentCount comment(s).";
  } elseif ($strAction == "denysuspend") {
    $blnDenySuspend = true;
    $strActionMsg = "You are about to deny \$intCommentCount comment(s) and suspend the author accounts. This will stop them from posting further comments. It will also prevent them from logging in.";
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "Action");
  }
  
  // Comment type
  $strCommentType = empty($_POST['txtCommentType']) ? "" : $_POST['txtCommentType'];
  switch ($strCommentType) {
    case "approved":
    case "pending":
    case "spam":
      break;
    default:
      $strCommentType = "";
      break;
  }
  
  // ** Path 1: Update the database ** //
  
  if (!empty($_POST['txtCommentCount'])) {
    $strCommentIDs   = $_POST['txtCommentIDs'];
    $strArticleIDs   = empty($_POST['txtArticleIDs']) ? "" : $_POST['txtArticleIDs'];
    $strAuthorIDs    = $_POST['txtAuthorIDs'];
    $intCommentCount = $_POST['txtCommentCount'];
    if (($blnApprove) || ($blnApproveTrust)) {
      // Bulk approve
      $CMS->COM->BulkApprove($strCommentIDs);
      $strDidWhat = "approved";
      // Update articles
      if ($strArticleIDs) {
        $CMS->ART->BulkRefreshArticleCommentCount($strArticleIDs);
        $CMS->ART->BulkMarkAsNew($strArticleIDs);
        $CMS->MSG->BulkNewCommentNotification($strCommentIDs);
      }
    } elseif (($blnDeny) || ($blnDenySuspend)) {
      // Bulk deny
      $CMS->COM->BulkDeny($strCommentIDs);
      $strDidWhat = "denied";
      // Update articles
      if ($strArticleIDs) {
        $CMS->ART->BulkRefreshArticleCommentCount($strArticleIDs);
      }
    }
    if ($blnApproveTrust) {
      // Bulk trust
      $CMS->US->BulkTrust($strAuthorIDs);
    } elseif ($blnDenySuspend) {
      // Bulk suspend
      $CMS->US->BulkSuspend($strAuthorIDs);
    }
    $strCommentText = $intCommentCount == 1 ? "comment" : "comments";
    if ($strCommentType) {
      $strCommentURL = "?type=".$strCommentType;
    } else {
      $strCommentURL = "";
    }
    $strHTML = <<<ConfPage
<h1 class="page-header">$strPageTitle</h1>
<p>$intCommentCount $strCommentText $strDidWhat. <a href="{FN_ADM_COMMENTS}$strCommentURL">Manage Comments</a></p>

ConfPage;
    $CMS->AP->Display($strHTML);
  }
  
  // ** Path 2: Review and proceed ** //
  
  $arrOptions = empty($_POST['chkBulkOptions']) ? "" : $_POST['chkBulkOptions'];
  if (!$arrOptions) {
    $CMS->Err_MFail(M_ERR_BULK_NO_ITEMS, "BulkOptions");
  }
  
  // Build comment IDs
  $strCommentIDs = "(";
  $intCommentCount = count($arrOptions);
  for ($i=0; $i<$intCommentCount; $i++) {
    $intID = $arrOptions[$i];
    if ($i == 0) {
      $strCommentIDs .= $intID;
    } else {
      $strCommentIDs .= ",".$intID;
    }
  }
  $strCommentIDs .= ")";
  
  // Build article IDs
  $strArticleIDs = "";
  for ($i=0; $i<$intCommentCount; $i++) {
    $intID = $arrOptions[$i];
    $intArticleID = $CMS->COM->GetArticleID($intID);
    if ($intArticleID) {
      if ($strArticleIDs) {
        $strArticleIDs .= ",".$intArticleID;
      } else {
        $strArticleIDs = "(".$intArticleID;
      }
    }
  }
  if ($strArticleIDs) {
    $strArticleIDs .= ")";
  }
  
  // Build comment authors
  $arrCommentAuthors = $CMS->COM->BulkCommentAuthors($strCommentIDs);
  $strCommentAuthors = "(";
  for ($i=0; $i<count($arrCommentAuthors); $i++) {
    $intID = $arrCommentAuthors[$i]['author_id'];
    if ($i == 0) {
      $strCommentAuthors .= $intID;
    } else {
      $strCommentAuthors .= ",".$intID;
    }
  }
  $strCommentAuthors .= ")";
  
  $strSubmitButton = $CMS->AC->Submit(M_BTN_PROCEED);
  $strCancelButton = $CMS->AC->CancelButton();

  // Build form
  $strHTML = <<<FormContent
<h1 class="page-header">$strPageTitle</h1>
<p>$strActionMsg</p>
<form action="{FN_ADM_COMMENTS_BULK}" method="post">
<div>
<input type="hidden" name="optBulk" value="$strAction" />
<input type="hidden" name="txtCommentIDs" value="$strCommentIDs" />
<input type="hidden" name="txtArticleIDs" value="$strArticleIDs" />
<input type="hidden" name="txtAuthorIDs" value="$strCommentAuthors" />
<input type="hidden" name="txtCommentCount" value="$intCommentCount" />
<input type="hidden" name="txtCommentType" value="$strCommentType" />
</div>
<p>$strSubmitButton $strCancelButton</p>
</form>

FormContent;
  $strHTML = str_replace('$intCommentCount', $intCommentCount, $strHTML);
  $CMS->AP->Display($strHTML);
