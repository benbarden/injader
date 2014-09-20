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
  
  $intStepA = empty($_POST['txtStep']) ? "" : $CMS->FilterNumeric($_POST['txtStep']);
  if ($intStepA) {
    $intStep = $intStepA;
  } else {
    $intStep = empty($_GET['step']) ? 1 : $CMS->FilterNumeric($_GET['step']);
  }
  switch ($intStep) {
    case 1:
    case 2:
      // OK to proceed
      break;
    default:
      $CMS->Err_MFail("Invalid step.", "");
  }

  $strFileError = "";
  
  if ($intStep == 2) {
    if ($_POST) {
      // Process file errors
      if (($_FILES['txtFile']['error']) && ($_FILES['txtFile']['name'])) {
        $strFileSubmitError = $CMS->FL->SubmissionError($_FILES['txtFile']['error']);
        $strFileError = $CMS->AC->InvalidFormData($strFileSubmitError);
        $intStep = 1;
      } else {
        if ($_FILES['txtFile']['name']) {
          $strImportPath = $_FILES['txtFile']['tmp_name'];
        }
        if (empty($strImportPath)) {
          $strFileError = $CMS->AC->InvalidFormData("Please upload a file.");
          $intStep = 1;
        }
      }
    }
  }
  
  $strPageTitle = "WordPress Import: Step $intStep";
  $CMS->AP->SetTitle($strPageTitle);
  
  if ($intStep == 1) {
    $strHTML = <<<ImportPage
<h1>$strPageTitle</h1>
<h2>Warning - Read this first!</h2>
<p>This will delete all existing articles, comments, files, and users on your site. Running an import should be done immediately after you install Injader. <b>Do not proceed if you have articles and comments in Injader that you need to keep!</b></p>
<p><b>Note:</b> The default Injader admin user (ID: 1) will not be deleted. However, if a user with ID 1 does not exist, it will be created with admin privileges. If this occurs, you can log in with the username <i>admin</i>, password <i>12345</i>. This is NOT a secure password and you are STRONGLY advised to change it immediately after the import is complete.</p>
<h2>What you need to do</h2>
<ol>
<li>Go to your WordPress Dashboard, and click on Tools - Export.</li>
<li>Export your WordPress site. Save the XML file.</li>
<li>Import the XML file below.</li>
</ol>
<p><b>Please note: This process may take some time to complete.</b> Don't abort the process once it has started.</p>
<form action="{FN_ADM_IMPORT_WP}" enctype="multipart/form-data" method="post">
<table class="DefaultTable NarrowTable" cellspacing="1">
  <tr>
    <td class="InfoColour Left"><b>Import XML file</b></td>
  </tr>
  <tr>
    <td class="BaseColour">
      $strFileError
      <input type="hidden" name="MAX_FILE_SIZE" value="50000000" />
      <input type="hidden" id="txtStep" name="txtStep" value="2" />
      <input type="file" id="txtFile" name="txtFile" size="30" />
    </td>
  </tr>
  <tr>
    <td class="FootColour Centre">
      <input type="submit" value="Proceed" />
    </td>
  </tr>
</table>
</form>

ImportPage;
  } elseif ($intStep == 2) {
    $strHTML = <<<ImportPage
<h1>$strPageTitle</h1>
<p>WordPress data successfully imported!</p>

ImportPage;
  }
  
  if ($intStep == 2) {
    // Setup
    $CMS->Query("DELETE FROM {IFW_TBL_AREAS} WHERE name NOT IN ('Home', 'Forum')", basename(__FILE__), __LINE__);
    $CMS->Query("DELETE FROM {IFW_TBL_USERS} WHERE id > 1", basename(__FILE__), __LINE__);
    $CMS->Query("ALTER TABLE {IFW_TBL_USERS} AUTO_INCREMENT = 2", basename(__FILE__), __LINE__);
    $CMS->Query("DELETE FROM {IFW_TBL_CONTENT}", basename(__FILE__), __LINE__);
    $CMS->Query("DELETE FROM {IFW_TBL_TAGS}", basename(__FILE__), __LINE__);
    $CMS->Query("DELETE FROM {IFW_TBL_UPLOADS}", basename(__FILE__), __LINE__);
    $CMS->Query("DELETE FROM {IFW_TBL_COMMENTS}", basename(__FILE__), __LINE__);
    // System defaults
    $intDefaultAreaID = $CMS->AR->GetDefaultAreaID("");
    $strUserGroups    = $CMS->UG->GetDefaultGroup();
    $strAdminGroup    = $CMS->UG->GetAdminGroupID();
    $dteCurrentDate   = $CMS->SYS->GetCurrentDateAndTime();
    // If there's no user with ID 1, create them
    $arrUsers = $CMS->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_USERS} WHERE ID = 1", basename(__FILE__), __LINE__);
    if ($arrUsers[0]['count'] == "0") {
      //$CMS->US->Create("", "admin", "12345", "", "", "", "", "", "", "", "", 0, $dteCurrentDate, "", $strUserGroups."|".$strAdminGroup);
      $strPass = md5("12345");
      $CMS->Query("INSERT INTO {IFW_TBL_USERS}(id, username, userpass, forename, surname, email, location, occupation, interests, homepage_link, homepage_text, avatar_id, join_date, ip_address, user_groups, seo_username) VALUES (1, 'admin', '$strPass', '', '', '', '', '', '', '', '', 0, '$dteCurrentDate', '', '$strUserGroups|$strAdminGroup', 'admin')", basename(__FILE__), __LINE__);
    }
    // Load import data
    $xml = file_get_contents($strImportPath);
    preg_match_all("/\<item\>(.*?)\<\/item\>/s", $xml, $itemblocks);
    foreach ($itemblocks[1] as $arrContentBlock) {
      $intAreaID = ""; // Reset
      // ********* STEP 1. IMPORT CONTENT ********** //
      // ** Tag matching ** //
      preg_match_all("/\<wp\:post_id\>(.*?)\<\/wp\:post_id\>/", $arrContentBlock, $arrPostID);
      preg_match_all("/\<wp\:post_type\>(.*?)\<\/wp\:post_type\>/", $arrContentBlock, $arrType);
      preg_match_all("/\<title\>(.*?)\<\/title\>/", $arrContentBlock, $arrTitle);
      preg_match_all("/\<link\>(.*?)\<\/link\>/", $arrContentBlock, $arrLink);
      preg_match_all("/\<pubDate\>(.*?)\<\/pubDate\>/", $arrContentBlock, $arrDate);
      preg_match_all("/\<wp\:post_date\>(.*?)\<\/wp\:post_date\>/", $arrContentBlock, $arrPDate);
      preg_match_all("/\<wp\:status\>(.*?)\<\/wp\:status\>/", $arrContentBlock, $arrStatus);
      preg_match_all("/\<wp\:comment_status\>(.*?)\<\/wp\:comment_status\>/", $arrContentBlock, $arrCStatus);
      preg_match_all("/\<dc\:creator\>\<\!\[CDATA\[(.*?)\]\]\>\<\/dc\:creator\>/", $arrContentBlock, $arrAuthor);
      preg_match_all("/\<category\>\<\!\[CDATA\[(.*?)\]\]\>\<\/category\>/s", $arrContentBlock, $arrCategory);
      preg_match_all("/\<content\:encoded\>\<\!\[CDATA\[(.*?)\]\]\>\<\/content\:encoded\>/s", $arrContentBlock, $arrContent);
      // ** Data assignment ** //
      $intPostID  = $arrPostID[1][0];
      $strType    = $arrType[1][0];
      $strTitle   = $CMS->AddSlashesIFW($arrTitle[1][0]);
      $strSEOTitle = $CMS->MakeSEOTitle($strTitle);
      $strLink    = $arrLink[1][0];
      //$strDate    = date('Y-m-d H:i:s', strtotime($arrDate[1][0]));
      $strDate    = $arrPDate[1][0];
      $strStatus  = $arrStatus[1][0];
      $strCStatus = $arrCStatus[1][0];
      $strAuthor  = $arrAuthor[1][0];
      $strContent = $arrContent[1][0];
      // Category
      $strCategory = $arrCategory[1][0];
      if (!$strCategory) {
        $intAreaID = $intDefaultAreaID;
      } else {
        $intAreaID = $CMS->AR->GetIDFromName($strCategory);
        if (!$intAreaID) {
          $intParentID = $intDefaultAreaID;
          $intAreaID = $CMS->AR->CreateArea($strCategory, 2, 1, 0, 0, $intParentID, 0, 0, 5, "0|asc", "Y", "", 0, "", "", "", "Content", "", "", C_NAV_PRIMARY);
        }
      }
      // Captions
      preg_match_all("/\[caption (.*?)\[\/caption\]/", $strContent, $arrCaptions);
      if (!empty($arrCaptions[1][0])) {
        // Store
        $strOriginalCaption = $arrCaptions[1][0];
        $strCaptionReplace1 = "[caption ".$strOriginalCaption;
        $strCaptionReplace2 = "[/caption]";
        // Assign
        preg_match_all("/id=\"(.*?)\"/",         $strOriginalCaption, $arrCaptionID);
        preg_match_all("/align=\"(.*?)\"/",      $strOriginalCaption, $arrCaptionAlign);
        preg_match_all("/width=\"(.*?)\"/",      $strOriginalCaption, $arrCaptionWidth);
        preg_match_all("/caption=\"(.*?)\"/",    $strOriginalCaption, $arrCaptionText);
        preg_match_all("/\](.*?)\[\/caption\]/", $strOriginalCaption."[/caption]", $arrCaptionCode);
        // Convert
        $intCaptionID    = $arrCaptionID[1][0];
        $strCaptionAlign = $arrCaptionAlign[1][0];
        $intCaptionWidth = $arrCaptionWidth[1][0];
        $strCaptionText  = $arrCaptionText[1][0];
        $strCaptionCode  = $arrCaptionCode[1][0];
        // ID
        if ($intCaptionID) {
          $strCaptionID = " id=\"$intCaptionID\"";
        } else {
          $strCaptionID = "";
        }
        // Style
        if ($strCaptionAlign == "alignleft") {
          $strStyleAlign = "text-align: left;";
        } elseif ($strCaptionAlign == "alignright") {
          $strStyleAlign = "text-align: right;";
        } else {
          $strStyleAlign = "";
        }
        if ($intCaptionWidth) {
          $strStyleWidth = "width: ".$intCaptionWidth."px;";
        } else {
          $strStyleWidth = "";
        }
        $strCaptionStyle = "";
        if ($strStyleAlign) {
          $strCaptionStyle = " style=\"$strStyleAlign";
        }
        if ($strStyleWidth) {
          if ($strCaptionText) {
            $strCaptionStyle .= " ".$strStyleWidth;
          } else {
            $strCaptionStyle = " style=\"$strStyleWidth";
          }
        }
        if ($strCaptionStyle) {
          $strCaptionStyle .= "\"";
        }
        // Text
        if ($strCaptionText) {
          $strCaptionText = "<br /><i>$strCaptionText</i>";
        } else {
          $strCaptionText = "";
        }
        // Build
        $strNewCaption = <<<NewCaptionHTML
<div$strCaptionID$strCaptionStyle>$strCaptionCode$strCaptionText

NewCaptionHTML;
        // Replace
        $strContent = str_replace($strCaptionReplace1, $strNewCaption, $strContent);
        $strContent = str_replace($strCaptionReplace2, "</div>", $strContent);
      }
      // ** WordPress line break nonsense ** //
      $strContent = nl2br($strContent);
      // ** Tidy up ** //
      $strContent = str_replace("</h2><br />", "</h2>", $strContent);
      $strContent = str_replace("<ol><br />", "<ol>", $strContent);
      $strContent = str_replace("</ol><br />", "</ol>", $strContent);
      $strContent = str_replace("<ul><br />", "<ul>", $strContent);
      $strContent = str_replace("</ul><br />", "</ul>", $strContent);
      $strContent = str_replace("</li><br />", "</li>", $strContent);
      $strContent = $CMS->AddSlashesIFW($strContent);
      // ** Validate post data ** //
      if (($strType == "post") && ($strStatus <> "inherit")) {
        if ($strStatus == "publish") {
          $strContStatus = C_CONT_PUBLISHED;
        } elseif ($strStatus == "draft") {
          $strContStatus = C_CONT_DRAFT;
        } elseif ($strStatus == "private") {
          $strContStatus = C_CONT_DRAFT;
        } else {
          $strContStatus = C_CONT_PUBLISHED;
        }
        if ($strCStatus == 'open') {
          $strLocked = 'N';
        } elseif ($strCStatus == 'closed') {
          $strLocked = 'Y';
        } else {
          $strLocked = 'N';
        }
        $intAuthorID = $CMS->US->GetIDFromName($strAuthor);
        if (!$intAuthorID) {
          $strSEOUsername = $CMS->MakeSEOTitle($strAuthor);
          $strPass = md5(mt_rand().time());
          $intAuthorID = $CMS->Query("INSERT INTO {IFW_TBL_USERS}(username, userpass, forename, surname, email, location, occupation, interests, homepage_link, homepage_text, avatar_id, join_date, ip_address, user_groups, seo_username) VALUES ('$strAuthor', '$strPass', '', '', '', '', '', '', '', '', 0, '$dteCurrentDate', '', '$strUserGroups', '$strSEOUsername')", basename(__FILE__), __LINE__);
        }
        if (!$intAreaID) {
          $intAreaID = $intDefaultAreaID;
        }
        $CMS->Query("INSERT INTO {IFW_TBL_CONTENT}(id, author_id, create_date, title, content, content_area_id, last_updated, read_userlist, tags, seo_title, link_url, locked, content_status) VALUES ($intPostID, $intAuthorID, '$strDate', '$strTitle', '$strContent', $intAreaID, '$strDate', '', '', '$strSEOTitle', '', '$strLocked', '$strContStatus')", basename(__FILE__), __LINE__);
        //print("<h2>Title: $strTitle</h2><ul><li>Link: $strLink</li><li>Date: $strDate</li><li>Author: $strAuthor</li></ul>\n");
      }
      // ** Comments ** //
      preg_match_all("/\<wp\:comment\>(.*?)\<\/wp\:comment\>/s", $arrContentBlock, $arrComment);
      foreach ($arrComment[1] as $arrCommentBlock) {
        preg_match_all("/\<wp\:comment_id\>(.*?)\<\/wp\:comment_id\>/s", $arrCommentBlock, $arrCommentID);
        preg_match_all("/\<wp\:comment_author\>\<\!\[CDATA\[(.*?)\]\]\>\<\/wp\:comment_author\>/s", $arrCommentBlock, $arrCommentAuthor);
        preg_match_all("/\<wp\:comment_author_email\>(.*?)\<\/wp\:comment_author_email\>/s", $arrCommentBlock, $arrCommentEmail);
        preg_match_all("/\<wp\:comment_author_url\>(.*?)\<\/wp\:comment_author_url\>/s", $arrCommentBlock, $arrCommentURL);
        preg_match_all("/\<wp\:comment_author_IP\>(.*?)\<\/wp\:comment_author_IP\>/s", $arrCommentBlock, $arrCommentIP);
        preg_match_all("/\<wp\:comment_date\>(.*?)\<\/wp\:comment_date\>/s", $arrCommentBlock, $arrCommentDate);
        preg_match_all("/\<wp\:comment_date_gmt\>(.*?)\<\/wp\:comment_date_gmt\>/s", $arrCommentBlock, $arrCommentDateGMT);
        preg_match_all("/\<wp\:comment_content\>\<\!\[CDATA\[(.*?)\]\]\>\<\/wp\:comment_content\>/s", $arrCommentBlock, $arrCommentContent);
        preg_match_all("/\<wp\:comment_approved\>(.*?)\<\/wp\:comment_approved\>/s", $arrCommentBlock, $arrCommentApproved);
        preg_match_all("/\<wp\:comment_type\>(.*?)\<\/wp\:comment_type\>/s", $arrCommentBlock, $arrCommentType);
        preg_match_all("/\<wp\:comment_parent\>(.*?)\<\/wp\:comment_parent\>/s", $arrCommentBlock, $arrCommentParent);
        preg_match_all("/\<wp\:comment_user_id\>(.*?)\<\/wp\:comment_user_id\>/s", $arrCommentBlock, $arrCommentUserID);
        // ** Data assignment ** //
        $intCommentID       = $arrCommentID[1][0];
        $strCommentAuthor   = $arrCommentAuthor[1][0];
        $strCommentEmail    = $arrCommentEmail[1][0];
        $strCommentURL      = $arrCommentURL[1][0];
        if ($strCommentURL <> "http://") {
          $strCommentURL = $CMS->AutoLink($strCommentURL);
        } else {
          $strCommentURL = "";
        }
        $strCommentIP       = $arrCommentIP[1][0];
        $strCommentDate     = $arrCommentDate[1][0];
        $strCommentDateGMT  = $arrCommentDateGMT[1][0];
        $strCommentContent  = $arrCommentContent[1][0];
        $strCommentContent  = nl2br($strCommentContent);
        $strCommentApproved = $arrCommentApproved[1][0];
        if ($strCommentApproved == "1") {
          $strCommentStatus = "Approved";
        } else {
          $strCommentStatus = "Pending";
        }
        $strCommentType     = $arrCommentType[1][0];
        $intCommentParent   = $arrCommentParent[1][0];
        $intCommentUserID   = $arrCommentUserID[1][0];
        // ** Build Query: Create form recipient ** //
        $strQuery = sprintf("INSERT INTO {IFW_TBL_COMMENTS}(id, story_id, comment_count, content, create_date, edit_date, author_id, ip_address, comment_status, guest_name, guest_email, guest_url) VALUES(%s, %s, 0, '%s', '%s', '0000-00-00 00:00:00', %s, '%s', '%s', '%s', '%s', '%s')",
          $intCommentID,
          $intPostID,
          mysql_real_escape_string($strCommentContent),
          mysql_real_escape_string($strCommentDate),
          $intCommentUserID,
          mysql_real_escape_string($strCommentIP),
          mysql_real_escape_string($strCommentStatus),
          mysql_real_escape_string($strCommentAuthor),
          mysql_real_escape_string($strCommentEmail),
          mysql_real_escape_string($strCommentURL)
        );
        // ** Process query ** //
        $CMS->Query($strQuery, basename(__FILE__), __LINE__);
        // ** Update comment count ** //
        $CMS->Query("UPDATE {IFW_TBL_CONTENT} SET comment_count = comment_count + 1 WHERE id = $intPostID", basename(__FILE__), __LINE__);
      }
      // *** END OF IMPORT *** //
    }
    
    // Rebuild area array
    $CMS->AT->RebuildAreaArray("");

    /*

    // Get all WP uploads
    $arrUploads = $CMS->ResultQuery("SELECT ID, post_author, post_date, post_content, post_title, post_modified, post_status, comment_status, guid FROM {IFW_TBL_WP_POSTS} WHERE post_status = 'inherit' ORDER BY ID ASC", basename(__FILE__), __LINE__);
    for ($i=0; $i<count($arrUploads); $i++) {
      $intID       = $arrUploads[$i]['ID'];
      $intAuthorID = $arrUploads[$i]['post_author'];
      $dteDate     = $arrUploads[$i]['post_date'];
      $strContent  = $arrUploads[$i]['post_content'];
      $strContent  = str_replace("\r", "<br />\r\n", $strContent);
      $strContent  = $CMS->AddSlashesIFW($strContent);
      $strLocation = $arrUploads[$i]['guid'];
      $strTitle    = $CMS->AddSlashesIFW($arrUploads[$i]['post_title']);
      $dteUpdated  = $arrUploads[$i]['post_modified'];
      $strPStatus  = $arrUploads[$i]['post_status'];
      if ($strPStatus == 'publish') {
        $strContStatus = C_CONT_PUBLISHED;
      } elseif ($strPStatus == 'draft') {
        $strContStatus = C_CONT_DRAFT;
      } elseif ($strPStatus == 'private') {
        $strContStatus = C_CONT_DRAFT;
      } else {
        $strContStatus = C_CONT_PUBLISHED;
      }
      $strCStatus  = $arrContent[$i]['comment_status'];
      if ($strCStatus == 'open') {
        $strLocked = 'N';
      } elseif ($strCStatus == 'closed') {
        $strLocked = 'Y';
      } else {
        $strLocked = 'N';
      }
      $strSEOTitle = $CMS->MakeSEOTitle($strTitle);
      $CMS->Query("INSERT INTO {IFW_TBL_UPLOADS}(id, title, description, location, file_area_id, author_id, user_groups, create_date, edit_date, hits, is_avatar, is_siteimage, delete_flag, thumb_small, thumb_medium, thumb_large, upload_size, seo_title, article_id) VALUES ($intID, '$strTitle', '$strContent', '$strLocation', 0, $intAuthorID, '', '$dteDate', '$dteDate', 0, 'N', 'Y', 'N', '', '', '', '', '$strSEOTitle', 0)", basename(__FILE__), __LINE__);
    }
    
    // Get all WP comments
    $arrComments = $CMS->ResultQuery("SELECT * FROM {IFW_TBL_WP_COMMENTS} WHERE comment_approved <> 'spam' AND comment_type = '' ORDER BY comment_ID ASC", basename(__FILE__), __LINE__);
    for ($i=0; $i<count($arrComments); $i++) {
      $intID        = $arrComments[$i]['comment_ID'];
      $intArticleID = $arrComments[$i]['comment_post_ID'];
      $strAuthor    = $arrComments[$i]['comment_author'];
      $strAuthor    = $CMS->DoEntities($strAuthor);
      $strAuthor    = $CMS->FilterAlphanumeric($strAuthor, C_CHARS_USERNAME);
      $strAuthor    = $CMS->AddSlashesIFW($strAuthor);
      $strEmail     = $arrComments[$i]['comment_author_email'];
      $strURL       = $arrComments[$i]['comment_author_url'];
      if ($strURL <> "http://") {
        $strURL = $CMS->AutoLink($strURL);
      } else {
        $strURL = "";
      }
      $strIP        = $arrComments[$i]['comment_author_IP'];
      $dteDate      = $arrComments[$i]['comment_date'];
      $strContent   = $arrComments[$i]['comment_content'];
      $strContent   = str_replace("\r", "<br />\r\n", $strContent);
      $strContent   = $CMS->AddSlashesIFW($strContent);
      $intUserID    = $arrComments[$i]['user_id'];
      if (!$intUserID) {
        $intUserID = $CMS->US->GetIDFromNameAndEmail($strAuthor, $strEmail);
        if (!$intUserID) {
          $strPass = md5($strAuthor.time());
          $strSEOUsername = $CMS->MakeSEOTitle($strAuthor);
          $intUserID = $CMS->Query("INSERT INTO {IFW_TBL_USERS}(username, userpass, forename, surname, email, location, occupation, interests, homepage_link, homepage_text, avatar_id, join_date, ip_address, user_groups, seo_username) VALUES ('$strAuthor', '$strPass', '', '', '$strEmail', '', '', '', '$strURL', '$strURL', 0, '$dteDate', '', '$strUserGroups', '$strSEOUsername')", basename(__FILE__), __LINE__);
        }
      }
      $intCommentID = $CMS->Query("INSERT INTO {IFW_TBL_COMMENTS}(story_id, comment_count, content, create_date, edit_date, author_id, upload_id, ip_address) VALUES($intArticleID, 0, '$strContent', '$dteDate', '0000-00-00 00:00:00', $intUserID, 0, '$strIP')", basename(__FILE__), __LINE__);
    }
    */
  }
  
  $CMS->AP->Display($strHTML);
?>