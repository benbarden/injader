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
  $intContentID = $CMS->FilterNumeric($_GET['id']);
  if (!$intContentID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
  }

  $arrContent = $CMS->ResultQuery("SELECT id, title, tags FROM {IFW_TBL_CONTENT} WHERE id = $intContentID", basename(__FILE__), __LINE__);
	if (count($arrContent) == 0) {
    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, $intContentID);
	}
  $arrData = $arrContent[0];

  $strPageTitle = "Edit Tags";
  if ($_POST) {
    $strPageTitle .= " - Results";
  }
  $CMS->AP->SetTitle($strPageTitle);

  $strTags = "";
  $strTagList = "";

  // ** Begin Post Data ** //
  
  if ($_POST) {
    $strNewTags = $_POST['txtTags'];
    $strOldTags = $arrData['tags'];
    if ($strOldTags) {
      if (($strNewTags) != ($strOldTags)) {
        $arrOldTags = explode(",", $strOldTags);
        for ($i=0; $i<count($arrOldTags); $i++) {
          $intTagID = $arrOldTags[$i];
          $CMS->TG->Minus($intTagID, $intContentID);
        }
      }
    }
    // Add new tags
    if ($strNewTags) {
      $strTagList = $CMS->TG->BuildIDList($strNewTags, $intContentID);
    }
    // Update database
    $CMS->Query("UPDATE {IFW_TBL_CONTENT} SET tags = '$strTagList' WHERE id = $intContentID", basename(__FILE__), __LINE__);
    // Log database write
    $intUserID = $CMS->RES->GetCurrentUserID();
    $CMS->SYS->CreateAccessLog("Edited article tags (ID: $intContentID)", AL_TAG_ARTICLE_EDITTAGS, $intUserID, "");
    // Confirmation page
    $strHTML = <<<ConfPage
<h1 class="page-header">$strPageTitle</h1>
<p>Tags updated successfully. <a href="{FN_ADM_CONTENT_MANAGE}">Manage Content</a></p>

ConfPage;
    $CMS->AP->Display($strHTML);
  }
  
  // ** End Post Data ** //
  
  $strTitle     = $arrData['title'];
  $strTagIDList = $arrData['tags'];
  if ($strTagIDList) {
    $strTags = $CMS->TG->BuildNameList($strTagIDList);
  }
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<form action="{FN_ADM_CONTENT_EDITTAGS}?id=$intContentID" method="post">
<div class="table-responsive">
<table class="table table-striped" style="width: 500px;">
  <tr>
    <td><strong>Title:</strong></td>
    <td>$strTitle</td>
  </tr>
  <tr>
    <td><label for="txtTags">Tags:</label></td>
    <td>
      <textarea id="txtTags" name="txtTags" rows="8" cols="40">$strTags</textarea>
      <br />Separate tags with commas. e.g. weather, blue sky, clouds
    </td>
  </tr>
  <tr>
    <td class="BaseColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</div>
</form>

END;

  $CMS->AP->Display($strHTML);
