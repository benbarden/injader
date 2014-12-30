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

  class Area extends Helper {
    // Caching
    var $blnTopLevelCacheLoaded = false;
    var $arrArea;
    var $strArticleIDs;
    // Create, Edit, Delete //
    function CreateArea($strName, $intAreaLevel, $intAreaOrder, 
      $intHierLeft, $intHierRight, $intParentID, $intPerProfileID, $intGraphicID, 
      $intContPerPage, $strSortRule, $strIncludeInRSSFeed, 
      $strMaxFileSize, $intMaxFilesPerUser, $strAreaURL, $strSmartTags, $strAreaDesc, 
      $strType, $strThemePath, $strLayoutStyle, $strSubareaContent) {
      global $CMS;
      $strSEOName = $this->MakeSEOTitle($strName);
      $intID = $this->Query("
        INSERT INTO {IFW_TBL_AREAS}(name, area_level, area_order, hier_left, hier_right, 
        parent_id, permission_profile_id, area_graphic_id, content_per_page, sort_rule, 
        include_in_rss_feed, max_file_size, max_files_per_user, area_url, smart_tags, 
        seo_name, area_description, area_type, theme_path, layout_style,
        subarea_content_on_index)
        VALUES('$strName', $intAreaLevel, $intAreaOrder, $intHierLeft, $intHierRight, 
        $intParentID, $intPerProfileID, $intGraphicID, $intContPerPage, '$strSortRule', 
        '$strIncludeInRSSFeed', '$strMaxFileSize', $intMaxFilesPerUser, '$strAreaURL', 
        '$strSmartTags', '$strSEOName', '$strAreaDesc', '$strType', '$strThemePath', 
        '$strLayoutStyle', '$strSubareaContent'
        )
      ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_AREA_CREATE, $intID, $strName);
      
      // Update mapping table
      $CMS->PL->SetTitle($strSEOName);
      $strLink = $CMS->PL->ViewArea($intID);
      $CMS->PL->SetTitle("");
      $CMS->UM->addLink($strLink, 0, $intID);
      
      return $intID;
    }
    function EditArea($intAreaID, $strName, $intAreaLevel, $intAreaOrder, 
      $intHierLeft, $intHierRight, $intParentID, $intPerProfileID, $intGraphicID, 
      $intContPerPage, $strSortRule, $strIncludeInRSSFeed, 
      $strMaxFileSize, $intMaxFilesPerUser, $strAreaURL, $strSmartTags, $strAreaDesc, 
      $strType, $strThemePath, $strLayoutStyle, $blnRebuild, $strSubareaContent) {
      global $CMS;
      if ($blnRebuild) {
        $strHierClause = "hier_left = $intHierLeft, hier_right = $intHierRight, ";
      } else {
        $strHierClause = "";
      }
      $strSEOName = $this->MakeSEOTitle($strName);
      $this->Query("
        UPDATE {IFW_TBL_AREAS}
        SET name = '$strName', area_level = $intAreaLevel, area_order = $intAreaOrder, 
        $strHierClause parent_id = $intParentID, permission_profile_id = $intPerProfileID, 
        area_graphic_id = $intGraphicID, content_per_page = $intContPerPage, 
        sort_rule = '$strSortRule', include_in_rss_feed = '$strIncludeInRSSFeed', 
        max_file_size = '$strMaxFileSize', max_files_per_user = $intMaxFilesPerUser, 
        area_url = '$strAreaURL', smart_tags = '$strSmartTags', seo_name = '$strSEOName', 
        area_description = '$strAreaDesc', area_type = '$strType', 
        theme_path = '$strThemePath', layout_style = '$strLayoutStyle', 
        subarea_content_on_index = '$strSubareaContent'
        WHERE id = $intAreaID
      ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_AREA_EDIT, $intAreaID, $strName);
      
      // Update mapping table
      $CMS->PL->SetTitle($strSEOName);
      $strLink = $CMS->PL->ViewArea($intAreaID);
      $CMS->PL->SetTitle("");
      $CMS->UM->addLink($strLink, 0, $intAreaID);
      
    }
    function DeleteArea($intAreaID) {
      global $CMS;
      $arrContent = $this->ResultQuery("SELECT id FROM {IFW_TBL_CONTENT} WHERE content_status = '{C_CONT_DELETED}' AND content_area_id = $intAreaID ORDER BY id ASC", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrContent); $i++) {
        $CMS->ART->Delete($arrContent[$i]['id']);
      }
      $arrArea = $this->ResultQuery("SELECT name FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $strName = $arrArea[0]['name'];
      // Delete URL mappings
      $this->Query("DELETE FROM {IFW_TBL_URL_MAPPING} WHERE area_id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      // Delete the area
      $this->Query("DELETE FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $CMS->AL->Build(AL_TAG_AREA_DELETE, $intAreaID, $strName);
    }
    // Special update functions //
    function ReorderArea($intAreaID, $intOrder) {
      $this->Query("UPDATE {IFW_TBL_AREAS} SET area_order = $intOrder WHERE id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
    }
    // ** SEO getter ** //
    function GetIDFromSEOTitle($strSEOTitle) {
      $strQuery = sprintf("SELECT id FROM {IFW_TBL_AREAS} WHERE seo_name = '%s'",
              mysql_real_escape_string($strSEOTitle));
      $arrResult = $this->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['id'];
    }
    function GetIDFromName($strName) {
      $strQuery = sprintf("SELECT id FROM {IFW_TBL_AREAS} WHERE name = '%s'",
              mysql_real_escape_string($strName));
      $arrResult = $this->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrResult[0]['id'];
    }
    // ** Get ** //
    function GetArea($intAreaID) {
      if ($intAreaID) {
        // Build cache first for the navigation bar
        if (!isset($this->arrArea[$intAreaID])) {
          if (!$this->blnTopLevelCacheLoaded) {
            $this->BuildTopLevelAreaCache();
          }
        }
        // If it's still not set, get this specific area
        if (!isset($this->arrArea[$intAreaID])) {
          $arrArea = $this->ResultQuery("
            SELECT a.*, p.id AS profile_id, is_system, view_area, create_article, 
            edit_article, delete_article, add_comment, edit_comment, delete_comment, 
            lock_article, attach_file FROM {IFW_TBL_AREAS} a 
            LEFT JOIN {IFW_TBL_PERMISSION_PROFILES} p ON a.permission_profile_id = p.id 
            WHERE a.id = $intAreaID
          ", __CLASS__ . "::" . __FUNCTION__ ." (Area: $intAreaID)", __LINE__);
          $this->arrArea[$intAreaID] = $arrArea[0];
        }
        return $this->arrArea[$intAreaID];
      }
    }
    function BuildTopLevelAreaCache() {
      $arrAreas = $this->ResultQuery("
        SELECT a.*, p.id AS profile_id, is_system, view_area, create_article, 
        edit_article, delete_article, add_comment, edit_comment, delete_comment, 
        lock_article, attach_file FROM {IFW_TBL_AREAS} a 
        LEFT JOIN {IFW_TBL_PERMISSION_PROFILES} p ON a.permission_profile_id = p.id 
        WHERE area_level = 1 ORDER BY a.id ASC
      ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      for ($i=0; $i<count($arrAreas); $i++) {
        $intAreaID = $arrAreas[$i]['id'];
        $this->arrArea[$intAreaID] = $arrAreas[$i];
      }
      $this->blnTopLevelCacheLoaded = true;
    }
    // ** Get (these use GetArea) ** //
    function GetAreaType($intAreaID) {
      if (!isset($this->arrArea[$intAreaID])) {
        $this->GetArea($intAreaID);
      }
      return $this->arrArea[$intAreaID]['area_type'];
    }
    function GetMaxFileSize($intAreaID) {
      if (!isset($this->arrArea[$intAreaID])) {
        $this->GetArea($intAreaID);
      }
      return $this->arrArea[$intAreaID]['max_file_size'];
    }
    function GetPerProfileID($intAreaID) {
      if (!isset($this->arrArea[$intAreaID])) {
        $this->GetArea($intAreaID);
      }
      return $this->arrArea[$intAreaID]['permission_profile_id'];
    }
    function GetSEOTitle($intAreaID) {
      if (!isset($this->arrArea[$intAreaID])) {
        $this->GetArea($intAreaID);
      }
      return $this->arrArea[$intAreaID]['seo_name'];
    }
    
    /**
     * Counts the total number of areas
     * @return integer
     */
    function CountAreas() {
      $arrAreaCount = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_AREAS} "
        , __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreaCount[0]['count'];
    }
    
    /**
     * Counts the number of areas with a particular area type
     * @param $strAreaType
     * @return integer
     */
    function CountAreasByAreaType($strAreaType) {
    	if (empty($strAreaType)) {
    		return "0";
    	} else {
        $strWhereClause = " WHERE area_type = '$strAreaType' ";
      }
      $arrAreaCount = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_AREAS} ".
        $strWhereClause, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreaCount[0]['count'];
    }

    /**
     * Counts the number of articles that exist in a comma-delimeted list of IDs.
     * Only articles that actually exist will be counted.  
     * @param $strTagList - e.g. 1,2,3,4,5
     * @return integer
     */
    function CountContentInList($strTagList) {
      $arrAreaCount = $this->ResultQuery("
        SELECT count(*) AS count FROM {IFW_TBL_CONTENT}
        WHERE id IN (".$strTagList.")
      ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreaCount[0]['count'];
    }
    
    function CountSmartAreaContent($strSmartTags) {
      $arrSmartTags = explode("|", $strSmartTags);
      $strArticleIDs = "";
      for ($i=0; $i<count($arrSmartTags); $i++) {
        $intTagID = $arrSmartTags[$i];
        $arrTagIDs = $this->ResultQuery("
          SELECT article_list FROM {IFW_TBL_TAGS} WHERE id = $intTagID
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        $strTagIDs = $arrTagIDs[0]['article_list'];
        if ($strTagIDs) {
          if ($strArticleIDs) {
            $strArticleIDs .= ",".$strTagIDs;
          } else {
            $strArticleIDs = $strTagIDs;
          }
        }
      }
      if ($strArticleIDs) {
        $intAreaCount = $this->CountContentInList($strArticleIDs);
        $this->strArticleIDs = $strArticleIDs;
      } else {
        $intAreaCount = 0;
        $this->strArticleIDs = "";
      }
      return $intAreaCount;
    }
    
    // ** Area/Article Counting ** //
    function CountContentInArea($intAreaID, $strContentStatus) {
      if ($strContentStatus) {
        $strWhereClause = " AND content_status = '$strContentStatus'";
      } else {
        $strWhereClause = "";
      }
      $arrAreaCount = $this->ResultQuery("
        SELECT count(*) AS count FROM {IFW_TBL_CONTENT}
        WHERE content_area_id = $intAreaID $strWhereClause
      ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreaCount[0]['count'];
    }
    
    /**
     * Special method for counting the published content in an area
     * plus all of its subareas, if the necessary flag is set
     * @param $intAreaID
     * @return integer
     */
    function CountIndexContent($intAreaID, $arrArea) {
        
        if ($arrArea['subarea_content_on_index'] == "Y") {
            
            $strQuery = sprintf("
                SELECT count(*) AS count
                FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a)
                WHERE c.content_area_id = a.id
                AND a.hier_left BETWEEN %s AND %s
                AND content_status = '{C_CONT_PUBLISHED}'
            ",
                mysql_real_escape_string($arrArea['hier_left']),
                mysql_real_escape_string($arrArea['hier_right'])
            );
            
        } else {
            
            $strQuery = sprintf("
                SELECT count(*) AS count
                FROM ({IFW_TBL_CONTENT} c, {IFW_TBL_AREAS} a)
                WHERE c.content_area_id = a.id
                AND c.content_area_id = %s
                AND content_status = '{C_CONT_PUBLISHED}'
            ",
                mysql_real_escape_string($intAreaID)
            );
            
        }
        
        $arrResult = $this->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        
        return (int) $arrResult[0]['count'];
        
    }
    
    // ** Misc area selection methods ** //
    function GetDefaultAreaID() {
      $arrDefault = $this->ResultQuery("
        SELECT id FROM {IFW_TBL_AREAS} ORDER BY hier_left LIMIT 1
      ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrDefault[0]['id'];
    }
  }
?>