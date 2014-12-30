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

class Theme extends Helper {
    
    // ** Get theme file location ** //
    function GetPath($strThemeDir, $strLayoutStyle, $strWhich) {
      global $CMS;
      if (!$strThemeDir) {
        $strThemeDir = $CMS->SYS->GetSysPref(C_PREF_DEFAULT_THEME); //"default";
      }
      if ($strWhich == C_TH_STYLESHEET) {
        $strSysPath = URL_SYS_THEMES;
      } else {
        $strSysPath = ABS_SYS_THEMES;
      }
      if ($strLayoutStyle) {
        $strFilePath = $strLayoutStyle."/".$strWhich;
      } else {
        $strFilePath = $strWhich;
      }
      $strPathToCheck = ABS_SYS_THEMES.$strThemeDir."/".$strFilePath;
      $strPath = $strSysPath.$strThemeDir."/".$strFilePath;
      if (file_exists($strPathToCheck) == true) {
        return $strPath;
      } else {
        return "";
      }
    }
    
    // ** THEME FILE LOCATION ** //
    var $strCurrentLocation;
    
    function SetCurrentLocation($strLocation) {
        $this->strCurrentLocation = $strLocation;
    }
    
    function GetCurrentLocation() {
        return $this->strCurrentLocation;
    }
    
    function IsIndex() {
        return $this->strCurrentLocation == C_TL_INDEX ? true : false;
    }
    
    function IsPage() {
        return $this->strCurrentLocation == C_TL_PAGE ? true : false;
    }
    
    function IsProfile() {
        return $this->strCurrentLocation == C_TL_PROFILE ? true : false;
    }
    
    function IsDefault() {
        return $this->strCurrentLocation == C_TL_DEFAULT ? true : false;
    }
    
    // ** SYSTEM: BUILT-IN STUFF ** //
    function GetSysAdminCPLink() {
      global $CMS;
      $CMS->RES->ValidateLoggedIn();
      return $CMS->RES->IsError() ? "" : FN_ADM_INDEX;
    }
    
    function GetSysUserCPLink() {
      return "";
    }
    
    function GetSysLoginLink() {
      global $CMS;
      $CMS->RES->ValidateLoggedIn();
      return $CMS->RES->IsError() ? FN_LOGIN : "";
    }
    
    function GetSysLogoutLink() {
      global $CMS;
      $CMS->RES->ValidateLoggedIn();
      return $CMS->RES->IsError() ? "" : FN_LOGOUT;
    }
    
    function GetSysRegisterLink() {
      global $CMS;
      if ($this->IsLoggedIn()) {
        return "";
      } elseif ($CMS->SYS->GetSysPref(C_PREF_USER_REGISTRATION) == "1") {
        return FN_REGISTER;
      } else {
        return "";
      }
    }
    
    function GetSysTagMapLink() {
      return FN_TAGMAP;
    }
    
    function GetSysSearchForm() {
      global $CMS;
      if (SVR_LOCATION != FN_SEARCH) {
        return $CMS->AC->SearchForm();
      }
    }
    
    function GetSysQueryTime() {
      global $strExecutionTime; // Set in header.php
      if ($strExecutionTime) {
        $strQueryTimeData = <<<ExecTime
<div id="majQueryTimeData">
<p>Query Execution Time</p>
<ol>
$strExecutionTime
</ol>
</div>
ExecTime;
      } else {
        $strQueryTimeData = "";
      }
      return $strQueryTimeData;
    }
    
