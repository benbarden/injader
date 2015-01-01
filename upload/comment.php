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

  require 'sys/header.php';
  $strAction = empty($_GET['action']) ? "" : $_GET['action'];
  $blnCreate = false; $blnEdit = false; $blnDelete = false;
  $blnCheckID = false;
  switch($strAction) {
    case "create": $strPageTitle = "Add Comment";    $blnCreate = true; break;
    case "edit":   $strPageTitle = "Edit Comment";   $blnEdit = true;   $blnCheckID = true; break;
    case "delete": $strPageTitle = "Delete Comment"; $blnDelete = true; $blnCheckID = true; break;
    default: $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }

  $intAreaID = empty($_GET['area']) ? "" : $CMS->FilterNumeric($_GET['area']);
  if (!$intAreaID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "intAreaID");
  }

  if ($blnCheckID) {
    $intCommentID = empty($_GET['id']) ? "" : $CMS->FilterNumeric($_GET['id']);
    if (!$intCommentID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
  } else {
    $intCommentID = "";
  }

  $blnArticle = true;
  $blnFile = false;
  if ($blnCreate) {
    $CMS->RES->AddComment($intAreaID);
    if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "AddComment");
    }
  } elseif ($blnEdit) {
    $CMS->RES->EditComment($intAreaID, $intCommentID);
    if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "EditComment");
    }
  } elseif ($blnDelete) {
    $CMS->RES->DeleteComment($intAreaID);
    if ($CMS->RES->IsError()) {
      $CMS->Err_MFail(M_ERR_UNAUTHORISED, "DeleteComment");
    }
  }

  $strItemName = "article";
  if ($blnCheckID) {
    $intItemID = $CMS->COM->GetArticleID($intCommentID);
  } else {
    $intItemID = !empty($_POST['txtArticleID']) ? $CMS->FilterNumeric($_POST['txtArticleID']) : "";
  }
  if (!$intItemID) {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "txtItemID");
  }
  $intArticleID = $intItemID;
  $intFileID = 0;
  $arrArticle = $CMS->ART->GetArticle($intArticleID);
  if (!is_array($arrArticle)) {
    $this->Err_MFail(M_ERR_NO_ROWS_RETURNED, $intArticleID);
  }
  $strTitle = $arrArticle['title'];
  if ($arrArticle['locked'] == "Y") {
    $CMS->Err_MFail(M_ERR_COMMENT_LOCKED, "");
  }
  
  $intUserID = $CMS->RES->GetCurrentUserID();
  
  $strGuestName         = "";
  $strGuestEmail        = "";
  $strGuestURL          = "";
  $strContent           = "";
  $strMissingGuestName  = "";
  $strMissingGuestEmail = "";
  $strMissingContent    = "";
  $strNameInvalidChars  = "";
  $strEmailInvalidChars = "";
  $strNameTooShort      = "";
  $strDuplicateName     = "";
  $strInvalidCAPTCHA    = "";
  $strCommentStatus     = "";
  
  $intUseCAPTCHA = $CMS->SYS->GetSysPref(C_PREF_COMMENT_CAPTCHA);
  
  if ($_POST) {
    $blnSubmitForm = true;
    if (!$intUserID) {
      $blnLoggedOut = true;
      $strGuestName  = empty($_POST['txtGuestName'])  ? "" : $_POST['txtGuestName'];
      $strGuestEmail = empty($_POST['txtGuestEmail']) ? "" : $_POST['txtGuestEmail'];
      $strGuestURL   = empty($_POST['txtGuestURL'])   ? "" : $_POST['txtGuestURL'];
	  $strGuestURL   = $CMS->FilterAlphanumeric($strGuestURL);
	  $strGuestURL   = $CMS->AutoLink($strGuestURL);
      // ** Check for missing fields ** //
      if (!$strGuestName) {
        $blnSubmitForm = false;
        $strMissingGuestName = $CMS->AC->InvalidFormData("");
      }
      if (!$strGuestEmail) {
        $blnSubmitForm = false;
        $strMissingGuestEmail = $CMS->AC->InvalidFormData("");
      }
      // ** Check for invalid data ** //
      if ($strGuestName) {
        $strFilteredName = $CMS->FilterAlphanumeric($strGuestName, C_CHARS_USERNAME);
        if ($strFilteredName != $strGuestName) {
          $strNameInvalidChars = $CMS->AC->InvalidFormData(M_ERR_INVALID_CHARS);
          $blnSubmitForm = false;
          $strGuestName = $strFilteredName;
        }
      }
      if ($strGuestEmail) {
        if (!$CMS->IsValidEmail($strGuestEmail)) {
          if (!$strNameInvalidChars) {
            $strEmailInvalidChars = $CMS->AC->InvalidFormData(M_ERR_INVALID_EMAIL);
          }
          $blnSubmitForm = false;
        }
      }
      // Process CAPTCHA
      if ((empty($intUserID)) && ($intUseCAPTCHA == "1")) {
        session_start();
        $strAnswer  = empty($_SESSION['txtAnswer']) ? "" : $_SESSION['txtAnswer'];
        $strCAPTCHA = empty($_POST['txtCAPTCHA']) ? "" : $_POST['txtCAPTCHA'];
        $captchaHash = password_hash($strCAPTCHA, PASSWORD_BCRYPT);
        if (!$strAnswer || !$strCAPTCHA) {
          $blnValidCAPTCHA = false;
        } elseif ($strAnswer == $captchaHash) {
          $blnValidCAPTCHA = true;
        } else {
          $blnValidCAPTCHA = false;
        }
        if (!$blnValidCAPTCHA) {
          $strInvalidCAPTCHA = $CMS->AC->InvalidFormData("Incorrect verification string. Please try again.");
          $blnSubmitForm = false;
        }
      }
      if ($blnSubmitForm) {
        // ** Check for an existing user ** //
        $blnUsernameOK = false;
        if ($CMS->US->IsUsernameLengthValid($strGuestName)) {
          if ($CMS->US->IsUniqueUsername($strGuestName) && $CMS->US->IsUniqueEmail($strGuestEmail)) {
            $blnUsernameOK = true;
          } else {
            $intTempUserID = $CMS->US->IsUsernameInUse($strGuestName, $strGuestEmail);
            if (!$intTempUserID) {
              $strDuplicateName = $CMS->AC->InvalidFormData(M_ERR_USERNAME_IN_USE);
              $blnSubmitForm = false;
            } else {
              $intUserID = $intTempUserID; // Retrieve guest user ID
              // Block comments from suspended users
              if ($CMS->US->IsSuspended($intUserID)) {
                $CMS->Err_MFail(M_ERR_USER_SUSPENDED, "");
              }
            }
          }
        } else {
          $strNameTooShort = $CMS->AC->InvalidFormData(M_ERR_USERNAME_TOO_SHORT);
          $blnSubmitForm = false;
        }
      }
    } else {
      $blnLoggedOut = false;
    }
    if (empty($_POST['txtContent'])) {
      $strContent = "";
    } else {
      $strContent = $CMS->DoEntities($_POST['txtContent']);
      //$strContent = strip_tags($_POST['txtContent']);
    }
    // Preview
    $blnPreview = empty($_POST['chkPreview']) ? false : true;
    if ($blnPreview) {
      $blnSubmitForm = false;
    }
    // Submit
    if ($blnSubmitForm) {
      // Make link
      if ($blnArticle) {
        $strViewLink = $CMS->PL->ViewArticle($intArticleID);
        $strItemDesc = "article";
      }
      if ($blnCreate) {
        $blnDoSpamCheck = false;
        $intUserIP = $_SERVER['REMOTE_ADDR'];
        if (empty($intUserID)) {
          $strAuthorName  = $strGuestName;
          $strAuthorEmail = $strGuestEmail;
          $blnDoSpamCheck = true;
        } else {
          // ** Existing user info ** //
          $arrUserDetails = $CMS->ResultQuery("
            SELECT username, email, user_moderate FROM {IFW_TBL_USERS} 
            WHERE id = $intUserID
          ", basename(__FILE__), __LINE__);
          $strAuthorName  = $arrUserDetails[0]['username'];
          $strAuthorEmail = $arrUserDetails[0]['email'];
          // If moderation is OFF for this user, approve the comment
          if ($arrUserDetails[0]['user_moderate'] == "N") {
            $strCommentStatus = "Approved";
          } else {
            $blnDoSpamCheck = true;
          }
        }
        
        // ** Auto-approve previous guest comments ** //
        $arrUserStatData = $CMS->UST->Get($strAuthorEmail);
        if (is_array($arrUserStatData)) {
          if ($arrUserStatData['comment_count'] > 0) {
            $strCommentStatus = "Approved";
            $blnDoSpamCheck = false;
          }
        }
        
        // ** Spam checker ** //
        if ($blnDoSpamCheck) {
        	
          $strSpamCheck = strtolower($strContent);
          if (strpos($strSpamCheck, "http://") !== false) {
            $strCommentStatus = "Spam";
          } elseif (strpos($strSpamCheck, "[link=") !== false) {
            $strCommentStatus = "Spam";
          } elseif (strpos($strSpamCheck, "[url=") !== false) {
            $strCommentStatus = "Spam";
          } elseif (strpos($strSpamCheck, "<a href=") !== false) {
            $strCommentStatus = "Spam";
          } elseif (strpos($strSpamCheck, "&lt;a href=") !== false) {
            $strCommentStatus = "Spam";
          } else {
          	$strCommentStatus = "Pending";
          	
          	// ** Check the spam rules ** //
          	if ($CMS->SR->MatchAll($strAuthorName, $strAuthorEmail, 
              $strGuestURL, $strContent, $intUserIP)) {
              $strCommentStatus = "Spam";
            }
          	
          }
          
        }
        
        // ** Subscribe ** //
        $blnSubscribe = empty($_POST['chkSubscribe']) ? false : true;
        if ($blnSubscribe) {
          $strSubs = $intArticleID;
        } else {
          $strSubs = "";
        }
        // ** Comment count / subscription ** //
        if ($strCommentStatus == "Approved") {
          $CMS->UST->Plus($strAuthorEmail, $strSubs);
        } elseif ($strCommentStatus == "Pending") {
          $CMS->UST->SetSub($strAuthorEmail, $strSubs);
        }
        // ** Cookie goodness ** //
        if (($strCommentStatus != "Spam") && (!$intUserID)) {
          $intGuestCookieDuration = $CMS->SYS->GetGuestCookieDuration();
          // Guest name
          if ($CMS->CK->Get(C_CK_COMMENT_NAME) != $strGuestName) {
            $CMS->CK->Set(C_CK_COMMENT_NAME, $strGuestName, $intGuestCookieDuration);
          }
          // Guest email
          if ($CMS->CK->Get(C_CK_COMMENT_EMAIL) != $strGuestEmail) {
            $CMS->CK->Set(C_CK_COMMENT_EMAIL, $strGuestEmail, $intGuestCookieDuration);
          }
          // Guest URL
          if ($CMS->CK->Get(C_CK_COMMENT_URL) != $strGuestURL) {
            $CMS->CK->Set(C_CK_COMMENT_URL, $strGuestURL, $intGuestCookieDuration);
          }
        }
        // ** Content ** //
        $strContent = $CMS->FMT->CMSToHTML($strContent);
        $strContent = nl2br($strContent);
        $strNotifyContent = $strContent; // do this now to save time later
        $dteDate = $CMS->SYS->GetCurrentDateAndTime();
        // ** Build Query: Add Comment ** //
        $strQuery = sprintf("INSERT INTO {IFW_TBL_COMMENTS}(story_id, comment_count, content, create_date, edit_date, author_id, upload_id, ip_address, comment_status, guest_name, guest_email, guest_url) VALUES(%s, %s, '%s', '%s', '%s', %s, %s, '%s', '%s', '%s', '%s', '%s')",
          $intArticleID, 0,
          mysql_real_escape_string($strContent),
          mysql_real_escape_string($dteDate),
          '0000-00-00 00:00', $intUserID, $intFileID,
          mysql_real_escape_string($intUserIP),
          mysql_real_escape_string($strCommentStatus),
          mysql_real_escape_string($strGuestName),
          mysql_real_escape_string($strGuestEmail),
          mysql_real_escape_string($strGuestURL)
        );
        // ** Process query ** //
        $intCommentID = $CMS->Query($strQuery, basename(__FILE__), __LINE__);
        // ** Log ** //
        $CMS->AL->Build(AL_TAG_COMMENT_ADD, $intCommentID, "");
        // Append comment ID to page link
        $strRawPageLink = $strViewLink; // required if the comment is awaiting review
        // Notifications
        $intNotify = 0;
        if ($strCommentStatus == "Approved") {
          // Comment is now live
          $CMS->MSG->NewCommentNotification($intCommentID, $intArticleID, $strAuthorName, $strAuthorEmail, $strItemDesc, $strTitle, $strNotifyContent, $strViewLink);
        } elseif ($strCommentStatus == "Pending") {
          // Comment is awaiting review
          $CMS->MSG->ReviewCommentNotification($intArticleID, $strAuthorName, $strAuthorEmail, $strItemDesc, $strTitle, $strNotifyContent);
        }
        // Mark as new
        if ($strCommentStatus == "Approved") {
          if ($blnArticle) {
            $CMS->ART->MarkAsNew($intArticleID);
          }
          // ** Update comment count ** //
          $CMS->ART->RefreshArticleCommentCount($intArticleID);
          // ** Do comment jump ** //
          $strViewLink .= "#c".$intCommentID;
        }
        // ** Confirmation ** //
        $strHTML = <<<ConfirmPage
<div id="pagecontent">
<h1>Thanks for your comment!</h1>
<p><a href="$strViewLink">Go back to the article you just commented on</a>.</p>
</div>
ConfirmPage;
        $CMS->MV->DefaultPage("Thanks for your comment!", $strHTML);
        // ** Redirect ** //
        //httpRedirect($strPageURL);
      } elseif ($blnEdit) {
        $strContent = $CMS->FMT->CMSToHTML($strContent);
        $strContent = nl2br($strContent);
        $strContent = $CMS->AddSlashesIFW($strContent);
        $dteDate = $CMS->SYS->GetCurrentDateAndTime();
        $CMS->COM->Edit($intCommentID, $strContent, $dteDate, "<a href=\"$strViewLink\">comment</a>");
        // Append comment ID to page link
        $strViewLink .= "#c".$intCommentID;
        //$strPageURL = "http://".SVR_HOST.$strViewLink;
        // ** Confirmation ** //
        $strHTML = <<<ConfirmPage
<div id="pagecontent">
<h1>Edit Comment - Results</h1>
<p>Comment successfully updated. <a href="$strViewLink">View updated comment</a>.</p>
</div>
ConfirmPage;
        $CMS->MV->DefaultPage("Edit Comment - Results", $strHTML);
      } elseif ($blnDelete) {
        // ** Update user comment stats ** //
        $arrCommentData = $CMS->COM->GetComment($intCommentID);
        if ($arrCommentData['email']) {
          $strEmail = $arrCommentData['email'];
        } elseif ($arrCommentData['guest_email']) {
          $strEmail = $arrCommentData['guest_email'];
        } else {
          $strEmail = "";
        }
        if ($strEmail) {
          $CMS->UST->Minus($strEmail);
        }
        // ** Delete from database ** //
        $CMS->COM->Delete($intCommentID, "");
        // ** Update comment count ** //
        $CMS->ART->RefreshArticleCommentCount($intArticleID);
        // ** Build link ** //
        if ($blnArticle) {
          $strViewLink = $CMS->PL->ViewArticle($intItemID);
        }
        // ** Confirmation ** //
        $strHTML = <<<ConfirmPage
<div id="pagecontent">
<h1>Delete Comment - Results</h1>
<p>Comment successfully deleted. <a href="$strViewLink">View $strItemName</a>.</p>
</div>
ConfirmPage;
        $CMS->MV->DefaultPage("Delete Comment - Results", $strHTML);
      }
    }
  }
  
  // ** NO POST ** //

  if ($_POST) {
    if (($blnCreate) && ($blnLoggedOut)) {
      $strGuestName  = $CMS->StripSlashesIFW($strGuestName);
      $strGuestEmail = $CMS->StripSlashesIFW($strGuestEmail);
      $strGuestURL   = $CMS->StripSlashesIFW($strGuestURL);
      $strContent    = $CMS->StripSlashesIFW($strContent);
    }
  } else {
    if ($blnCheckID) {
      $arrCommentData = $CMS->COM->GetComment($intCommentID);
      $intArticleID   = $arrCommentData['story_id'];
      $intItemID = $intArticleID;
      $strContent = $arrCommentData['content'];
      $strContent = str_replace("<br />", "", $strContent);
      $strContent = str_replace("{", "{".ZZZ_TEMP, $strContent);
      $strContent = str_replace("}", ZZZ_TEMP."}", $strContent);
      $strContent = $CMS->FMT->HTMLToCMS($strContent);
    }
  }

  $strPostButtons  = $CMS->AC->PostButtons("txtContent");
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();
  
  if ($blnDelete) {
    if (!$strContent) {
      $strContent = " ";
    }
    $strActionLink = FN_COMMENT;
    $strHTML = <<<DeleteComment
<div id="pagecontent">
<h1>$strPageTitle</h1>
<form action="$strActionLink?action=delete&amp;id=$intCommentID&amp;type=$strItemName&amp;area=$intAreaID" method="post">
<p>You are about to delete the following comment:</p>
<input type="hidden" name="txtContent" value="$strContent" />
<textarea rows="5" cols="50" disabled="disabled">$strContent</textarea>
<p>$strSubmitButton $strCancelButton</p>
</form>
</div>

DeleteComment;
  } else {
  	
    $strCommentForm = $CMS->AC->CommentForm(
      $intCommentID, $intItemID, $strAction, $intAreaID, 
      $strMissingGuestName, $strMissingGuestEmail, $strMissingContent, 
      $strGuestName, $strGuestEmail, $strGuestURL, $strContent
    );
    
    $strHTML = <<<CommentPage
<div id="pagecontent">
<h1>$strPageTitle</h1>
$strNameTooShort
$strDuplicateName
$strNameInvalidChars
$strEmailInvalidChars
$strInvalidCAPTCHA
$strCommentForm
</div>

CommentPage;
    
  }
  
  $CMS->MV->DefaultPage($strPageTitle, $strHTML);
?>