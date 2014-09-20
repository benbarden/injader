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

  class View extends Helper {
    var $intUserProfileID;
    // ** Theme Data ** //
    var $strThemeHeader;
    var $strThemeStyles;
    var $strThemeFooter;
    var $strThemeIndex;
    var $strThemePage;
    var $strThemeProfile;
    // ** Robots ** //
    var $blnAllowRobots;
    // ** ADMIN PREVIEW ** //
    var $blnDoPreview;
    function DoPreview($blnPreview) {
      $this->blnDoPreview = $blnPreview;
    }
    function IsPreview() {
      return $this->blnDoPreview ? true : false;
    }
    // ** COMMON ** //
    var $blnThemeFileCallsComplete; // Prevent from running twice;
    function DoThemeFileCalls($strThemePath, $strLayoutStyle) {
      if ($this->blnThemeFileCallsComplete) {
        return false;
      }
      global $CMS;
      // ** STYLES ** //
      $this->strThemeStyles = $CMS->TH->GetPath($strThemePath, $strLayoutStyle, C_TH_STYLESHEET);
      if (!$this->strThemeStyles) {
        if ($strLayoutStyle) {
          // Allow layout styles to use a common stylesheet.css
          $this->strThemeStyles = $CMS->TH->GetPath($strThemePath, "", C_TH_STYLESHEET);
          if (!$this->strThemeStyles) {
            $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_STYLESHEET);
          }
        } else {
          $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_STYLESHEET);
        }
      }
      // ** HEADER ** //
      $this->strThemeHeader = $CMS->TH->GetPath($strThemePath, $strLayoutStyle, C_TH_HEADER);
      if (!$this->strThemeHeader) {
        if ($strLayoutStyle) {
          // Allow layout styles to use a common header.php
          $this->strThemeHeader = $CMS->TH->GetPath($strThemePath, "", C_TH_HEADER);
          if (!$this->strThemeHeader) {
            $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_HEADER);
          }
        } else {
          $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_HEADER);
        }
      }
      // ** FOOTER ** //
      $this->strThemeFooter = $CMS->TH->GetPath($strThemePath, $strLayoutStyle, C_TH_FOOTER);
      if (!$this->strThemeFooter) {
        if ($strLayoutStyle) {
          // Allow layout styles to use a common footer.php
          $this->strThemeFooter = $CMS->TH->GetPath($strThemePath, "", C_TH_FOOTER);
          if (!$this->strThemeFooter) {
            $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_FOOTER);
          }
        } else {
          $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_FOOTER);
        }
      }
      // ** USER PROFILE ** //
      $this->strThemeProfile = $CMS->TH->GetPath($strThemePath, $strLayoutStyle, C_TH_PROFILE);
      if (!$this->strThemeProfile) {
        if ($strLayoutStyle) {
          // Allow layout styles to use a common profile.php
          $this->strThemeProfile = $CMS->TH->GetPath($strThemePath, "", C_TH_PROFILE);
          if (!$this->strThemeProfile) {
            $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_PROFILE);
          }
        } else {
          $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_PROFILE);
        }
      }
      // ** INDEX ** //
      $this->strThemeIndex  = $CMS->TH->GetPath($strThemePath, $strLayoutStyle, C_TH_INDEX);
      if (!$this->strThemeIndex) {
        if ($strLayoutStyle) {
          // Allow layout styles to use a common index.php
          $this->strThemeIndex = $CMS->TH->GetPath($strThemePath, "", C_TH_INDEX);
          if (!$this->strThemeIndex) {
            $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_INDEX);
          }
        } else {
          $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_INDEX);
        }
      }
      // ** PAGE ** //
      $this->strThemePage   = $CMS->TH->GetPath($strThemePath, $strLayoutStyle, C_TH_PAGE);
      if (!$this->strThemePage) {
        if ($strLayoutStyle) {
          // Allow layout styles to use a common page.php
          $this->strThemePage = $CMS->TH->GetPath($strThemePath, "", C_TH_PAGE);
          if (!$this->strThemePage) {
            $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_PAGE);
          }
        } else {
          $CMS->Err_MFail(M_ERR_THEME_FILE_MISSING, C_TH_PAGE);
        }
      }
      // ** SETTINGS ** //
      $strSettingsFile = $CMS->TH->GetPath($strThemePath, "", C_TH_SETTINGS);
      if (file_exists($strSettingsFile) == true) {
        $strSettingsData = file_get_contents($strSettingsFile);
        if (strpos($strSettingsData, "\r\n") !== false) {
          $arrSettings = explode("\r\n", $strSettingsData); // Windows
        } elseif (strpos($strSettingsData, "\r") !== false) {
          $arrSettings = explode("\r", $strSettingsData); // Mac
        } elseif (strpos($strSettingsData, "\n") !== false) {
          $arrSettings = explode("\n", $strSettingsData); // Linux
        } else {
          $arrSettings[0] = $strSettingsData; // Only one item
        }
        if ($strSettingsData) {
          if ((is_array($arrSettings)) && (count($arrSettings) > 0)) {
            for ($i=0; $i<count($arrSettings); $i++) {
              $arrSettingRow = explode(" => ", $arrSettings[$i]);
              $strCol1 = empty($arrSettingRow[0]) ? "" : $arrSettingRow[0];
              $strCol2 = empty($arrSettingRow[1]) ? "" : $arrSettingRow[1];
              $CMS->TS->Set($strCol1, $strCol2);
            }
          }
        }
      }
      // ** END ** //
      $this->blnThemeFileCallsComplete = true;
    }
    function DoSystemGeneric() {
      global $CMS;
      $strSiteTitle = $CMS->SYS->GetSysPref(C_PREF_SITE_TITLE);
      $CMS->TH->SetHeaderSiteTitle($strSiteTitle);
      $strSiteDesc = $CMS->SYS->GetSysPref(C_PREF_SITE_DESCRIPTION);
      $CMS->TH->SetHeaderSiteDesc($strSiteDesc);
      $strMetaGenerator = "<meta name=\"generator\" content=\"".PRD_PRODUCT_NAME." - ".PRD_PRODUCT_URL."\" />\n";
      $CMS->TH->SetHeaderMetaGenerator($strMetaGenerator);
      $strSiteKeywords = $CMS->SYS->GetSysPref(C_PREF_SITE_KEYWORDS);
      $strKeywords = "<meta name=\"keywords\" content=\"$strSiteKeywords\" />\n";
      $strSiteFavicon  = $CMS->SYS->GetSysPref(C_PREF_SITE_FAVICON);
      if ($strSiteFavicon) {
        $strFavicon = "<link rel=\"icon\" href=\"$strSiteFavicon\" type=\"image/x-icon\" />\n<link rel=\"shortcut icon\" href=\"$strSiteFavicon\" type=\"image/x-icon\" />\n";
      } else {
        $strFavicon = "";
      }
      $strMetaTags = $strKeywords.$strFavicon;
      $CMS->TH->SetHeaderMetaKeywords($strMetaTags);
      $strHeaderCoreStyles = "<link href=\"".URL_SYS_ROOT."core.css\" rel=\"stylesheet\" type=\"text/css\" />\n";
      $CMS->TH->SetHeaderCoreStyles($strHeaderCoreStyles);
      $strHeaderScripts = "<script type=\"text/javascript\" src=\"".URL_SYS_ROOT."scripts.js\"></script>\n<script type=\"text/javascript\" src=\"".URL_SYS_ROOT."init.js\"></script>\n";
      $CMS->TH->SetHeaderScripts($strHeaderScripts);
      // 2.3.0
      // TinyMCE preview overrides this, so ensure it hasn't been set yet.
      if (!$CMS->TH->GetHeaderCustomTags()) {
        $strCustomHeadTags = $CMS->SYS->GetSysPref(C_PREF_SITE_HEADER);
        $CMS->TH->SetHeaderCustomTags($strCustomHeadTags);
      }
    }
    function DoTopLevelNavBar($intAreaID, $strNavType) {
      global $CMS;
      if (!$strNavType) {
        $strNavType = C_NAV_PRIMARY;
      }
      $strNavClause = " AND parent.nav_type = '$strNavType' ";
      if ($intAreaID) {
        $blnDefaultPage = false;
      } else {
        $blnDefaultPage = true;
        $intAreaID = $CMS->AR->GetDefaultAreaID($strNavType);
      }
      $arrAreas = $this->ResultQuery("SELECT parent.id, parent.name, parent.seo_name ".
        "FROM ({IFW_TBL_AREAS} AS node, {IFW_TBL_AREAS} AS parent) ".
        "WHERE node.hier_left BETWEEN parent.hier_left AND parent.hier_right ".
        "AND node.id = $intAreaID $strNavClause ORDER BY node.hier_left", 
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $intTopLevelAreaID = $arrAreas[0]['id'];
      // Primary navigation - Top-level areas
      $arrAreas = $CMS->AT->GetParentedAreas(0, "All", $strNavType);
      $strNavData = "";
      $j = 0;
      $intNavCount = 0;
      for ($i=0; $i<count($arrAreas); $i++) {
        $intNavID = $arrAreas[$i]['id'];
        $CMS->RES->ViewArea($intNavID);
        if (!$CMS->RES->IsError()) {
          $arrNavigationItems[$j++] = $arrAreas[$i];
          $intNavCount++;
        }
      }
      if ($blnDefaultPage) {
        $intTopLevelAreaID = ""; // Don't light up the navbar for default pages
      }
      // *** Assignments *** //
      $CMS->TH->SetNavigationSelectedItem($intTopLevelAreaID);
      $CMS->TH->SetNavigationCount($intNavCount);
      $CMS->TH->SetNavigationItems($arrNavigationItems);
      $CMS->TH->StartNavigationItems();
    }
    function DoAllLevelNavBar($strNavType) {
      global $CMS;
      if (!$strNavType) {
        $strNavType = C_NAV_PRIMARY;
      }
      $CMS->AT->arrAreaData = array(); // Reset
      $arrDDLAreas = $CMS->AT->BuildAreaArray(1, $strNavType);
      $j = 0; // List count
      $arrNavigationItems = array();
      if (is_array($arrDDLAreas)) {
	      for ($i=0; $i<count($arrDDLAreas); $i++) {
	        $intDDLID    = $arrDDLAreas[$i]['id'];
	        $strDDLName  = $arrDDLAreas[$i]['name'];
	        $strDDLType  = $arrDDLAreas[$i]['type'];
	        $intDDLLevel = $arrDDLAreas[$i]['level'];
	        $blnProceed = false;
	        $CMS->RES->ViewArea($intDDLID);
	        $blnProceed = $CMS->RES->IsError() ? false : true;
	        if ($blnProceed) {
	        	$strDDLNameIndent = " ";
	          if ($intDDLLevel > 1) {
	            for ($k=1; $k<$intDDLLevel; $k++) {
	              $strDDLNameIndent .= "- ";
	            }
	          }
	          $arrNavigationItems[$j]           = $arrDDLAreas[$i];
	          $arrNavigationItems[$j]['indent'] = $strDDLNameIndent;
	          //$arrNavigationItems[$j]['list_value'] = $intDDLID;
	          //$arrNavigationItems[$j]['list_text']  = $strDDLNameIndent.$strDDLName;
	          $j++;
	        }
	      }
      }
      // *** Assignments *** //
      $CMS->TH->SetNavigationSelectedItem("");
      $CMS->TH->SetNavigationCount($j);
      if (is_array($arrNavigationItems)) {
        $CMS->TH->SetNavigationItems($arrNavigationItems);
      }
      $CMS->TH->StartNavigationItems();
    }


      /* ********************************************** */
      /* *                                              */
      /* *              view.php/area/                  */
      /* *                                              */
      /* ********************************************** */


    function Area($intID) {
      global $CMS;
      $dteStartTime = $this->MicrotimeFloat();
      
      $CMS->TH->SetCurrentLocation(C_TL_INDEX);
      
      // System lock
      $strSystemLock = $CMS->SYS->GetSysPref(C_PREF_SYSTEM_LOCK);
      if ($strSystemLock == "Y") {
        $CMS->RES->Admin();
        $blnIsSystemLocked = $CMS->RES->IsError() ? true : false;
        $CMS->RES->ClearErrors();
      } else {
        $blnIsSystemLocked = false;
      }
      // Multi-paging
      if (!empty($_GET['page'])) {
        $intPageNumber = $_GET['page'];
        if ($intPageNumber < 1) {
          $intPageNumber = 1;
        }
      } else {
        $intPageNumber = 1;
      }
      // Check the area exists
      $arrArea = $CMS->AR->GetArea($intID);
      if (!$arrArea['id']) {
        $this->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Area ID: $intID");
      }
      // Check the user has access
      $CMS->RES->ViewArea($intID);
      if ($CMS->RES->IsError()) {
        $CMS->Err_MFail(M_ERR_UNAUTHORISED, "ViewArea");
      }
      // ** SEO links ** //
      $strAreaName = $arrArea['name'];
      $strSEOName  = $arrArea['seo_name'];
      $CMS->PL->SetTitle($strSEOName);
      if (!empty($_GET['page'])) {
        $strViewArea = $CMS->PL->ViewArea($intID)."?page=".$intPageNumber;
      } else {
        $strViewArea = $CMS->PL->ViewArea($intID);
      }

      // Release scheduled content
      $CMS->ART->ReleaseScheduledContent();
      // Purge old access logs
      $CMS->AL->Purge();
      // Can't view linked areas
      $strAreaType = $CMS->AR->GetAreaType($intID);
      if ($strAreaType == "Linked") {
        $this->Err_MFail(M_ERR_VIEW_LINKED_AREA, "Area: $intID");
      }
      // Build the page
      $strContentItems = ""; // Initialise
      $strAreaName       = $arrArea['name'];
      $strAreaDesc       = $arrArea['area_description'];
      $intParentID       = $arrArea['parent_id'];
      $intContentPerPage = $arrArea['content_per_page'];
      $intAreaGraphicID  = $arrArea['area_graphic_id'];
      /* ********************************************** */
      /* *              Theme File Calls                */
      /* ********************************************** */
      $this->DoThemeFileCalls($arrArea['theme_path'], $arrArea['layout_style']);
      /* ********************************************** */
      /* *              System Generic                  */
      /* ********************************************** */
      $this->DoSystemGeneric();
      /* ********************************************** */
      /* *              Navigation Bar                  */
      /* ********************************************** */
      $this->DoTopLevelNavBar($intID, "");
      /* ********************************************** */
      /* *              Header - Page Title             */
      /* ********************************************** */
      $CMS->TH->SetHeaderPageTitle($strAreaName);
      /* ********************************************** */
      /* *              Header - Meta Desc              */
      /* ********************************************** */
      $strMetaDesc = $CMS->TH->GetHeaderSiteDesc();
      $strMetaAreaDesc = strip_tags($arrArea['area_description']);
      if ($strMetaAreaDesc) {
        $strMetaDesc = $strMetaAreaDesc;
      }
      $strMetaDesc = str_replace('&nbsp;', "", $strMetaDesc);
      $strMetaDesc = str_replace('&quot;', "'", $strMetaDesc);
      $strMetaDesc = str_replace('"', "'", $strMetaDesc);
      $strMetaDesc = str_replace("<", "", $strMetaDesc);
      $strMetaDesc = str_replace(">", "", $strMetaDesc);
      $strMetaDesc = str_replace("&lt;", "", $strMetaDesc);
      $strMetaDesc = str_replace("&gt;", "", $strMetaDesc);
      $strHeaderMetaDesc = "<meta name=\"description\" content=\"$strMetaDesc\" />\n";
      $CMS->TH->SetHeaderMetaDesc($strHeaderMetaDesc);
      /* ********************************************** */
      /* *              Header - Site Feed              */
      /* ********************************************** */
      $strRSSArticlesURL = $CMS->SYS->GetSysPref(C_PREF_RSS_ARTICLES_URL);
      if (!$strRSSArticlesURL) {
        $strRSSArticlesURL = FN_FEEDS."?name=articles";
      }
      $strHeaderSiteFeed = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Site Feed - ".$CMS->TH->GetHeaderSiteTitle()."\" href=\"$strRSSArticlesURL\" />\n";
      $CMS->TH->SetHeaderSiteFeed($strHeaderSiteFeed);
      /* ********************************************** */
      /* *              Header - Area Feed              */
      /* ********************************************** */
      if ($arrArea['area_type'] == C_AREA_CONTENT) {
        $strAreaName = $arrArea['name'];
        $strHeaderAreaFeed = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Area Feed - $strAreaName - ".$CMS->TH->GetHeaderSiteTitle()."\" href=\"".FN_FEEDS."?name=articles&amp;id=$intID\" />\n";
      } else {
        $strHeaderAreaFeed = "";
      }
      $CMS->TH->SetHeaderAreaFeed($strHeaderAreaFeed);
      /* ********************************************** */
      /* *              Header - Article Feed           */
      /* ********************************************** */
      $strHeaderArticleFeed = ""; // Not used on area pages
      $CMS->TH->SetHeaderArticleFeed($strHeaderArticleFeed);
      /* ********************************************** */
      /* *              Header - Area Styles            */
      /* ********************************************** */
      $strHeaderAreaStyles = "<link href=\"".$this->strThemeStyles."\" rel=\"stylesheet\" type=\"text/css\" />\n";
      $CMS->TH->SetHeaderAreaStyles($strHeaderAreaStyles);
      /* ********************************************** */
      /* *              Sys - Wrapper                   */
      /* ********************************************** */
      $strSysWrapperStart = "<div id=\"area-index-$intID\" class=\"area-index\">\n";
      $strSysWrapperEnd   = "</div>\n";
      $CMS->TH->SetSysWrapperStart($strSysWrapperStart);
      $CMS->TH->SetSysWrapperEnd($strSysWrapperEnd);
      /* ********************************************** */
      /* *              Sys - Breadcrumbs               */
      /* ********************************************** */
      $strBreadcrumbs = $CMS->AT->BuildBreadcrumbs($intID, true, 0, 0);
      $CMS->TH->SetSysBreadcrumbs($strBreadcrumbs);
      /* ********************************************** */
      /* *              Sys - Write Link                */
      /* ********************************************** */
      if ($strAreaType != "Smart") {
        $CMS->RES->CreateArticle($intID);
        if (!$CMS->RES->IsError()) {
          $CMS->TH->SetSysWriteLink(FN_ADM_WRITE."?action=create&amp;area=$intID");
        }
      }
      /* ********************************************** */
      /* *              Area - Graphic                  */
      /* ********************************************** */
      $strAreaGraphic = "<div class=\"content-area-graphic\"><img src=\"{FN_FILE_DOWNLOAD}?id=$intAreaGraphicID\" alt=\"$strAreaName - Area graphic\" /></div>";
      /* ********************************************** */
      /* *              Area - Content                  */
      /* ********************************************** */
      // 5. Content in this area
      $intCount = 0;
      if ($strAreaType == "Smart") {
        $intAreaCount = $CMS->AR->CountSmartAreaContent($arrArea['smart_tags']);
      } else {
        $intAreaCount = $CMS->AR->CountIndexContent($intID, $arrArea);
      }
      if ($intAreaCount > 0) {
        $intStart = $CMS->PN->GetPageStart($intContentPerPage, $intPageNumber);
        $intEnd   = $intContentPerPage;
        // Get content
        $strSQLSortRule = $CMS->BuildAreaSortRule($arrArea['sort_rule']);
        if ($strAreaType == "Smart") {
          $strArticleIDs = $CMS->AR->strArticleIDs;
          $arrContentItems = $CMS->ART->GetSmartAreaArticles($strArticleIDs, $strSQLSortRule, $intStart, $intEnd);
        } else {
          $arrContentItems = $CMS->ART->GetIndexContent($intID, $arrArea, $strSQLSortRule, $intStart, $intEnd);
        }
        // Page number links
        $intNumPages = $CMS->PN->GetTotalPages($intContentPerPage, $intAreaCount);
        $strViewArea = $CMS->PL->ViewArea($intID);
        $strPageLinks = $CMS->PNN->MakeOneParam($intNumPages, $intPageNumber, $strViewArea);
        // Ensure we don't get too many items
        $intCount = $CMS->PN->ItemsOnPage($intContentPerPage, count($arrContentItems));
      } else {
        $strPageLinks = "";
      }
      // *** Assignments *** //
      $CMS->TH->SetContentCount($intCount);
      if ($intCount > 0) {
        $CMS->TH->SetContentItems($arrContentItems);
        $CMS->TH->StartContentItems();
      }
      /* ********************************************** */
      /* *              Area - Subareas                 */
      /* ********************************************** */
      $arrParentedAreas = $CMS->AT->GetParentedAreas($intID, "All", "");
      $intSubareaCount = 0;
      for ($i=0; $i<count($arrParentedAreas); $i++) {
        $intSubAreaID = $arrParentedAreas[$i]['id'];
        $CMS->RES->ViewArea($intSubAreaID);
        if (!$CMS->RES->IsError()) {
          $arrAvailableAreas[$intSubareaCount++] = $arrParentedAreas[$i];
        }
      }
      // *** Assignments *** //
      $CMS->TH->SetSubareaCount($intSubareaCount);
      if ($intSubareaCount > 0) {
        $CMS->TH->SetSubareaItems($arrAvailableAreas);
        $CMS->TH->StartSubareaItems();
      }
      /*
          // Subarea item count
          if (strpos($strItem, '$intNumItems') !== false) {
            $intNumItems = 0;
            $strAreaType = $CMS->AR->GetAreaType($intSubAreaID);
            if ($strAreaType == "Content") {
              $intNumItems = $CMS->AR->CountContentInArea($intSubAreaID, C_CONT_PUBLISHED);
            } elseif ($strAreaType == "Smart") {
              $intNumItems = $CMS->AR->CountSmartAreaContent($strSmartTags);
            }
            $strItem = str_replace('$intNumItems', $intNumItems, $strItem);
          }
          $strItem = str_replace('$cmsGraphicLink', "<div class=\"subarea-graphiclink\"><a href=\"$strAreaHref\"><img src=\"{FN_FILE_DOWNLOAD}?id=$intGraphicID\" alt=\"Link to $strSubAreaName\" /></a></div>", $strItem);
          $strItem = str_replace('$cmsGraphic', "<div class=\"subarea-graphic\"><img src=\"{FN_FILE_DOWNLOAD}?id=$intGraphicID\" alt=\"$strSubAreaName - Area graphic\" /></div>", $strItem);
        }
      }
      */
      /* ********************************************** */
      /* *              Output Theme Files              */
      /* ********************************************** */
      require($this->strThemeHeader);
      require($this->strThemeIndex);
      require($this->strThemeFooter);
      // ** Disconnect ** //
      //$IQP = new IQuery;
      //$IQP->Disconnect();
      $CMS->IQ->Disconnect();
    }

      /* ********************************************** */
      /* *                                              */
      /* *              view.php/article/               */
      /* *                                              */
      /* ********************************************** */

    function Article($intID) {
      global $CMS;
      
      $CMS->TH->SetCurrentLocation(C_TL_PAGE);
      
      $arrContent = $CMS->ART->GetArticle($intID);
    	if (count($arrContent) == 0) {
        $this->Err_MFail(M_ERR_NO_ROWS_RETURNED, $intID);
    	}
      $intAreaID = $arrContent['content_area_id'];
      $CMS->RES->ViewArea($intAreaID);
      if ($CMS->RES->IsError()) {
        $CMS->Err_MFail(M_ERR_UNAUTHORISED, "ViewArea");
      }
      $strUserGroups = $arrContent['user_groups'];
      if ($strUserGroups) {
        $CMS->RES->ViewArticle($intID);
        if ($CMS->RES->IsError()) {
          $CMS->Err_MFail(M_ERR_UNAUTHORISED, "ViewArticle");
        }
      }
      if (!$CMS->ART->IsPublished($intID)) {
        $CMS->Err_MFail(M_ERR_UNPUBLISHED_CONTENT, "ContentStatus");
      }
      // ** SEO links ** //
      $strContTitle = $arrContent['title'];
      $strSEOTitle  = $arrContent['seo_title'];
      $CMS->PL->SetTitle($strSEOTitle);
      $strViewArticle = $CMS->PL->ViewArticle($intID);
      /*
      $blnRedirect = false;
      if ($strTitle) {
        if ($strTitle != $strSEOTitle) {
          $blnRedirect = true;
        }
      } else {
        $blnRedirect = true;
      }
      if ($blnRedirect) {
        if ($_SERVER['REQUEST_URI'] == $strViewArticle) {
          // Do nothing - prevent infinite loop
        } else {
          httpRedirectPerm($strViewArticle);
        }
      }
      */
      // Log activity - but only for registered users
      $intUserID = $CMS->RES->GetCurrentUserID();
      if ($intUserID) {
        $CMS->SYS->CreateAccessLog("Viewed article: <a href=\"$strViewArticle\">$strContTitle</a>", AL_TAG_ARTICLE_VIEW, $intUserID, "");
        $CMS->ART->UpdateUserList($intID, $intUserID);
        $CMS->ART->IncrementHits($intID);
      } else {
        $CMS->ART->IncrementHits($intID);
      }
      // Release scheduled content
      $CMS->ART->ReleaseScheduledContent();
      // Purge old access logs
      $CMS->AL->Purge();
      // ** Article Content ** //
      $strContTitle  = $arrContent['title'];
      $strContBody   = $arrContent['content'];
      $strAreaName   = $arrContent['area_name'];
      $intAreaID     = $arrContent['content_area_id'];
      $dteCreateDate = $arrContent['create_date'];
      $dteEditDate   = $arrContent['edit_date'];
      $strSEOTitle   = $arrContent['seo_title'];
      $strLocked     = $arrContent['locked'];
      // Hits get incremented after we retrieve the record, so increment here too
      $intHits       = $arrContent['hits'] + 1;
      $strContURL    = $arrContent['link_url'];
      $blnEdited = $arrContent['edit_date_raw'] == "0000-00-00 00:00:00" ? false : true;
      $strContIntro      = substr($strContBody, 0, 200);
      if (strlen($strContIntro) > 200) {
        $strContIntro .= "...";
      }
      // ** Get area data ** //
      $arrArea = $CMS->AR->GetArea($intAreaID);
      /* ********************************************** */
      /* *              Article - Prev/Next             */
      /* ********************************************** */
      $CMS->ARCO->Build($intAreaID, $intID);
      $CMS->TH->SetContentNextLink($CMS->ARCO->GetNext());
      $CMS->TH->SetContentPrevLink($CMS->ARCO->GetPrev());
      /* ********************************************** */
      /* *              Article - Tags                  */
      /* ********************************************** */
      $strTagIDList = $arrContent['tags'];
      if ($strTagIDList) {
        $strTextTags   = $CMS->TG->BuildNameList($strTagIDList);
        $strLinkedTags = $CMS->TG->BuildLinkedNameList($strTagIDList, $intAreaID);
      } else {
        $strTextTags   = "";
        $strLinkedTags = "";
      }
      $CMS->TH->SetContentTextTags($strTextTags);
      $CMS->TH->SetContentLinkedTags($strLinkedTags);
      $CMS->PL->SetTitle($strSEOTitle);
      /* ********************************************** */
      /* *              Article - Related Content       */
      /* ********************************************** */
      $arrRelatedContent = $CMS->TH->GetRelatedContent($strTagIDList, $intID);
      // *** Assignments *** //
      if (is_array($arrRelatedContent)) {
        $CMS->TH->SetRelatedContentCount(count($arrRelatedContent));
        $CMS->TH->SetRelatedContentItems($arrRelatedContent);
        $CMS->TH->StartRelatedContentItems();
        //$CMS->TH->NextRelatedContentItem();
      } else {
        $CMS->TH->SetRelatedContentCount(0);
      }
      /* ********************************************** */
      /* *              Article - Comments              */
      /* ********************************************** */
      $arrComments = $CMS->TH->GetComments($intID);
      // *** Assignments *** //
      if (is_array($arrComments)) {
        $CMS->TH->SetCommentCount(count($arrComments));
        $CMS->TH->SetCommentItems($arrComments);
        $CMS->TH->StartCommentItems();
        //$CMS->TH->NextCommentItem();
      } else {
        $CMS->TH->SetCommentCount(0);
      }
      /* ********************************************** */
      /* *              Theme File Calls                */
      /* ********************************************** */
      $arrArea = $CMS->AR->GetArea($intAreaID);
      $this->DoThemeFileCalls($arrArea['theme_path'], $arrArea['layout_style']);
      /* ********************************************** */
      /* *              System Generic                  */
      /* ********************************************** */
      $this->DoSystemGeneric();
      /* ********************************************** */
      /* *              Navigation Bar                  */
      /* ********************************************** */
      $this->DoTopLevelNavBar($intAreaID, "");
      /* ********************************************** */
      /* *              Header - Page Title             */
      /* ********************************************** */
      $CMS->TH->SetHeaderPageTitle($strContTitle);
      /* ********************************************** */
      /* *              Header - Meta Desc              */
      /* ********************************************** */
      $strPageDesc = strip_tags($strContBody);
      $strPageDesc = $CMS->MakeMetaDesc($strPageDesc);
      if ($strPageDesc) {
        $strMetaDesc = $strPageDesc;
      } else {
        $strMetaDesc = $CMS->TH->GetHeaderSiteDesc();
      }
      $strMetaDesc = str_replace('&nbsp;', "", $strMetaDesc);
      $strMetaDesc = str_replace('&quot;', "'", $strMetaDesc);
      $strMetaDesc = str_replace('"', "'", $strMetaDesc);
      $strMetaDesc = str_replace("<", "", $strMetaDesc);
      $strMetaDesc = str_replace(">", "", $strMetaDesc);
      $strMetaDesc = str_replace("&lt;", "", $strMetaDesc);
      $strMetaDesc = str_replace("&gt;", "", $strMetaDesc);
      $strHeaderMetaDesc = "<meta name=\"description\" content=\"$strMetaDesc\" />\n";
      $CMS->TH->SetHeaderMetaDesc($strHeaderMetaDesc);
      /* ********************************************** */
      /* *              Header - Site Feed              */
      /* ********************************************** */
      $strRSSArticlesURL = $CMS->SYS->GetSysPref(C_PREF_RSS_ARTICLES_URL);
      if (!$strRSSArticlesURL) {
        $strRSSArticlesURL = FN_FEEDS."?name=articles";
      }
      $strHeaderSiteFeed = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Site Feed - ".$CMS->TH->GetHeaderSiteTitle()."\" href=\"$strRSSArticlesURL\" />\n";
      $CMS->TH->SetHeaderSiteFeed($strHeaderSiteFeed);
      /* ********************************************** */
      /* *              Header - Area Feed              */
      /* ********************************************** */
      if ($arrArea['area_type'] == C_AREA_CONTENT) {
        $strAreaName = $arrArea['name'];
        $strHeaderAreaFeed = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Area Feed - $strAreaName - ".$CMS->TH->GetHeaderSiteTitle()."\" href=\"".FN_FEEDS."?name=articles&amp;id=$intAreaID\" />\n";
      } else {
        $strHeaderAreaFeed = "";
      }
      $CMS->TH->SetHeaderAreaFeed($strHeaderAreaFeed);
      /* ********************************************** */
      /* *              Header - Article Feed           */
      /* ********************************************** */
      $strHeaderArticleFeed = "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"Comment Feed - $strContTitle - ".$CMS->TH->GetHeaderSiteTitle()."\" href=\"".FN_FEEDS."?name=comments&amp;id=$intID\" />\n";
      $CMS->TH->SetHeaderArticleFeed($strHeaderArticleFeed);
      /* ********************************************** */
      /* *              Header - Area Styles            */
      /* ********************************************** */
      $strHeaderAreaStyles = "<link href=\"".$this->strThemeStyles."\" rel=\"stylesheet\" type=\"text/css\" />\n";
      $CMS->TH->SetHeaderAreaStyles($strHeaderAreaStyles);
      /* ********************************************** */
      /* *              Sys - Wrapper                   */
      /* ********************************************** */
      $strSysWrapperStart = "<div id=\"article-page-$intID\" class=\"article-page\">\n";
      $strSysWrapperEnd   = "</div>\n";
      $CMS->TH->SetSysWrapperStart($strSysWrapperStart);
      $CMS->TH->SetSysWrapperEnd($strSysWrapperEnd);
      /* ********************************************** */
      /* *              Sys - Breadcrumbs               */
      /* ********************************************** */
      $strBreadcrumbs = $CMS->AT->BuildBreadcrumbs($intAreaID, true, $intID, 0);
      $strBreadcrumbs .= " &gt; $strContTitle";
      $CMS->TH->SetSysBreadcrumbs($strBreadcrumbs);
      /* ********************************************** */
      /* *              Area - Graphic                  */
      /* ********************************************** */
      $intAreaGraphicID = $arrArea['area_graphic_id'];
      $strAreaGraphic = "<div class=\"content-area-graphic\"><img src=\"{FN_FILE_DOWNLOAD}?id=$intAreaGraphicID\" alt=\"$strAreaName - Area graphic\" /></div>";
      /* ********************************************** */
      /* *              Output Theme Files              */
      /* ********************************************** */
      // *** Assignments *** //
      $CMS->TH->SetContentCount(1);
      $arrThemeContent[0] = $arrContent;
      $CMS->TH->SetContentItems($arrThemeContent);
      $CMS->TH->StartContentItems();
      $CMS->TH->NextContentItem();
      // *** Output *** //
      require($this->strThemeHeader);
      require($this->strThemePage);
      require($this->strThemeFooter);
      // ** Disconnect ** //
      //$IQP = new IQuery;
      //$IQP->Disconnect();
      $CMS->IQ->Disconnect();
    }

      /* ********************************************** */
      /* *                                              */
      /* *              Default pages                   */
      /* *                                              */
      /* ********************************************** */
      
    function DefaultPageAllowRobots($strPageTitle, $strHTML) {
      $this->blnAllowRobots = true;
      $this->DefaultPage($strPageTitle, $strHTML);
    }
    
    function DefaultPage($strPageTitle, $strHTML) {
        
        global $CMS;
        
        $CMS->TH->SetCurrentLocation(C_TL_DEFAULT);
        
        /* ********************************************** */
        /* *              Theme File Calls                */
        /* ********************************************** */
        $this->DoThemeFileCalls("", "");
        /* ********************************************** */
        /* *              System Generic                  */
        /* ********************************************** */
        $this->DoSystemGeneric();
        /* ********************************************** */
        /* *              Navigation Bar                  */
        /* ********************************************** */
        $this->DoTopLevelNavBar("", "");
        /* ********************************************** */
        /* *              Header - Page Title             */
        /* ********************************************** */
        $CMS->TH->SetHeaderPageTitle($strPageTitle);
        /* ********************************************** */
        /* *              Header - Meta Desc              */
        /* ********************************************** */
        $strMetaDesc = $CMS->TH->GetHeaderSiteDesc();
        $strMetaDesc = str_replace('&nbsp;', "", $strMetaDesc);
        $strMetaDesc = str_replace('&quot;', "'", $strMetaDesc);
        $strMetaDesc = str_replace('"', "'", $strMetaDesc);
        $strMetaDesc = str_replace("<", "", $strMetaDesc);
        $strMetaDesc = str_replace(">", "", $strMetaDesc);
        $strMetaDesc = str_replace("&lt;", "", $strMetaDesc);
        $strMetaDesc = str_replace("&gt;", "", $strMetaDesc);
        $strHeaderMetaDesc = "<meta name=\"description\" content=\"$strMetaDesc\" />\n";
        if (!$this->blnAllowRobots) {
            $strHeaderMetaDesc .= "<meta name=\"robots\" content=\"noindex, nofollow\" />\n";
        }
        $CMS->TH->SetHeaderMetaDesc($strHeaderMetaDesc);
        /* ********************************************** */
        /* *              Header - Area Styles            */
        /* ********************************************** */
        $strHeaderAreaStyles = "<link href=\"".$this->strThemeStyles."\" rel=\"stylesheet\" type=\"text/css\" />\n";
        $CMS->TH->SetHeaderAreaStyles($strHeaderAreaStyles);
        /* ********************************************** */
        /* *              Sys - Wrapper Start             */
        /* ********************************************** */
        if ($CMS->TH->GetSysWrapperStart() == "") {
            $strSysWrapperStart = "<div id=\"default-page\" class=\"default-page\">\n";
            $CMS->TH->SetSysWrapperStart($strSysWrapperStart);
        } else {
            $strSysWrapperStart = $CMS->TH->GetSysWrapperStart();
        }
        /* ********************************************** */
        /* *              Sys - Wrapper End               */
        /* ********************************************** */
        if ($CMS->TH->GetSysWrapperEnd() == "") {
            $strSysWrapperEnd = "</div>\n";
            $CMS->TH->SetSysWrapperEnd($strSysWrapperEnd);
        } else {
            $strSysWrapperEnd = $CMS->TH->GetSysWrapperEnd();
        }
        /* ********************************************** */
        /* *              Output Theme Files              */
        /* ********************************************** */
        require($this->strThemeHeader);
        print($strHTML);
        require($this->strThemeFooter);
        // ** Disconnect ** //
        //$IQP = new IQuery;
        $CMS->IQ->Disconnect();
        // ** Close the page ** //
        exit;
    }
    
    /* ********************************************** */
    /* *                                              */
    /* *              view.php/file/                  */
    /* *                                              */
    /* ********************************************** */
    
    function File($intID) {
        
        global $CMS;
        $arrFile = $CMS->FL->GetFile($intID);
        if (count($arrFile) == 0) {
            $this->Err_MFail(M_ERR_NO_ROWS_RETURNED, "File ID: $intID");
        }
        if (($arrFile['is_avatar'] == "Y") || ($arrFile['is_siteimage'] == "Y")) {
            $this->Err_MFail(M_ERR_CANNOT_VIEW_DIRECTLY, "File ID: $intID");
        }
        $strSEOTitle  = $arrFile['seo_title'];
        $intArticleID = $arrFile['article_id'];
        if ($intArticleID) {
            // ** If attached to an article, 
            //    display the article rather than the file ** //
            $this->Article($intArticleID, "");
        }
        
    }

      /* ********************************************** */
      /* *                                              */
      /* *              view.php/user/                  */
      /* *                                              */
      /* ********************************************** */

    function User($intID) {
      global $CMS;
      $strTitle = "";
      $CMS->TH->SetCurrentLocation(C_TL_PROFILE);
      
      // ** Validate user details ** //
      $arrUserProfile = $CMS->US->Get($intID);
      if (count($arrUserProfile) == 0) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "User: $intID");
      }
      if ($CMS->US->IsSuspended($intID)) {
        $CMS->Err_MFail(M_ERR_USER_SUSPENDED, "");
      }
      $this->intUserProfileID = $intID; // referenced in PluginDisplay.php
      // ** SEO links ** //
      $strSEOTitle = $arrUserProfile['seo_username'];
      $blnRedirect = false;
      if ($strTitle) {
        if ($strTitle != $strSEOTitle) {
          $blnRedirect = true;
        }
      } else {
        $blnRedirect = true;
      }
      $CMS->PL->SetTitle($strSEOTitle);
      $strViewUser = $CMS->PL->ViewUser($intID);
      if ($blnRedirect) {
        if ($_SERVER['REQUEST_URI'] == $strViewUser) {
          // Do nothing - prevent infinite loop
        } else {
          httpRedirectPerm($strViewUser);
        }
      }
      $CMS->TH->SetUser($arrUserProfile);
      $strUserName = $arrUserProfile['username'];
      /* ********************************************** */
      /* *              Theme File Calls                */
      /* ********************************************** */
      $this->DoThemeFileCalls("", "");
      /* ********************************************** */
      /* *              System Generic                  */
      /* ********************************************** */
      $this->DoSystemGeneric();
      /* ********************************************** */
      /* *              Navigation Bar                  */
      /* ********************************************** */
      $this->DoTopLevelNavBar("", "");
      /* ********************************************** */
      /* *              Header - Page Title             */
      /* ********************************************** */
      $CMS->TH->SetHeaderPageTitle("View Profile - $strUserName");
      /* ********************************************** */
      /* *              Header - Meta Desc              */
      /* ********************************************** */
      $strMetaDesc = "$strUserName's profile";
      $strHeaderMetaDesc = "<meta name=\"description\" content=\"$strMetaDesc\" />\n";
      $CMS->TH->SetHeaderMetaDesc($strHeaderMetaDesc);
      /* ********************************************** */
      /* *              Header - Area Styles            */
      /* ********************************************** */
      $strHeaderAreaStyles = "<link href=\"".$this->strThemeStyles."\" rel=\"stylesheet\" type=\"text/css\" />\n";
      $CMS->TH->SetHeaderAreaStyles($strHeaderAreaStyles);
      /* ********************************************** */
      /* *              Sys - Wrapper                   */
      /* ********************************************** */
      $strSysWrapperStart = "<div id=\"user-profile\" class=\"user-profile\">\n";
      $strSysWrapperEnd   = "</div>\n";
      $CMS->TH->SetSysWrapperStart($strSysWrapperStart);
      $CMS->TH->SetSysWrapperEnd($strSysWrapperEnd);
      // *** Output *** //
      require($this->strThemeHeader);
      require($this->strThemeProfile);
      require($this->strThemeFooter);
      // ** Disconnect ** //
      //$IQP = new IQuery;
      //$IQP->Disconnect();
      $CMS->IQ->Disconnect();
      // ** Close the page ** //
      exit;
    }
  }

?>