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
  
  $strAction = empty($_GET['action']) ? "" : $_GET['action'];
  if ($strAction == "create") {
    $strPageTitle = "New Article";
    $blnCreate = true;
    $blnEdit = false;
  } elseif ($strAction == "edit") {
    $strPageTitle = "Edit Article";
    $blnCreate = false;
    $blnEdit = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }
  $CMS->AP->SetTitle($strPageTitle);
	
	if ($blnEdit) {
        $intContentID = empty($_GET['id']) ? "" : $CMS->FilterNumeric($_GET['id']);
        if (!$intContentID) {
          $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
        }
	} else {
        $intContentID = '';
    }
	
  if ($blnCreate) {
    $categoryId = empty($_GET['area']) ? "" : $_GET['area'];
    if (!$CMS->RES->CanAddContent()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Author");
    }
  } elseif ($blnEdit) {
    $arrContent = $CMS->ART->GetArticle($intContentID);
    if (count($arrContent) == 0) {
      $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, $intContentID);
    }
    $categoryId = $arrContent['category_id'];
    $CMS->RES->EditArticle($categoryId, $intContentID);
		if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "EditArticle");
    }
  }
	
  $intUserID = $CMS->RES->GetCurrentUserID();
  $strWarnings = "";
  
  $blnNoContent = false;
  $blnNoTitle   = false;
  $strMissingContent = "";
  $strMissingTitle = "";
  $strArticleTags = "";
  $strContStatus = empty($_POST['txtStatus']) ? C_CONT_PUBLISHED : $_POST['txtStatus'];
  $strDraftSaved = "";
  $strExcerpt = ""; $intCustomOrder = "";
  $contentUrl = "";

	if ($_POST) {
		$blnSubmitForm = true;
    $intAuthorID = $_POST['txtAuthorID'];
    // Date/Time
    $intYear   = $_POST['optYear'];
    $intMonth  = $_POST['optMonth'];
    $intDay    = $_POST['optDay'];
    $intHHMMSS = $_POST['optHour'].":".$_POST['optMinute'].":".$_POST['optSecond'];
    $dteArticleCreated = $intYear."-".$intMonth."-".$intDay." ".$intHHMMSS;
    // We have to call stripslashes in case not all the data is entered
    // If we're ok to proceed, addslashes is used later on
		$strContTitle   = $CMS->StripSlashesIFW($_POST['txtTitle']);
		$strContBody    = $_POST['txtFormContent'];
		$strContURL     = $_POST['txtURL'];
		$strExcerpt     = $CMS->StripSlashesIFW($_POST['txtExcerpt']);
		$intCustomOrder = $CMS->FilterNumeric($_POST['txtCustomOrder']);
		if (!isset($intCustomOrder) || ($intCustomOrder == '')) $intCustomOrder = 0;
    $strArticleTags = $CMS->StripSlashesIFW($_POST['txtArticleTags']);
    // Validate area
    $categoryId = empty($_POST['optParent']) ? "" : $_POST['optParent'];
    // Link override
    $contentUrl = empty($_POST['content-url']) ? "" : $_POST['content-url'];
    // Validate content
    if (!$strContBody) {
      $blnSubmitForm = false;
      $strMissingContent = $CMS->AC->InvalidFormData("");
    }
    if (!$strContTitle) {
      $blnSubmitForm = false;
      $strMissingTitle = $CMS->AC->InvalidFormData("");
    } else {
      if (
          (strtolower($strContTitle) == "cp") ||
          (strtolower($strContTitle) == "data") ||
          (strtolower($strContTitle) == "info") ||
          (strtolower($strContTitle) == "installer") ||
          (strtolower($strContTitle) == "sys")
          )
        {
        $blnSubmitForm = false;
        $strMissingTitle = $CMS->AC->InvalidFormData(M_ERR_SYSTEM_SEO_ARTICLE_TITLE);
      } else {
        $blnInvalid = false;
        // Check if the link has been used
        $blnCheckLink = false;
        if ($blnCreate) {
            $intLinkStyle = $CMS->SYS->GetSysPref(C_PREF_LINK_STYLE);
            if (!in_array($intLinkStyle, array("1", "2", "5"))) {
                $blnCheckLink = true;
                $intCheckArticleID = 0;
            }
        } elseif ($blnEdit) {
            $blnCheckLink = true;
            $intCheckArticleID = $intContentID;
        }
        if ($blnCheckLink) {
            if ($contentUrl) {
                $strLink = $contentUrl;
            } else {
                $strSEOTitle = $CMS->MakeSEOTitle($strContTitle);
                $CMS->PL->SetTitle($strSEOTitle);
                $strLink = $CMS->PL->ViewArticle($intCheckArticleID, $categoryId);
                $CMS->PL->SetTitle("");
            }
            $blnInvalid = $CMS->UM->isUrlInUse($strLink, $intCheckArticleID, "");
            // Tell the user if it's invalid
            if ($blnInvalid) {
              $blnSubmitForm = false;
              $strMissingTitle = $CMS->AC->InvalidFormData(M_ERR_DUPLICATE_SEO_TITLE);
            }
        }
      }
    }
    if (!$categoryId) {
      $blnSubmitForm = false;
    }

    // ** Check if OK to submit ** //
  	if ($blnSubmitForm) {
      // Process title
      $strContTitle = $CMS->AddSlashesIFW($strContTitle);
      $strContTitle = $CMS->DoEntities($strContTitle);
      // Prepare content for database
      $CMS->AP->SetTitle($strPageTitle." - Results");

      $strReadMoreEditor = $CMS->AddSlashesIFW($CMS->AC->ReadMoreEditor());
      $strReadMorePublic = $CMS->AddSlashesIFW($CMS->AC->ReadMorePublic());
      
      if (strpos($strContBody, $strReadMoreEditor) !== false) {
          $strReadMorePublic = str_replace("<", "{".ZZZ_TEMP, $strReadMorePublic);
          $strReadMorePublic = str_replace(">", ZZZ_TEMP."}", $strReadMorePublic);
          $strContBody = str_replace($strReadMoreEditor, $strReadMorePublic, $strContBody);
      }
      
      $strContBody = strip_tags($strContBody, C_ARTICLE_TAGS);
      
      $strContBody = str_replace("{".ZZZ_TEMP, "<", $strContBody);
      $strContBody = str_replace(ZZZ_TEMP."}", ">", $strContBody);

      $strContBody = $CMS->AddSlashesIFW($strContBody);
      // Tags
      $strTagList = "";
      if ($blnEdit) {
        // Remove previous tags
        $strOldTags = $arrContent['tags'];
        if ($strOldTags) {
          if ($strArticleTags != $strOldTags) {
            $CMS->TG->RemoveArticleTags($strOldTags, $intContentID);
          }
        }
      }
      // Add new tags
      if ($strArticleTags) {
        // We don't have the content ID yet if we're creating
        if (!$blnCreate) {
          $strTagList = $CMS->TG->BuildIDList($strArticleTags, $intContentID);
        }
      }
      // Check if the user has publish access
      if (!$CMS->RES->CanPublish()) {
          $strContStatus = C_CONT_REVIEW;
      }
      $CMS->RES->ClearErrors();
      // Check if the article is future-dated
      if ($strContStatus == C_CONT_PUBLISHED) {
        if ($dteArticleCreated > $CMS->SYS->GetCurrentDateAndTime()) {
          $strContStatus = C_CONT_SCHEDULED;
        }
      }
      // Write to DB
      $strContURL = $CMS->AutoLink($strContURL);
      $strGroupList = ""; // No longer used
      if ($blnCreate) {
        if ($strContStatus == C_CONT_PUBLISHED) {
          $blnFirstPublish = true;
        } else {
          $blnFirstPublish = false;
        }
        $intContentID = $CMS->ART->Create($intAuthorID, $dteArticleCreated, $strContTitle,
            $strContBody, $categoryId, "", $strContURL, $strContStatus, $strGroupList,
            $strExcerpt, $intCustomOrder, $contentUrl);
        $strTagList = $CMS->TG->BuildIDList($strArticleTags, $intContentID);
        $CMS->ART->SetTags($intContentID, $strTagList);
        $arrCurrentData = $CMS->ART->GetArticle($intContentID);
      } elseif ($blnEdit) {
        $arrCurrentData = $CMS->ART->GetArticle($intContentID);
        if ($strContStatus == C_CONT_PUBLISHED) {
          if ($arrCurrentData['content_status'] == C_CONT_PUBLISHED) {
            // Article was already published
            $blnFirstPublish = false;
          } else {
            // Article has not been published before
            $blnFirstPublish = true;
          }
        } else {
          // Article is not yet published
          $blnFirstPublish = false;
        }
        $CMS->ART->Edit($intContentID, $intAuthorID, $strContTitle, $strContBody, 
            $dteArticleCreated, $categoryId, $strTagList, $strContURL, $strContStatus,
            $strGroupList, $strExcerpt, $intCustomOrder, $contentUrl);
        $CMS->ART->ClearUserlist($intContentID);
      }
      // Notify admin
      if ($strContStatus == C_CONT_REVIEW) {
        $CMS->MSG->ReviewArticleNotification($intContentID, $arrCurrentData);
      }
      if ($blnFirstPublish) {
        $CMS->MSG->NewArticleNotification($intContentID, $arrCurrentData);
      }
      // Build confirmation page
      $strAddAnother = "";
      if ($strContStatus == C_CONT_PUBLISHED) {
        $strItemMsg = "Article published.";
        if ($blnCreate) {
          $strAddAnother = " <a href=\"{FN_ADM_WRITE}?action=create&amp;area=$categoryId\">Add another article</a> :";
        }
        $strSEOTitle = $CMS->MakeSEOTitle($strContTitle);
        $CMS->PL->SetTitle($strSEOTitle);
        $CMS->ART->arrArticle = array(); // force a recache in case the article title changes
        // get article data
        $articleData = $CMS->ART->GetArticle($intContentID);
        // populate links
        $strViewArticle = $articleData['permalink'];
        $strViewArea    = $CMS->PL->ViewArea($categoryId);
        $strConfLinks = "\n<br /><a href=\"$strViewArticle\">View the article</a> :$strAddAnother";
      } elseif ($strContStatus == C_CONT_DRAFT) {
        $strItemMsg = "Draft saved.";
        $strConfLinks = "<a href=\"{FN_ADM_WRITE}?id=$intContentID&amp;action=edit\">Keep editing</a> : <a href=\"{FN_ADM_CONTENT_MANAGE}\">Return to Manage Content</a>";
      } elseif ($strContStatus == C_CONT_SCHEDULED) {
        $strItemMsg = "Article has been scheduled.";
        $strConfLinks = "<a href=\"{FN_ADM_WRITE}?id=$intContentID&amp;action=edit\">Keep editing</a> : <a href=\"{FN_ADM_CONTENT_MANAGE}\">Return to Manage Content</a>";
      } elseif ($strContStatus == C_CONT_REVIEW) {
        $strItemMsg = "Article submitted for review. An administrator will need to approve it before it goes live.";
        $strConfLinks = "<a href=\"{FN_ADM_CONTENT_MANAGE}\">Return to Manage Content</a>";
      }
      $strHTML = <<<PostArticleHTML
<h1 class="page-header">$strPageTitle</h1>
<p>$strItemMsg $strConfLinks</p>
PostArticleHTML;
      $CMS->AP->Display($strHTML);
  	}
  }
	// *** END DATABASE UPDATE *** //
  
  // *** START OF WRITE FORM *** //
  $strFileUploadText = "Attach a file (optional):";
  if ($_POST) {
      $strContBody = str_replace("\r", "", $strContBody);
      $strContBody = str_replace("\n", "", $strContBody);
      $strContBody = str_replace("'", "\'", $strContBody);
  } else {
    // ** Display data that was entered if form was not submitted ** //
    if ($blnCreate) {
      $dteArticleCreated = $CMS->SYS->GetCurrentDateAndTime();
      $strContTitle = "";
      $strContBody  = "";
      $strContURL   = "";
      if (!empty($_GET['area'])) {
        $categoryId = $CMS->FilterNumeric($_GET['area']);
      } else {
        $categoryId = 0;
      }
    } elseif ($blnEdit) {
      $strContTitle = $CMS->StripSlashesIFW($arrContent['title']);
      //$strContTitle = $CMS->DoEntities($strContTitle);
      $categoryId = $arrContent['category_id'];
      $dteArticleCreated = $arrContent['create_date_raw'];
      
      $strContBody = $arrContent['content'];
      
      $strReadMoreEditor = $CMS->AC->ReadMoreEditor();
      $strReadMorePublic = $CMS->AC->ReadMorePublic();
      if (strpos($strContBody, $strReadMorePublic) !== false) {
          $strContBody = str_replace($strReadMorePublic, $strReadMoreEditor, $strContBody);
      }
      
      $strContBody = str_replace("\r", "", $strContBody);
      $strContBody = str_replace("\n", "", $strContBody);
      $strContBody = str_replace("'", "\'", $strContBody);

      // Tags
      $strTagIDList = $arrContent['tags'];
      if ($strTagIDList) {
        $strArticleTags = $CMS->TG->BuildNameList($strTagIDList);
      }
      $strContURL = $arrContent['link_url'];
      $strExcerpt = $arrContent['article_excerpt'];
      $intCustomOrder = $arrContent['article_order'];

      $urlMapping = $CMS->UM->getActiveArticle($intContentID);
      if ($urlMapping) {
          $contentUrl = $urlMapping[0]['relative_url'];
      }
    }
  }
  
  $strModifiedAuthor = "";
  if ($blnCreate) {
    $strArticleAuthor = $CMS->RES->GetCurrentUser();
    $intAuthorID      = $CMS->RES->GetCurrentUserID();
  } elseif (($blnEdit) && ($arrContent['username'])) {
    $strArticleAuthor = $arrContent['username'];
    $intAuthorID      = $arrContent['author_id'];
  }
  
  if ($blnCreate) {
    $strFormTag  = "<form id=\"frmWrite\" enctype=\"multipart/form-data\" action=\"{FN_ADM_WRITE}?action=create\" method=\"post\" onsubmit=\"SendEditorData();\">\n";
  } elseif ($blnEdit) {
    $strFormTag  = "<form id=\"frmWrite\" enctype=\"multipart/form-data\" action=\"{FN_ADM_WRITE}?action=edit&amp;id=$intContentID\" method=\"post\" onsubmit=\"SendEditorData();\">\n";
	}

  $strPageTitle = $CMS->AP->GetTitle();

  // Areas
  $categoryDropDownHtml = $CMS->DD->CategoryList($categoryId, false);

  // Date
  $strDateLists = $CMS->AC->DateListsShort($dteArticleCreated);
  
  // Buttons
  $strDraftButton  = "<input type=\"button\" value=\"{M_BTN_SAVE_DRAFT}\" onclick=\"document.getElementById('txtStatus').value = '{C_CONT_DRAFT}'; document.getElementById('frmWrite').submit();\" />";
  $strSubmitButton = "<input type=\"button\" value=\"{M_BTN_PUBLISH_POST}\" onclick=\"document.getElementById('txtStatus').value = '{C_CONT_PUBLISHED}'; document.getElementById('frmWrite').submit();\" />";
  
  // Read More
  $strReadMoreLink = $CMS->AC->ReadMoreEditor();
  
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strModifiedAuthor
$strFormTag
$strDraftSaved

