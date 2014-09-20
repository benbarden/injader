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
  $strAction = $_GET['action'];
  if ($strAction == "create") {
    $strPageTitle = "Create Permission Profile";
    $blnCreate = true;
    $blnEdit = false;
  } elseif ($strAction == "edit") {
    $strPageTitle = "Edit Permission Profile";
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

  $strViewAreaGroupList       = "";
  $strCreateArticleGroupList  = "";
  $strPublishArticleGroupList = "";
  $strEditArticleGroupList    = "";
  $strDeleteArticleGroupList  = "";
  $strAttachFileGroupList     = "";
  $strAddCommentGroupList     = "";
  $strEditCommentGroupList    = "";
  $strDeleteCommentGroupList  = "";
  $strLockArticleGroupList    = "";

  if ($_POST) {
    $blnSubmitForm = true;
    $strProfileName = $_POST['txtProfileName'];
    if (!$strProfileName) {
      $blnSubmitForm = false;
      $strMissingName = $CMS->AC->InvalidFormData("");
    }
    $strViewAreaGroupList = !empty($_POST['chkViewArea']) ? $CMS->UG->BuildGroupList($_POST['chkViewArea'], "chkViewArea") : "";
    $strCreateArticleGroupList = !empty($_POST['chkCreateArticle']) ? $CMS->UG->BuildGroupList($_POST['chkCreateArticle'], "chkCreateArticle") : "";
    $strPublishArticleGroupList = !empty($_POST['chkPublishArticle']) ? $CMS->UG->BuildGroupList($_POST['chkPublishArticle'], "chkPublishArticle") : "";
    $strEditArticleGroupList = !empty($_POST['chkEditArticle']) ? $CMS->UG->BuildGroupList($_POST['chkEditArticle'], "chkEditArticle") : "";
    $strDeleteArticleGroupList = !empty($_POST['chkDeleteArticle']) ? $CMS->UG->BuildGroupList($_POST['chkDeleteArticle'], "chkDeleteArticle") : "";
    $strAttachFileGroupList = !empty($_POST['chkAttachFile']) ? $CMS->UG->BuildGroupList($_POST['chkAttachFile'], "chkAttachFile") : "";
    $strAddCommentGroupList = !empty($_POST['chkAddComment']) ? $CMS->UG->BuildGroupList($_POST['chkAddComment'], "chkAddComment") : "";
    $strEditCommentGroupList = !empty($_POST['chkEditComment']) ? $CMS->UG->BuildGroupList($_POST['chkEditComment'], "chkEditComment") : "";
    $strDeleteCommentGroupList = !empty($_POST['chkDeleteComment']) ? $CMS->UG->BuildGroupList($_POST['chkDeleteComment'], "chkDeleteComment") : "";
    $strLockArticleGroupList = !empty($_POST['chkLockArticle']) ? $CMS->UG->BuildGroupList($_POST['chkLockArticle'], "chkLockArticle") : "";
    if ($blnSubmitForm) {
      $strProfileName = $CMS->AddSlashesIFW($_POST['txtProfileName']);
      $strPageTitle .= " - Results";
      // Update database
      if ($blnCreate) {
        $strMsg = "created";
        $CMS->PP->Create($strProfileName, $strViewAreaGroupList, $strCreateArticleGroupList, $strPublishArticleGroupList, $strEditArticleGroupList, $strDeleteArticleGroupList, $strAddCommentGroupList, $strEditCommentGroupList, $strDeleteCommentGroupList, $strLockArticleGroupList, $strAttachFileGroupList);
      } elseif ($blnEdit) {
        $strMsg = "edited";
        $CMS->PP->Edit($intProfileID, $strProfileName, $strViewAreaGroupList, $strCreateArticleGroupList, $strPublishArticleGroupList, $strEditArticleGroupList, $strDeleteArticleGroupList, $strAddCommentGroupList, $strEditCommentGroupList, $strDeleteCommentGroupList, $strLockArticleGroupList, $strAttachFileGroupList);
      }
      // Display message
      $strHTML = <<<ConfirmHTML
<h1>$strPageTitle</h1>
<p>Permission profile $strMsg. <a href="{FN_ADM_PERMISSIONS}">Permission Profiles</a></p>

ConfirmHTML;
      $CMS->AP->Display($strHTML);
    }
  }
  if (!$_POST) {
    if ($blnEdit) {
      $arrPermissions = $CMS->PP->Get($intProfileID);
      $strProfileName             = $arrPermissions['name'];
      $strViewAreaGroupList       = $arrPermissions['view_area'];
      $strCreateArticleGroupList  = $arrPermissions['create_article'];
      $strPublishArticleGroupList = $arrPermissions['publish_article'];
      $strEditArticleGroupList    = $arrPermissions['edit_article'];
      $strDeleteArticleGroupList  = $arrPermissions['delete_article'];
      $strAttachFileGroupList     = $arrPermissions['attach_file'];
      $strAddCommentGroupList     = $arrPermissions['add_comment'];
      $strEditCommentGroupList    = $arrPermissions['edit_comment'];
      $strDeleteCommentGroupList  = $arrPermissions['delete_comment'];
      $strLockArticleGroupList    = $arrPermissions['lock_article'];
    }
  }
  $CMS->AP->SetTitle($strPageTitle);

  // Repopulate form
  $arrViewArea       = explode("|", $strViewAreaGroupList);
  $arrCreateArticle  = explode("|", $strCreateArticleGroupList);
  $arrPublishArticle = explode("|", $strPublishArticleGroupList);
  $arrEditArticle    = explode("|", $strEditArticleGroupList);
  $arrDeleteArticle  = explode("|", $strDeleteArticleGroupList);
  $arrAttachFile     = explode("|", $strAttachFileGroupList);
  $arrAddComment     = explode("|", $strAddCommentGroupList);
  $arrEditComment    = explode("|", $strEditCommentGroupList);
  $arrDeleteComment  = explode("|", $strDeleteCommentGroupList);
  $arrLockArticle    = explode("|", $strLockArticleGroupList);
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  $strViewAreaHTML       = $CMS->AC->DoCheckboxes($arrViewArea, "ViewArea");
  $strCreateArticleHTML  = $CMS->AC->DoCheckboxes($arrCreateArticle, "CreateArticle");
  $strPublishArticleHTML = $CMS->AC->DoCheckboxes($arrPublishArticle, "PublishArticle");
  $strEditArticleHTML    = $CMS->AC->DoCheckboxes($arrEditArticle, "EditArticle");
  $strDeleteArticleHTML  = $CMS->AC->DoCheckboxes($arrDeleteArticle, "DeleteArticle");
  $strAttachFileHTML     = $CMS->AC->DoCheckboxes($arrAttachFile, "AttachFile");
  $strAddCommentHTML     = $CMS->AC->DoCheckboxes($arrAddComment, "AddComment");
  $strEditCommentHTML    = $CMS->AC->DoCheckboxes($arrEditComment, "EditComment");
  $strDeleteCommentHTML  = $CMS->AC->DoCheckboxes($arrDeleteComment, "DeleteComment");
  $strLockArticleHTML    = $CMS->AC->DoCheckboxes($arrLockArticle, "LockArticle");
  
  if ($blnCreate) {
    $strFormTag = "<form id=\"frmAdminPerProf\" action=\"{FN_ADM_PERMISSION}?action=create\" method=\"post\">";
  } elseif ($blnEdit) {
    $strFormTag = "<form id=\"frmAdminPerProf\" action=\"{FN_ADM_PERMISSION}?action=edit&amp;id=$intProfileID\" method=\"post\">";
  }
  
  $strSelectGroups = $CMS->AC->PermissionQuickLinks();

  $strHTML = <<<END
<h1>$strPageTitle</h1>
$strSelectGroups
$strFormTag
<table class="DefaultTable FixedTable WideTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td><label for="txtProfileName">Name</label></td>
    <td>
      $strMissingName
      <input id="txtProfileName" name="txtProfileName" type="text" size="50" maxlength="100" value="$strProfileName" />
    </td>
  </tr>
  <tr>
    <td class="HeadColour SpanCell Bold" colspan="2">View</td>
  </tr>
  <tr>
    <td>View Area</td>
    <td>$strViewAreaHTML</td>
  </tr>
  <tr>
    <td class="HeadColour SpanCell Bold" colspan="2">Articles</td>
  </tr>
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
    <td class="HeadColour SpanCell Bold" colspan="2">Comments</td>
  </tr>
  <tr>
    <td>Add comment and edit own comments</td>
    <td>$strAddCommentHTML</td>
  </tr>
  <tr>
    <td>Edit all comments</td>
    <td>$strEditCommentHTML</td>
  </tr>
  <tr>
    <td>Delete comments</td>
    <td>$strDeleteCommentHTML</td>
  </tr>
  <tr>
    <td>Lock article to prevent comments</td>
    <td>$strLockArticleHTML</td>
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