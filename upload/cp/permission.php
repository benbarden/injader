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
  $strAction = $_GET['action'];
  if ($strAction == "create") {
    $strPageTitle = "Create Permission Profile";
    $blnCreate = true;
    $blnEdit = false;
  } elseif ($strAction == "edit") {
    $strPageTitle = "Edit Permissions";
    $blnCreate = false;
    $blnEdit = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }
  if ($blnEdit) {
    $intProfileID = $CMS->FilterNumeric($_GET['id']);
    if (!$intProfileID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
  }
  $CMS->AP->SetTitle($strPageTitle);

  $strProfileName = "";
  $strMissingName = "";

  $strCreateArticleGroupList  = "";
  $strPublishArticleGroupList = "";
  $strEditArticleGroupList    = "";
  $strDeleteArticleGroupList  = "";
  $strAttachFileGroupList     = "";

  if ($_POST) {
    $blnSubmitForm = true;
    $strCreateArticleGroupList = !empty($_POST['chkCreateArticle']) ? $CMS->UG->BuildGroupList($_POST['chkCreateArticle'], "chkCreateArticle") : "";
    $strPublishArticleGroupList = !empty($_POST['chkPublishArticle']) ? $CMS->UG->BuildGroupList($_POST['chkPublishArticle'], "chkPublishArticle") : "";
    $strEditArticleGroupList = !empty($_POST['chkEditArticle']) ? $CMS->UG->BuildGroupList($_POST['chkEditArticle'], "chkEditArticle") : "";
    $strDeleteArticleGroupList = !empty($_POST['chkDeleteArticle']) ? $CMS->UG->BuildGroupList($_POST['chkDeleteArticle'], "chkDeleteArticle") : "";
    $strAttachFileGroupList = !empty($_POST['chkAttachFile']) ? $CMS->UG->BuildGroupList($_POST['chkAttachFile'], "chkAttachFile") : "";
    if ($blnSubmitForm) {
      $strPageTitle .= " - Results";
      // Update database
      if ($blnCreate) {
        $strMsg = "created";
        $CMS->PP->Create($strProfileName, $strCreateArticleGroupList, $strPublishArticleGroupList, $strEditArticleGroupList, $strDeleteArticleGroupList, $strAttachFileGroupList);
      } elseif ($blnEdit) {
        $strMsg = "edited";
        $CMS->PP->Edit($intProfileID, $strCreateArticleGroupList, $strPublishArticleGroupList, $strEditArticleGroupList, $strDeleteArticleGroupList, $strAttachFileGroupList);
      }
      // Display message
      $strHTML = <<<ConfirmHTML
<h1 class="page-header">$strPageTitle</h1>
<p>Permissions $strMsg.</p>

ConfirmHTML;
      $CMS->AP->Display($strHTML);
    }
  }
  if (!$_POST) {
    if ($blnEdit) {
      $arrPermissions = $CMS->PP->Get($intProfileID);
      $strCreateArticleGroupList  = $arrPermissions['create_article'];
      $strPublishArticleGroupList = $arrPermissions['publish_article'];
      $strEditArticleGroupList    = $arrPermissions['edit_article'];
      $strDeleteArticleGroupList  = $arrPermissions['delete_article'];
      $strAttachFileGroupList     = $arrPermissions['attach_file'];
    }
  }
  $CMS->AP->SetTitle($strPageTitle);

  // Repopulate form
  $arrCreateArticle  = explode("|", $strCreateArticleGroupList);
  $arrPublishArticle = explode("|", $strPublishArticleGroupList);
  $arrEditArticle    = explode("|", $strEditArticleGroupList);
  $arrDeleteArticle  = explode("|", $strDeleteArticleGroupList);
  $arrAttachFile     = explode("|", $strAttachFileGroupList);

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  $strCreateArticleHTML  = $CMS->AC->DoCheckboxes($arrCreateArticle, "CreateArticle");
  $strPublishArticleHTML = $CMS->AC->DoCheckboxes($arrPublishArticle, "PublishArticle");
  $strEditArticleHTML    = $CMS->AC->DoCheckboxes($arrEditArticle, "EditArticle");
  $strDeleteArticleHTML  = $CMS->AC->DoCheckboxes($arrDeleteArticle, "DeleteArticle");
  $strAttachFileHTML     = $CMS->AC->DoCheckboxes($arrAttachFile, "AttachFile");

  if ($blnCreate) {
    $strFormTag = "<form id=\"frmAdminPerProf\" action=\"{FN_ADM_PERMISSION}?action=create\" method=\"post\">";
  } elseif ($blnEdit) {
    $strFormTag = "<form id=\"frmAdminPerProf\" action=\"{FN_ADM_PERMISSION}?action=edit&amp;id=$intProfileID\" method=\"post\">";
  }
  
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strFormTag
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td>Create article and edit own articles</td>
    <td>$strCreateArticleHTML</td>
  </tr>
  <tr>
    <td>Publish own articles</td>
    <td>$strPublishArticleHTML</td>
  </tr>
  <tr>
    <td>Edit all articles</td>
    <td>$strEditArticleHTML</td>
  </tr>
  <tr>
    <td>Delete article (can be reversed)</td>
    <td>$strDeleteArticleHTML</td>
  </tr>
  <tr>
    <td>Attach File</td>
    <td>$strAttachFileHTML</td>
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