<div id="tabs">
    <ul>
    <li><a href="#tab-main">Content</a></li>
    <li><a href="#tab-settings">Settings</a></li>
    </ul>
    <div id="tab-main">

<div class="table-responsive">
<table class="table table-striped">
    <tr>
      <td><label for="txtTitle"><b>Title</b>:</label></td>
      <td>
        $strMissingTitle
        <input type="text" id="txtTitle" name="txtTitle" value="$strContTitle" maxlength="125" size="50" />
      </td>
      <td><b>Area</b>:</td>
      <td>
        <select id="optParent" name="optParent">
$categoryDropDownHtml
        </select>
      </td>
    </tr>
    <tr>
        <td>
            <label>URL:</label>
        </td>
        <td colspan="3">
            <input type="text" id="content-url" name="content-url" maxlength="255" size="50" value="$contentUrl">
        </td>
    </tr>
    <tr>
      <td class="BaseColour" colspan="4">
        $strMissingContent
        <textarea id="txtFormContent" name="txtFormContent" style="width: 100%; height: 400px;">$strContBody</textarea>
      </td>
    </tr>
  </table>
</div>
</div> <!-- /tab-main -->

<div id="tab-settings">

<div class="table-responsive">
<table class="table table-striped">
    <tr class="separator-row">
      <td colspan="2">Basic Information</td>
    </tr>
    <tr>
      <td>Author:</td>
      <td>
        $strArticleAuthor
        <input type="hidden" name="txtAuthorID" value="$intAuthorID" />
      </td>
    </tr>
    <tr>
      <td>
        <label for="txtArticleTags">Tags</label>
      </td>
      <td>
        <textarea id="txtArticleTags" name="txtArticleTags" rows="3" cols="50">$strArticleTags</textarea>
        <br />Separate tags with commas. e.g. weather, blue sky, clouds
      </td>
    </tr>
    <tr>
      <td>Date:</td>
      <td>
  $strDateLists
      </td>
    </tr>
    <tr class="separator-row">
      <td colspan="2">Additional Information</td>
    </tr>
    <tr>
      <td><label for="txtExcerpt">Custom Excerpt</label></td>
      <td>
        <textarea id="txtExcerpt" name="txtExcerpt" rows="4" cols="50">$strExcerpt</textarea>
      </td>
    </tr>
    <tr>
      <td><label for="txtCustomOrder">Custom Order</label></td>
      <td>
        <input type="text" id="txtCustomOrder" name="txtCustomOrder" value="$intCustomOrder" maxlength="10" size="5" />
      </td>
    </tr>
    <tr>
      <td><label for="txtURL">Custom Link:</label></td>
      <td>
        <input type="text" id="txtURL" name="txtURL" value="$strContURL" maxlength="150" size="60" />
        <br>
        <em>Good for e.g. third-party links.</em>
      </td>
    </tr>
  </table>
