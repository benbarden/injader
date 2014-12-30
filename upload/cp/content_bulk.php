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
  $strPageTitle = "Manage Content - Bulk Tools";
  $CMS->AP->SetTitle($strPageTitle);
  
  if (!$_POST) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "PostData");
  }
  
  $strAction = $_POST['optBulk'];
  $blnMove       = false; $blnLock   = false; $blnUnlock = false;
  $blnPublish    = false; $blnDelete = false;
  $blnEditAuthor = false;
  if ($strAction == "move") {
    $blnMove = true;
  } elseif ($strAction == "lock") {
    $blnLock = true;
  } elseif ($strAction == "unlock") {
    $blnUnlock = true;
  } elseif ($strAction == "editauthor") {
    $blnEditAuthor = true;
  } elseif ($strAction == "publish") {
    $blnPublish = true;
  } elseif ($strAction == "delete") {
    $blnDelete = true;
  } elseif ($strAction == "restore") {
    $blnRestore = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "Action");
  }
  
  // ** Path 1: Update the database ** //
  
  if (!empty($_POST['txtArticleCount'])) {
    $strArticleIDs   = $_POST['txtArticleIDs'];
    $intArticleCount = $_POST['txtArticleCount'];
    if ($blnMove) {
      $intAreaID = $CMS->FilterNumeric($_POST['optArea']);
      if (!$intAreaID) {
        $CMS->Err_MFail(M_ERR_MISSINGPARAMS_USER, "Area");
      }
      $CMS->ART->BulkMove($intAreaID, $strArticleIDs);
      $strDidWhat = "moved";
    } elseif ($blnLock) {
      $CMS->ART->BulkLock($strArticleIDs);
      $strDidWhat = "locked";
    } elseif ($blnUnlock) {
      $CMS->ART->BulkUnlock($strArticleIDs);
      $strDidWhat = "unlocked";
    } elseif ($blnEditAuthor) {
      $intUserID = $CMS->FilterNumeric($_POST['optUser']);
      if (!$intUserID) {
        $CMS->Err_MFail(M_ERR_MISSINGPARAMS_USER, "User");
      }
      $CMS->ART->BulkEditAuthor($strArticleIDs, $intUserID);
      $strDidWhat = "updated";
    } elseif ($blnPublish) {
      $CMS->ART->BulkPublish($strArticleIDs);
      $strDidWhat = "published";
    } elseif ($blnDelete) {
      $CMS->ART->BulkDelete($strArticleIDs);
      $strDidWhat = "deleted";
    } elseif ($blnRestore) {
      $CMS->ART->BulkRestore($strArticleIDs);
      $strDidWhat = "restored";
    }
    $strArticleText = $intArticleCount == 1 ? "article" : "articles";
    $strHTML = <<<ConfPage
<h1 class="page-header">$strPageTitle</h1>
<p>$intArticleCount $strArticleText $strDidWhat. <a href="{FN_ADM_CONTENT_MANAGE}">Manage Content</a></p>

ConfPage;
    $CMS->AP->Display($strHTML);
  }
  
  // ** Path 2: Review and proceed ** //
  
  $arrOptions = empty($_POST['chkBulkOptions']) ? "" : $_POST['chkBulkOptions'];
  if (!$arrOptions) {
    $CMS->Err_MFail(M_ERR_BULK_NO_ITEMS, "BulkOptions");
  }
  
  // Build IDs for SQL
  $strArticleIDs = "(";
  for ($i=0; $i<count($arrOptions); $i++) {
    $intID = $arrOptions[$i];
    if ($i == 0) {
      $strArticleIDs .= $intID;
    } else {
      $strArticleIDs .= ",".$intID;
    }
  }
  $strArticleIDs .= ")";
  
  // Build article titles
  $strArticleTitles = "";
  $arrArticleTitles = $CMS->ART->BulkArticleTitles($strArticleIDs);
  $intArticleCount = count($arrArticleTitles);
  for ($i=0; $i<$intArticleCount; $i++) {
    $strTitle = $arrArticleTitles[$i]['title'];
    if ($i == 0) {
      $strArticleTitles .= "<ul>\n";
    }
    $strArticleTitles .= "<li>$strTitle</li>\n";
  }
  $strArticleTitles .= "</ul>\n";
  
  $strActionMsg = "";
  if ($blnMove) {
    // Build area list
    $strAreaList  = $CMS->DD->AreaHierarchy("", 0, "Content", false, false, C_NAV_PRIMARY);
    // Area list HTML
    $strFormContent = <<<AreaList
<p>Please select the destination area for the above articles. All articles will be moved to the area you select. If you do not wish to proceed, click the Cancel button now.</p>
<div>
<select id="optArea" name="optArea">
$strAreaList
</select>
<br><br>
</div>

AreaList;
  } elseif ($blnLock) {
    $strFormContent = "";
  } elseif ($blnUnlock) {
    $strFormContent = "";
  } elseif ($blnEditAuthor) {
    // Build user list
    $strListUsers = $CMS->DD->UserList("");
    // Area list HTML
    $strFormContent = <<<AreaList
<p>Please select the author to use for the above articles. The author of these articles will be changed to the user you specify. If you do not wish to proceed, click the Cancel button now.</p>
<div>
<select id="optUser" name="optUser">
$strListUsers
</select>
<br><br>
</div>

AreaList;
    $strActionMsg = "edit the author for";
  } elseif ($blnDelete) {
    $strFormContent = "";
  } elseif ($blnPublish) {
    $strFormContent = "";
  } elseif ($blnRestore) {
    $strFormContent = "";
  }
  if (!$strActionMsg) {
    $strActionMsg = $strAction;
  }
  
  $strSubmitButton = $CMS->AC->Submit(M_BTN_PROCEED);
  $strCancelButton = $CMS->AC->CancelButton();

  // Build form
  $strHTML = <<<FormContent
<h1 class="page-header">$strPageTitle</h1>
<p>You are about to $strActionMsg the following articles:</p>
$strArticleTitles
<form action="{FN_ADM_CONTENT_BULK}" method="post">
$strFormContent
<div>
<input type="hidden" name="optBulk" value="$strAction" />
<input type="hidden" name="txtArticleIDs" value="$strArticleIDs" />
<input type="hidden" name="txtArticleCount" value="$intArticleCount" />
</div>
<p>$strSubmitButton $strCancelButton</p>
</form>

FormContent;
  $CMS->AP->Display($strHTML);