    // ** SYSTEM: NAVIGATION BAR ** //
    var $intNavigationSelectedItem;
    var $intNavigationCount;
    var $arrNavigationItems;
    var $intNavigationIndex;
    function SetNavigationSelectedItem($intSelectedItem) {
      $this->intNavigationSelectedItem = $intSelectedItem;
    }
    function GetNavigationSelectedItem() {
      return $this->intNavigationSelectedItem;
    }
    function SetNavigationCount($intCount) {
      $this->intNavigationCount = $intCount;
    }
    function GetNavigationCount() {
      return $this->intNavigationCount;
    }
    function SetNavigationItems($arrItems) {
      $this->arrNavigationItems = $arrItems;
    }
    function StartNavigationItems() {
      $this->intNavigationIndex = -1; // increments to 0 for first item
    }
    function NextNavigationItem() {
      $this->intNavigationIndex++;
      return $this->intNavigationIndex;
    }
    // ** Navigation item properties ** //
    function GetNavigationID() {
      return $this->arrNavigationItems[$this->intNavigationIndex]['id'];
    }
    function GetNavigationIndent() {
      return empty($this->arrNavigationItems[$this->intNavigationIndex]['indent']) ? "" : $this->arrNavigationItems[$this->intNavigationIndex]['indent'];
    }
    function GetNavigationName() {
      $strIndent = $this->GetNavigationIndent();
      return $strIndent.$this->arrNavigationItems[$this->intNavigationIndex]['name'];
    }
    function GetNavigationSEOName() {
      return $this->arrNavigationItems[$this->intNavigationIndex]['seo_name'];
    }
    function GetNavigationAreaURL() {
      return $this->arrNavigationItems[$this->intNavigationIndex]['area_url'];
    }
    function GetNavigationLink() {
      global $CMS;
      if ($this->GetNavigationAreaURL()) {
        $strNavLink = $this->GetNavigationAreaURL();
      } else {
        $CMS->PL->SetTitle($this->GetNavigationSEOName());
        $strNavLink = $CMS->PL->ViewArea($this->GetNavigationID());
      }
      return $strNavLink;
    }
    // ** HEADER ** //
    var $strPageTitle;
    var $strSiteTitle;
    var $strSiteDesc;
    var $strMetaGenerator;
    var $strMetaDesc;
    var $strMetaKeywords;
    var $strSiteFeed;
    var $strAreaFeed;
    var $strArticleFeed;
    var $strCoreStyles;
    var $strAreaStyles;
    var $strScripts;
    var $strCustomHeadTags;
    function SetHeaderPageTitle($strPageTitle) {
      $this->strPageTitle = $strPageTitle;
    }
    function GetHeaderPageTitle() {
      return $this->strPageTitle;
    }
    function SetHeaderSiteTitle($strSiteTitle) {
      $this->strSiteTitle = $strSiteTitle;
    }
    function GetHeaderSiteTitle() {
      return $this->strSiteTitle;
    }
    function SetHeaderSiteDesc($strSiteDesc) {
      $this->strSiteDesc = $strSiteDesc;
    }
    function GetHeaderSiteDesc() {
      return $this->strSiteDesc;
    }
    function SetHeaderMetaGenerator($strMetaGenerator) {
      $this->strMetaGenerator = $strMetaGenerator;
    }
    function GetHeaderMetaGenerator() {
      return $this->strMetaGenerator;
    }
    function SetHeaderMetaDesc($strMetaDesc) {
      $this->strMetaDesc = $strMetaDesc;
    }
    function GetHeaderMetaDesc() {
      return $this->strMetaDesc;
    }
    function SetHeaderMetaKeywords($strMetaKeywords) {
      $this->strMetaKeywords = $strMetaKeywords;
    }
    function GetHeaderMetaKeywords() {
      return $this->strMetaKeywords;
    }
    function SetHeaderSiteFeed($strSiteFeed) {
      $this->strSiteFeed = $strSiteFeed;
    }
    function GetHeaderSiteFeed() {
      return $this->strSiteFeed;
    }
    function SetHeaderAreaFeed($strAreaFeed) {
      $this->strAreaFeed = $strAreaFeed;
    }
    function GetHeaderAreaFeed() {
      return $this->strAreaFeed;
    }
    function SetHeaderArticleFeed($strArticleFeed) {
      $this->strArticleFeed = $strArticleFeed;
    }
    function GetHeaderArticleFeed() {
      return $this->strArticleFeed;
    }
    function SetHeaderCoreStyles($strCoreStyles) {
      $this->strCoreStyles = $strCoreStyles;
    }
    function GetHeaderCoreStyles() {
      return $this->strCoreStyles;
    }
    function SetHeaderAreaStyles($strAreaStyles) {
      $this->strAreaStyles = $strAreaStyles;
    }
    function GetHeaderAreaStyles() {
      return $this->strAreaStyles;
    }
    function SetHeaderScripts($strScripts) {
      $this->strScripts = $strScripts;
    }
    function GetHeaderScripts() {
      return $this->strScripts;
    }
    function SetHeaderCustomTags($strCustomHeadTags) {
      $this->strCustomHeadTags = $strCustomHeadTags;
    }
    function GetHeaderCustomTags() {
      return $this->strCustomHeadTags;
    }
    // ** SYS: WRAPPER ** //
    var $strSysWrapperStart;
    var $strSysWrapperEnd;
    function SetSysWrapperStart($strValue) {
      $this->strSysWrapperStart = $strValue;
    }
    function GetSysWrapperStart() {
      return $this->strSysWrapperStart;
    }
    function SetSysWrapperEnd($strValue) {
      $this->strSysWrapperEnd = $strValue;
    }
    function GetSysWrapperEnd() {
      return $this->strSysWrapperEnd;
    }
    // ** SYS: BREADCRUMBS ** //
    var $strSysBreadcrumbs;
    function SetSysBreadcrumbs($strValue) {
      $this->strSysBreadcrumbs = $strValue;
    }
    function GetSysBreadcrumbs() {
      return $this->strSysBreadcrumbs;
    }
    // ** SYS: WRITE LINK ** //
    var $strSysWriteLink;
    function SetSysWriteLink($strValue) {
      $this->strSysWriteLink = $strValue;
    }
    function GetSysWriteLink() {
      return $this->strSysWriteLink;
    }
    // ** AREA: SUBAREA ITEMS ** //
    var $intSubareaCount;
    var $arrSubareaItems;
    var $intSubareaIndex;
    function SetSubareaCount($intCount) {
      $this->intSubareaCount = $intCount;
    }
    function GetSubareaCount() {
      return $this->intSubareaCount;
    }
    function SetSubareaItems($arrItems) {
      $this->arrSubareaItems = $arrItems;
    }
    function StartSubareaItems() {
      $this->intSubareaIndex = -1; // increments to 0 for first item
    }
    function NextSubareaItem() {
      $this->intSubareaIndex++;
      return $this->intSubareaIndex;
    }
    // ** Subarea properties ** //
    function GetSubareaID() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['id'];
    }
    function GetSubareaName() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['name'];
    }
    function GetSubareaSEOName() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['seo_name'];
    }
    function GetSubareaDesc() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['area_description'];
    }
    function GetSubareaURL() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['area_url'];
    }
    function GetSubareaLinkURL() {
      global $CMS;
      $strAreaURL = $this->GetSubareaURL();
      if ($strAreaURL) {
        return $strAreaURL;
      } else {
        $CMS->PL->SetTitle($this->GetSubareaSEOName());
        $strAreaURL = $CMS->PL->ViewArea($this->GetSubareaID());
        return $strAreaURL;
      }
    }
    function GetSubareaGraphicID() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['area_graphic_id'];
    }
    function GetSubareaItemClassList() {
      $intCurrentID = $this->GetSubareaID();
      $strClassList = "subarea-item";
      if ($this->intSubareaIndex == 0) {
        $strClassList .= " first";
      } elseif ($this->intSubareaIndex == $this->GetSubareaCount() - 1) {
        $strClassList .= " last";
      }
      if (($this->intSubareaIndex % 2) == 0) {
        $strClassList .= " even";
      } else {
        $strClassList .= " odd";
      }
      return $strClassList;
    }
    // ** AREA: CONTENT ITEMS ** //
    var $intContentCount;
    var $arrContentItems;
    var $intContentIndex;
    var $arrContentAttachments;
    function SetContentCount($intCount) {
      $this->intContentCount = $intCount;
    }
    function GetContentCount() {
      return $this->intContentCount;
    }
    function SetContentItems($arrItems) {
      $this->arrContentItems = $arrItems;
    }
    function StartContentItems() {
      $this->intContentIndex = -1; // increments to 0 for first item
    }
    function NextContentItem() {
      global $CMS; // Do not remove!
      // Go to the next index in the array
      $this->intContentIndex++;
      /* ********************************************** */
      /* *              Article - Attachments           */
      /* * This is used to display a file download link */
      /* * and thumbnails, if they exist.               */
      /* ********************************************** */
      $CMS->TH->GetContentAttachments();
      // Return the new index
      return $this->intContentIndex;
    }
    // ** Content item properties ** //
    function GetContentID() {
      return $this->arrContentItems[$this->intContentIndex]['id'];
    }
    function GetContentAreaID() {
      return $this->arrContentItems[$this->intContentIndex]['content_area_id'];
    }
    function GetContentAreaName() {
      return $this->arrContentItems[$this->intContentIndex]['area_name'];
    }
    function GetContentAreaGraphicID() {
      return $this->arrSubareaItems[$this->intSubareaIndex]['area_graphic_id'];
    }
    function GetContentAuthorID() {
      return $this->arrContentItems[$this->intContentIndex]['author_id'];
    }
    function GetContentAuthorName() {
      return $this->arrContentItems[$this->intContentIndex]['username'];
    }
    function GetContentSEOUsername() {
      return $this->arrContentItems[$this->intContentIndex]['seo_username'];
    }
    function GetContentAuthorProfileLink() {
      global $CMS;
      $strProfileHTML = "";
      if ($this->GetContentAuthorID()) {
        $strProfileURL = $CMS->PL->ViewUser($this->GetContentAuthorID());
        $strProfileHTML = "<a href=\"$strProfileURL\">".$this->GetContentAuthorName()."'s profile</a>";
      }
      return $strProfileHTML;
    }
    function GetContentAuthorAvatarID() {
      return $this->arrContentItems[$this->intContentIndex]['avatar_id'];
    }
    function GetContentTitle() {
      return $this->arrContentItems[$this->intContentIndex]['title'];
    }
    function GetContentSEOTitle() {
      return $this->arrContentItems[$this->intContentIndex]['seo_title'];
    }
    function GetContentCreateDate() {
      return $this->arrContentItems[$this->intContentIndex]['create_date'];
    }
    function GetContentDeleted() {
      return $this->arrContentItems[$this->intContentIndex]['content_status'] == C_CONT_DELETED ? "Y" : "N";
    }
    function GetContentLocked() {
      return $this->arrContentItems[$this->intContentIndex]['locked'];
    }
    function GetContentReadUserlist() {
      return $this->arrContentItems[$this->intContentIndex]['read_userlist'];
    }
    function GetContentHits() {
      return $this->arrContentItems[$this->intContentIndex]['hits'];
    }
    function GetContentLinkURL() {
      global $CMS;
      return $CMS->PL->ViewArticle($this->GetContentID());
    }
    function GetContentURL() {
      return $this->arrContentItems[$this->intContentIndex]['link_url'];
    }
    
    /**
     * Gets the content body
     * @return string
     */
    function GetContentBody() {
        
        global $CMS;
        $strContBody = $this->arrContentItems[$this->intContentIndex]['content'];
        $strReadMoreEditor = $CMS->AC->ReadMoreEditor();
        $strReadMorePublic = $CMS->AC->ReadMorePublic();
        if ($this->IsIndex()) {
            if (strpos($strContBody, $strReadMorePublic) !== false) {
                $strDataToReplace = $strReadMorePublic;
                $doReplace = true;
            } elseif (strpos($strContBody, $strReadMoreEditor) !== false) {
                $strDataToReplace = $strReadMoreEditor;
                $doReplace = true;
            } else {
                $doReplace = false;
            }
            if ($doReplace) {
                $arrContBody = explode($strDataToReplace, $strContBody);
                $strContLink = $CMS->PL->ViewArticle($this->GetContentID());
                $strContBody = $arrContBody[0].
                    '<span class="read-more"><a href="'.$strContLink.'">Read more...</a></span>';
                if (strpos($strContBody, '<p><span class="read-more">') !== false) {
                    $strContBody .= '</p>';
                }
            }
        } elseif ($this->IsPage()) {
            // Fix for unusual circumstances where the code isn't properly replaced
            if (strpos($strContBody, $strReadMoreEditor) !== false) {
                $strContBody = str_replace($strReadMoreEditor, $strReadMorePublic, $strContBody);
            }
        }
        return $strContBody;
        
    }
    
    /**
     * Gets an article excerpt, if it is set.
     * If not, it'll get a portion of the content body.
     * @param $intLength
     * @param $strSuffix
     * @return string
     */
    function GetContentExcerpt($intLength, $strSuffix) {
        
        $strCustomExcerpt = $this->arrContentItems[$this->intContentIndex]['article_excerpt'];
        
        if (empty($strCustomExcerpt)) {
            $strContBody = $this->arrContentItems[$this->intContentIndex]['content'];
            $strContBody = str_replace("\r", " ", $strContBody);
            $strContBody = str_replace("\n", " ", $strContBody);
            $strContBody = str_replace("  ", " ", $strContBody);
            $strContBody = strip_tags($strContBody);
            $strContBody = substr($strContBody, 0, $intLength).$strSuffix;
            $strCustomExcerpt = $strContBody;
        }
        
        return $strCustomExcerpt;
        
    }
    
    function GetContentCommentCount() {
      return $this->arrContentItems[$this->intContentIndex]['comment_count'];
    }
    // ** Attachments ** //
    function GetContentAttachments() {
      global $CMS;
      if (!empty($this->arrContentItems[$this->intContentIndex])) {
        $intContentID = $this->GetContentID();
        $strTitle     = $this->GetContentTitle();
        $this->arrContentAttachments[$this->intContentIndex] = $CMS->FL->GetAttachedFiles($intContentID);
      }
    }
    function GetContentThumbSmall() {
      if (is_array($this->arrContentAttachments[$this->intContentIndex])) {
        $strThumbSmall = $this->ArticleThumbnail($this->arrContentAttachments[$this->intContentIndex], "s", $this->GetContentTitle());
      } else {
        $strThumbSmall = "";
      }
      return $strThumbSmall;
    }
    function GetContentThumbMedium() {
      if (is_array($this->arrContentAttachments[$this->intContentIndex])) {
        $strThumbMedium = $this->ArticleThumbnail($this->arrContentAttachments[$this->intContentIndex], "m", $this->GetContentTitle());
      } else {
        $strThumbMedium = "";
      }
      return $strThumbMedium;
    }
    function GetContentThumbLarge() {
      if (is_array($this->arrContentAttachments[$this->intContentIndex])) {
        $strThumbLarge = $this->ArticleThumbnail($this->arrContentAttachments[$this->intContentIndex], "l", $this->GetContentTitle());
      } else {
        $strThumbLarge = "";
      }
      return $strThumbLarge;
    }
    function GetContentDownloadLink() {
      global $CMS;
      if (is_array($this->arrContentAttachments[$this->intContentIndex])) {
        $intFileID   = $this->arrContentAttachments[$this->intContentIndex][0]['id'];
        $strLocation = $this->arrContentAttachments[$this->intContentIndex][0]['location'];
        $strFileType = strtoupper($this->GetExtensionFromPath($strLocation));
        $strFileSize = $this->MakeFileSize($this->arrContentAttachments[$this->intContentIndex][0]['upload_size']);
        $strFileLink = FN_FILE_DOWNLOAD."?id=$intFileID";
        $this->SetContentDownloadSize($strFileSize);
        $this->SetContentDownloadType($strFileType);
        //$strDownloadLink = "<span class=\"attach-download\"><a href=\"$strFileLink\">Download attachment</a></span> <span class=\"attach-desc\">($strFileSize $strFileType)</span>";
        //$strDownloadLink = $CMS->ARCO->ArticleAttachments($this->GetContentID(), $this->arrContentAttachments[$this->intContentIndex]);
      } else {
        $strFileLink = "";
      }
      return $strFileLink;
    }
    function GetContentDownloadHits() {
      global $CMS;
      if (is_array($this->arrContentAttachments[$this->intContentIndex])) {
        $intHits = $this->arrContentAttachments[$this->intContentIndex][0]['hits'];
      } else {
        $intHits = "";
      }
      return $intHits;
    }
    // ** Thumbnails ** //
    function ArticleThumbnail($arrFiles, $strSize, $strTitle) {
      global $CMS;
      $strThumbnail = "";
      $strThumbFile = "";
      switch ($strSize) {
        case "s":
          $strThumbFile = $arrFiles[0]['thumb_small'];
          break;
        case "m":
          $strThumbFile = $arrFiles[0]['thumb_medium'];
          break;
        case "l":
          $strThumbFile = $arrFiles[0]['thumb_large'];
          break;
        case "o":
          $strThumbFile = $arrFiles[0]['location'];
          break;
      }
      if ($strThumbFile) {
        //$strThumbFile = URL_ROOT.$strThumbFile;
        //$strThumbnail = "<img src=\"$strThumbFile\" class=\"fvThumb fvThumbSmall\" alt=\"$strTitle\" />";
        $intFileID    = $arrFiles[0]['id'];
        $strThumbnail = "<img src=\"".FN_FILE_DOWNLOAD."?id=$intFileID&amp;s=$strSize\" class=\"fvThumb fvThumbSmall\" alt=\"$strTitle\" title=\"$strTitle\" />";
      }
      return $strThumbnail;
    }
    // ** Custom properties ** //
    var $strContentNextLink;
    var $strContentPrevLink;
    var $strContentDownloadSize;
    var $strContentDownloadType;
    var $strContentTextTags;
    var $strContentLinkedTags;
    var $strThemeData;
    function GetContentAreaLink() {
      global $CMS;
      return $CMS->PL->ViewArea($this->GetContentAreaID());
    }
    function SetContentNextLink($strValue) {
      $this->strContentNextLink = $strValue;
    }
    function GetContentNextLink() {
      return $this->strContentNextLink;
    }
    function SetContentPrevLink($strValue) {
      $this->strContentPrevLink = $strValue;
    }
    function GetContentPrevLink() {
      return $this->strContentPrevLink;
    }
    function SetContentDownloadSize($strValue) {
      $this->strContentDownloadSize = $strValue;
    }
    function GetContentDownloadSize() {
      return $this->strContentDownloadSize;
    }
    function SetContentDownloadType($strValue) {
      $this->strContentDownloadType = $strValue;
    }
    function GetContentDownloadType() {
      return $this->strContentDownloadType;
    }
    function SetContentTextTags($strValue) {
      $this->strContentTextTags = $strValue;
    }
    function GetContentTextTags() {
      return $this->strContentTextTags;
    }
    function SetContentLinkedTags($strValue) {
      $this->strContentLinkedTags = $strValue;
    }
    function GetContentLinkedTags() {
      return $this->strContentLinkedTags;
    }
    // ** Related Content - Assignments ** //
    var $intRelatedContentCount;
    var $arrRelatedContentItems;
    var $intRelatedContentIndex;
    function SetRelatedContentCount($intCount) {
      $this->intRelatedContentCount = $intCount;
    }
    function GetRelatedContentCount() {
      return $this->intRelatedContentCount;
    }
    function SetRelatedContentItems($arrItems) {
      $this->arrRelatedContentItems = $arrItems;
    }
    function StartRelatedContentItems() {
      $this->intRelatedContentIndex = -1; // increments to 0 for first item
    }
    function NextRelatedContentItem() {
      $this->intRelatedContentIndex++;
      return $this->intRelatedContentIndex;
    }
    // ** Related Content - Properties ** //
    function GetRelatedContentID() {
      return $this->arrRelatedContentItems[$this->intRelatedContentIndex]['id'];
    }
    function GetRelatedContentWeight() {
      return $this->arrRelatedContentItems[$this->intRelatedContentIndex]['tag_weight'];
    }
    function GetRelatedContentTitle() {
      return $this->arrRelatedContentItems[$this->intRelatedContentIndex]['title'];
    }
    function GetRelatedContentSEOTitle() {
      return $this->arrRelatedContentItems[$this->intRelatedContentIndex]['seo_title'];
    }
    function GetRelatedContentLinkURL() {
      global $CMS;
      return $CMS->PL->ViewArticle($this->GetRelatedContentID());
    }
    // ** Related Content - Builder ** //
    function GetRelatedContent($strTagList, $intContentID) {
      global $CMS;
      $arrSimilar = "";
      $arrRelatedContent = "";
      if ($strTagList) {
        $arrSimilar = $CMS->ARCO->BuildSimilarArticles($strTagList, $intContentID);
      }
      if (is_array($arrSimilar)) {
        arsort($arrSimilar);
        $intSimilarItemCount = 10; //$CMS->SYS->GetSysPref(C_PREF_SIMILAR_ITEM_COUNT);
        $intItemCounter = 0;
        foreach ($arrSimilar as $intID => $intWeight) {
          if ($intItemCounter == $intSimilarItemCount) {
            break;
          }
          $arrTitle = $this->ResultQuery("SELECT title, seo_title FROM {IFW_TBL_CONTENT} WHERE id = $intID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
          $arrRelatedContent[$intItemCounter]['id']         = $intID;
          $arrRelatedContent[$intItemCounter]['tag_weight'] = $intWeight;
          $arrRelatedContent[$intItemCounter]['title']      = $arrTitle[0]['title'];
          $arrRelatedContent[$intItemCounter]['seo_title']  = $arrTitle[0]['seo_title'];
          $intItemCounter++;
        }
      }
      return $arrRelatedContent;
    }
    // ** Comments - Assignments ** //
    var $intCommentCount;
    var $arrCommentItems;
    var $intCommentIndex;
    function SetCommentCount($intCount) {
      $this->intCommentCount = $intCount;
    }
    function GetCommentCount() {
      return $this->intCommentCount;
    }
    function GetCommentIndex() {
      return $this->intCommentIndex;
    }
    function GetCommentNumber() {
      return $this->intCommentIndex + 1;
    }
    function SetCommentItems($arrItems) {
      $this->arrCommentItems = $arrItems;
    }
    function StartCommentItems() {
      $this->intCommentIndex = -1; // increments to 0 for first item
    }
    function NextCommentItem() {
      $this->intCommentIndex++;
      return $this->intCommentIndex;
    }
    // ** Comments - Properties ** //
    function GetCommentID() {
      return $this->arrCommentItems[$this->intCommentIndex]['id'];
    }
    function GetCommentArticleID() {
      return $this->arrCommentItems[$this->intCommentIndex]['article_id'];
    }
    function GetCommentAuthor() {
      $strUser = $this->arrCommentItems[$this->intCommentIndex]['username'];
      if (!$strUser) {
        $strUser = $this->arrCommentItems[$this->intCommentIndex]['guest_name'];
      }
      return $strUser;
    }
    function GetCommentAuthorID() {
      return $this->arrCommentItems[$this->intCommentIndex]['author_id'];
    }
    function GetCommentAuthorProfileLink() {
      global $CMS;
      $strProfileHTML = "";
      if ($this->GetCommentAuthorID()) {
        $strProfileURL = $CMS->PL->ViewUser($this->GetCommentAuthorID());
        $strProfileHTML = "<a href=\"$strProfileURL\">".$this->GetCommentAuthor()."'s profile</a>";
      }
      return $strProfileHTML;
    }
    function GetCommentAvatarID() {
      return $this->arrCommentItems[$this->intCommentIndex]['avatar_id'];
    }
    function GetCommentBody() {
      return $this->arrCommentItems[$this->intCommentIndex]['content'];
    }
    function GetCommentCreateDate() {
      return $this->arrCommentItems[$this->intCommentIndex]['create_date'];
    }
    function GetCommentEditDate() {
      $dteEditedRaw = $this->GetCommentEditDateRaw();
      if ($dteEditedRaw == "0000-00-00 00:00:00") {
        return "";
      } else {
        return $this->arrCommentItems[$this->intCommentIndex]['edit_date'];
      }
    }
    function GetCommentEditDateRaw() {
      return $this->arrCommentItems[$this->intCommentIndex]['edit_date_raw'];
    }
    function GetCommentHomepageLink() {
      $strLink = $this->arrCommentItems[$this->intCommentIndex]['homepage_link'];
      if (!$strLink) {
        $strLink = $this->arrCommentItems[$this->intCommentIndex]['guest_url'];
      }
      return $strLink;
    }
    function GetCommentHomepageText() {
      return $this->arrCommentItems[$this->intCommentIndex]['homepage_text'];
    }
    function GetCommentAuthorHomepageLink() {
      global $CMS;
      $strHomePageLink = $this->GetCommentHomepageLink();
      $strHomePageText = $this->GetCommentHomepageText();
      if ($strHomePageLink) {
        $strNoFollow = $CMS->SYS->GetSysPref(C_PREF_COMMENT_USE_NOFOLLOW);
        if ($strNoFollow == "1") {
          //$intCommentCount = $CMS->COM->CountUserComments($this->GetCommentAuthorID());
          $intCommentCount = $this->GetCommentAuthorQuota();
          $intNoFollowLimit = $CMS->SYS->GetSysPref(C_PREF_COMMENT_NOFOLLOW_LIMIT);
          if ($intCommentCount >= $intNoFollowLimit) {
            $blnUseNoFollow = false;
          } else {
            $blnUseNoFollow = true;
          }
        } else {
          $blnUseNoFollow = false;
        }
        $strNoFollow = $blnUseNoFollow ? " rel=\"nofollow\"" : "";
        if (!$strHomePageText) {
          $strHomePageText = $this->GetCommentAuthor()."'s home page";
        }
        $strAuthorLink = "<a href=\"$strHomePageLink\"".$strNoFollow.">$strHomePageText</a>";
      } else {
        $strAuthorLink = "";
      }
      return $strAuthorLink;
    }
    function GetCommentIP() {
      global $CMS;
      $CMS->RES->Admin();
      if (!$CMS->RES->IsError()) {
        return $this->arrCommentItems[$this->intCommentIndex]['ip_address'];
      } else {
        return "";
      }
    }
    function GetCommentAuthorQuota() {
      global $CMS;
      $intQuota = "";
      if ($this->arrCommentItems[$this->intCommentIndex]['email']) {
        $strEmail = $this->arrCommentItems[$this->intCommentIndex]['email'];
      } elseif ($this->arrCommentItems[$this->intCommentIndex]['guest_email']) {
        $strEmail = $this->arrCommentItems[$this->intCommentIndex]['guest_email'];
      } else {
        $strEmail = "";
      }
      if ($strEmail) {
        $arrQuota = $CMS->UST->Get($strEmail);
        $intQuota = $arrQuota['comment_count'];
      }
      return $intQuota;
    }
    function GetCommentRating() {
      return $this->arrCommentItems[$this->intCommentIndex]['rating_value'];
    }
    function GetCommentPermalink() {
      global $CMS;
      $strLinkURL = $CMS->PL->ViewArticle($this->GetCommentArticleID());
      $strPermalink = $strLinkURL."#c".$this->GetCommentID();
      return $strPermalink;
    }
    // ** Comments - Links ** //
    function GetCommentEditLink() {
      global $CMS;
      $strEdit = "";
      $intAreaID    = $this->GetContentAreaID();
      $intCommentID = $this->GetCommentID();
      $CMS->RES->EditComment($intAreaID, $intCommentID);
      if (!$CMS->RES->IsError()) {
        $strEdit = "<a href=\"".FN_COMMENT."?action=edit&amp;id=$intCommentID&amp;area=$intAreaID\">Edit</a>";
      }
      return $strEdit;
    }
    function GetCommentDeleteLink() {
      global $CMS;
      $strDelete = "";
      $intAreaID    = $this->GetContentAreaID();
      $intCommentID = $this->GetCommentID();
      $CMS->RES->DeleteComment($intAreaID);
      if (!$CMS->RES->IsError()) {
        $strDelete = "<a href=\"".FN_COMMENT."?action=delete&amp;id=$intCommentID&amp;area=$intAreaID\">Delete</a>";
      }
      return $strDelete;
    }
    // ** Comments - Builder ** //
    function GetComments($intContentID) {
      global $CMS;
      $arrComments = $CMS->COM->GetArticleComments($intContentID);
      return $arrComments;
    }
    function GetCommentForm() {
      global $CMS;
      $intContentID = $this->GetContentID();
      $intAreaID    = $this->GetContentAreaID();
      $strLocked    = $this->GetContentLocked();
      if ($strLocked == "Y") {
        $blnLocked = true;
        $blnAddComment = false;
      } else {
        $blnLocked = false;
        $CMS->RES->AddComment($intAreaID);
        $blnAddComment = !$CMS->RES->IsError();
      }
  		if ($blnAddComment) {
        $strCommentForm = $CMS->AC->CommentForm("", $intContentID, "Create", $intAreaID, "", "", "", "", "", "", "");
  		} else {
        $strCommentForm = "";
  		}
      return $strCommentForm;
    }
    // ** Comments - Permissions ** //
    function CanAddComments() {
      global $CMS;
      $strLocked = $this->GetContentLocked();
      $intAreaID = $this->GetContentAreaID();
      if ($strLocked == "Y") {
        $blnLocked = true;
        $blnAddComment = false;
      } else {
        $blnLocked = false;
        $CMS->RES->AddComment($intAreaID);
        $blnAddComment = !$CMS->RES->IsError();
      }
      return $blnAddComment;
    }
    function IsLoggedIn() {
      global $CMS;
      $CMS->RES->ValidateLoggedIn();
      return !$CMS->RES->IsError();
    }
    // ** Miscellaneous ** //
    function GetContentIntro() {
      $strContent = $this->GetContentBody();
      $strIntro        = substr($strContent, 0, 200);
      if (strlen($strContent) > 200) {
        $strIntro .= "...";
      }
      $strIntro = str_replace("</p><p>", ". ", $strIntro);
      $strIntro = strip_tags($strIntro);
      return $strIntro;
    }
    function GetContentEditLink() {
      global $CMS;
      $strEdit = "";
      $intAreaID    = $this->GetContentAreaID();
      $intContentID = $this->GetContentID();
      $intAuthorID  = $this->GetContentAuthorID();
      $CMS->RES->EditArticleCached($intAreaID, $intContentID, $intAuthorID);
      if (!$CMS->RES->IsError()) {
        $strEdit = "<a href=\"".FN_ADM_WRITE."?action=edit&amp;id=$intContentID\">Edit</a> ";
      }
      return $strEdit;
    }
    function GetContentDeleteLink() {
      global $CMS;
      $strDelete = "";
      $intAreaID    = $this->GetContentAreaID();
      $intContentID = $this->GetContentID();
      $strDeleted   = $this->GetContentDeleted();
      $CMS->RES->DeleteArticle($intAreaID);
      if (!$CMS->RES->IsError()) {
        $strViewLink = $CMS->PL->ViewArea($intAreaID);
        if ($strDeleted == "Y") {
          $strDelete = "<a href=\"".FN_USER_TOOLS."?action=restorearticle&amp;id=$intContentID&amp;back=$strViewLink\">Restore</a>";
        } else {
          $strDelete = "<a href=\"".FN_USER_TOOLS."?action=deletearticle&amp;id=$intContentID&amp;back=$strViewLink\">Delete</a>";
        }
      }
      return $strDelete;
    }
    function GetContentLockLink() {
      global $CMS;
      $strLockLink = "";
      $intAreaID    = $this->GetContentAreaID();
      $intContentID = $this->GetContentID();
      $strLocked   = $this->GetContentLocked();
      $CMS->RES->LockArticle($intAreaID);
      if (!$CMS->RES->IsError()) {
        $strViewLink = $CMS->PL->ViewArticle($intContentID);
        if ($strLocked == "Y") {
          $strLockLink = "<a href=\"".FN_USER_TOOLS."?action=unlockarticle&amp;id=$intContentID&amp;back=$strViewLink\">Unlock</a>";
        } else {
          $strLockLink = "<a href=\"".FN_USER_TOOLS."?action=lockarticle&amp;id=$intContentID&amp;back=$strViewLink\">Lock</a>";
        }
      }
      return $strLockLink;
    }
    function GetContentIsUnread() {
      global $CMS;
      $strReadUserlist = $this->GetContentReadUserlist();
      $CMS->RES->ValidateLoggedIn();
      if ($CMS->RES->IsError()) {
        $blnArticleIsUnread = false;
      } else {
        $intUserID = $CMS->RES->GetCurrentUserID();
        $blnArticleIsUnread = true;
        if ($strReadUserlist != "") {
          $arrUserlist = explode("|", $strReadUserlist);
          for ($j=0; $j<count($arrUserlist); $j++) {
            if ($arrUserlist[$j] == $intUserID) {
              $blnArticleIsUnread = false;
              break;
            }
          }
        }
      }
      return $blnArticleIsUnread;
    }
    // ** Profile - Set ** //
    var $arrUserProfile;
    function SetUser($arrUserProfile) {
      $this->arrUserProfile = $arrUserProfile;
    }
    // ** Profile - Get - Basic ** //
    function GetUserID() {
      return $this->arrUserProfile['id'];
    }
    function GetUserName() {
      return $this->arrUserProfile['username'];
    }
    function GetUserForename() {
      return $this->arrUserProfile['forename'];
    }
    function GetUserSurname() {
      return $this->arrUserProfile['surname'];
    }
    function GetUserEmail() {
      return $this->arrUserProfile['email'];
    }
    function GetUserLocation() {
      return $this->arrUserProfile['us_loc'];
    }
    function GetUserOccupation() {
      return $this->arrUserProfile['occupation'];
    }
    function GetUserInterests() {
      return $this->arrUserProfile['interests'];
    }
    function GetUserHomeLink() {
      return $this->arrUserProfile['homepage_link'];
    }
    function GetUserHomeText() {
      return $this->arrUserProfile['homepage_text'];
    }
    function GetUserJoinDate() {
      return $this->arrUserProfile['join_date'];
    }
    function GetUserAvatarID() {
    	return $this->arrUserProfile['up_id'];
    }
    // ** Profile - Get - Special ** //
    function GetUserAvatarHTML() {
      $intAvatarID = $this->GetUserAvatarID();
      $strUserName = $this->GetUserName();
    	if ($intAvatarID) {
    		$strAvatarHTML = "<img src=\"".FN_FILE_DOWNLOAD."?id=$intAvatarID\" alt=\"$strUserName's avatar\" />\n";
    	} else {
    		$strAvatarHTML = "&nbsp;";
    	}
      return $strAvatarHTML;
    }
    function GetUserHomeHTML() {
      $strUserHomeLink = $this->GetUserHomeLink();
      $strUserHomeText = $this->GetUserHomeText();
      if ((!$strUserHomeLink) || (!$strUserHomeText)) {
        $strUserHomeHTML = "&nbsp;";
      } else {
        $strUserHomeHTML = "<a href=\"$strUserHomeLink\">$strUserHomeText</a>";
      }
      return $strUserHomeHTML;
    }
  }

?>