</div>

</div> <!-- /tabs -->

<table class="DefaultTable PageTable FixedTable" style="margin-top: 2px;" cellspacing="1">
  <tr>
    <td class="FootColour Centre">
      <input id="txtStatus" name="txtStatus" type="hidden" value="" />
      $strDraftButton $strSubmitButton
    </td>
  </tr>
</table>

</form>

END;

  // ** SCRIPT ** //
  $strHTML .= <<<FooterScript
<script type="text/javascript">
    var ckEditorElem = 'txtFormContent';

    $(document).ready(function() {

        var articleId = '$intContentID';

        $('#tabs').tabs();

        $('#txtTitle, #content-url').on('blur', function() {

            var articleTitle = $('#txtTitle').val();
            var currentUrl = $('#content-url').val();
            if ((articleId != '') && (currentUrl != '')) {
                return false;
            }

            $.post('/ajax/article/BuildUrl.php', {
                'id': articleId,
                'title': articleTitle,
                'currentUrl': currentUrl
            }, function(json) {
                console.log(json);
                var urlObject = $.parseJSON(json);
                if (urlObject.error == '') {
                    articleUrl = urlObject.url;
                    $('#content-url').val(articleUrl);
                } else {
                    $('#content-url').val('');
                }
            });

        });
    });
</script>
<script type="text/javascript" src="{URL_ROOT}assets/js/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{URL_ROOT}assets/js/injader/editor.js"></script>

FooterScript;

  $CMS->AP->Display($strHTML);
