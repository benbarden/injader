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

class URLMapping extends Helper {
    
    /**
     * URL caching
     * @var array
     */
    var $arrURLData = array();
    
    /**
     * Builds the mapping table for the first time
     * This is also used when changing the link style
     * @return void
     */
    function rebuildAll() {
        
        global $CMS;
        
        $intLinkStyle = $CMS->SYS->GetSysPref(C_PREF_LINK_STYLE);
        if (!$intLinkStyle) $intLinkStyle = "1";
        
        // ** Step 1. Deactivate all links ** //
        $CMS->Query("
            UPDATE {IFW_TBL_URL_MAPPING}
            SET is_active = 'N'
            WHERE is_active = 'Y'
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        
        // ** Step 2. Process areas ** //
        $arrAreas = $CMS->ResultQuery("
            SELECT id, seo_name FROM {IFW_TBL_AREAS}
            WHERE area_type = 'Content'
            ORDER BY id ASC
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        for ($i=0; $i<count($arrAreas); $i++) {
            $intAreaID  = $arrAreas[$i]['id'];
            $strSEOName = $arrAreas[$i]['seo_name'];
            $CMS->PL->SetTitle($strSEOName);
            $strLink    = $CMS->PL->ViewArea($intAreaID);
            $CMS->PL->SetTitle("");
            $this->addLink($strLink, 0, $intAreaID);
        }
        
        // ** Step 3. Process articles ** //
        $arrArticles = $CMS->ResultQuery("
            SELECT id, seo_title, permalink FROM {IFW_TBL_CONTENT}
            WHERE content_status = 'Published'
            ORDER BY id ASC
        ", __CLASS__ . "::" . __FUNCTION__, __LINE__);
        for ($i=0; $i<count($arrArticles); $i++) {
            $intArticleID = $arrArticles[$i]['id'];
            $strLink      = $arrArticles[$i]['permalink'];
            $this->addLink($strLink, $intArticleID, 0);
        }
        
    }
    
    /**
     * Adds a link to the mapping table
     * @param $strURL
     * @param $intArticleID
     * @param $intAreaID
     * @return void
     */
    function addLink($strURL, $intArticleID, $intAreaID) {
        
        global $CMS;
        
        $blnDelete = false;
        
        // ** 1. First ensure the link doesn't already exist in the table
        $strQuery = sprintf("
            SELECT *
            FROM {IFW_TBL_URL_MAPPING}
            WHERE relative_url = '%s'
        ", mysql_real_escape_string($strURL));
        $arrResult = $CMS->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        if (is_array($arrResult)) {
            if ($arrResult[0]['is_active'] == "Y") return false;
            if ($arrResult[0]['is_active'] == "N") $blnDelete = true;
        }
        
        // ** 2. Next, deactivate any existing links
        if (!empty($intArticleID)) {
            $strQuery = sprintf("
                UPDATE {IFW_TBL_URL_MAPPING}
                SET is_active = 'N'
                WHERE is_active = 'Y'
                AND article_id = %s
            ", mysql_real_escape_string($intArticleID));
        } elseif (!empty($intAreaID)) {
            $strQuery = sprintf("
                UPDATE {IFW_TBL_URL_MAPPING}
                SET is_active = 'N'
                WHERE is_active = 'Y'
                AND area_id = %s
            ", mysql_real_escape_string($intAreaID));
        }
        $CMS->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        
        // ** 3. If the mapping exists as an inactive item, delete it
        if ($blnDelete) {
            $strQuery = sprintf("
                DELETE FROM {IFW_TBL_URL_MAPPING}
                WHERE relative_url = '%s'
            ", mysql_real_escape_string($strURL));
            $CMS->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        }
        
        // ** 4. Finally, add the new mapping
        $strQuery = sprintf("
            INSERT INTO {IFW_TBL_URL_MAPPING} (
                relative_url, is_active, article_id, area_id
            )
            VALUES (
                '%s', 'Y', %s, %s
            )
        ",
            mysql_real_escape_string($strURL),
            mysql_real_escape_string($intArticleID),
            mysql_real_escape_string($intAreaID)
        );
        $CMS->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        
    }
    
    /**
     * Gets a URL mapping item from its URL
     * @param $strURL
     * @return array
     */
    function getByUrl($strURL) {
        
        global $CMS;
        
        $arrURL = explode("?", $strURL);
        $strURL = $arrURL[0];
        $strQuery = sprintf("
            SELECT *
            FROM {IFW_TBL_URL_MAPPING}
            WHERE relative_url = '%s'
        ", mysql_real_escape_string($strURL));
        $arrResult = $CMS->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrResult;
        
    }
    
    /**
     * Gets the active URL for a given article ID
     * @param $intArticleID
     * @return string
     */
    function getActiveArticle($intArticleID) {
        
        global $CMS;
        
        $strQuery = sprintf("
            SELECT *
            FROM {IFW_TBL_URL_MAPPING}
            WHERE article_id = %s
            AND is_active = 'Y'
        ", mysql_real_escape_string($intArticleID));
        $arrResult = $CMS->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return is_array($arrResult) ? $arrResult : "";
        
    }
    
    /**
     * Gets the active URL for a given area ID
     * @param $intAreaID
     * @return string
     */
    function getActiveArea($intAreaID) {
        
        global $CMS;
        
        $strQuery = sprintf("
            SELECT *
            FROM {IFW_TBL_URL_MAPPING}
            WHERE area_id = %s
            AND is_active = 'Y'
        ", mysql_real_escape_string($intAreaID));
        $arrResult = $CMS->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return is_array($arrResult) ? $arrResult : "";
        
    }
    
    /**
     * Checks if a url has been used by another page
     * @param $strUrl
     * @param $intArticleID
     * @param $intAreaID
     * @return boolean
     */
    function isUrlInUse($strUrl, $intArticleID = "", $intAreaID = "") {
        
        global $CMS;
        
        if (!empty($intArticleID)) {
            $strQuery = sprintf("
                SELECT count(*) AS count
                FROM {IFW_TBL_URL_MAPPING}
                WHERE article_id <> %s
                AND relative_url = '%s'
            ",
                mysql_real_escape_string($intArticleID),
                mysql_real_escape_string($strUrl)
            );
        } elseif (!empty($intAreaID)) {
            $strQuery = sprintf("
                SELECT count(*) AS count
                FROM {IFW_TBL_URL_MAPPING}
                WHERE area_id <> %s
                AND relative_url = '%s'
            ",
                mysql_real_escape_string($intAreaID),
                mysql_real_escape_string($strUrl)
            );
        } else {
            $strQuery = sprintf("
                SELECT count(*) AS count
                FROM {IFW_TBL_URL_MAPPING}
                WHERE relative_url = '%s'
            ",
                mysql_real_escape_string($strUrl)
            );
        }
        $arrResult = $CMS->ResultQuery($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        return $arrResult[0]['count'] > 0 ? true : false;
        
    }
    
    // ** ** ** code below this line may be removed ** ** ** //
    
    /**
     * Used to create the cache in the event it doesn't exist
     * @return void
     */
    function FirstCacheBuild() {
        
        global $CMS;
        
        if (!$CMS->CacheFile->Exists("URLMapping_URLs")) {
            $this->RebuildCache();
        }
        
    }
    
    /**
     * Used to forcefully rebuild the class cache from the database
     * @return void
     */
    function RebuildCache() {
        
        global $CMS;
        
        $this->arrURLData = array();
        
        $arrResult = $CMS->ResultQuery("SELECT * FROM {IFW_TBL_URL_MAPPING}", 
            __CLASS__ . "::" . __FUNCTION__, __LINE__);
        for ($i=0; $i<count($arrResult); $i++) {
            foreach ($arrResult[$i] as $key => $value) {
        	   $this->arrURLData[$key] = $value;
            }
        }
        
        $CMS->CacheBuild->ArrayBuild("URLMapping_URLs", $this->arrURLData);
        
    }
    
    /**
     * Stores a cache item using the specified format
     * @param $strKey
     * @param $arrData
     * @return void
     */
    function StoreCacheItem($strKey, $arrData) {
        
        $this->arrURLData[$strKey] = $arrData;
	    
    }
    
    /**
      * Retrieves an item from the cache
      * @param $strKey
      * @return array if found, else boolean(false)
      */
    function LoadCacheItem($strKey) {
        
        if (array_key_exists($strKey, $this->arrURLData)) {
        	
        	return $this->arrURLData[$strKey];
        	
        } else {
        	
        	return false;
        	
        }

    }
    
    /**
     * Checks whether a URL exists in the mapping table
     * @param $strURL
     * @return boolean
     */
    function Exists($strURL) {
        
        $arrCacheData = $this->LoadCacheItem($strURL);
        
        if (!is_array($arrCacheData)) {
        	
            $arrResult = $this->ResultQuery("SELECT * FROM {IFW_TBL_URL_MAPPING} ".
            "WHERE relative_url = '$strURL'", __CLASS__ . "::" . __FUNCTION__, __LINE__);
            
            $this->StoreCacheItem($strURL, $arrResult[0]);
            
            $arrCacheData = $this->LoadCacheItem($strURL);
            
        }
        
        return count($arrCacheData) > 0 ? true : false;
        
    }
    
    /**
     * Checks whether a URL is active
     * @param $strURL
     * @return boolean
     */
    function IsActive($strURL) {
        
    }
    
}
