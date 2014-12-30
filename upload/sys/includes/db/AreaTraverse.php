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

  class AreaTraverse extends Helper {
  	
    var $arrAreaData;
    var $intContentAreaTypeID;
    var $intFileAreaTypeID;
    var $intLinkedAreaTypeID;
    var $intSmartAreaTypeID;
    var $intNumAreas;
    var $intLeft;
    var $intRight;
    var $intAreasBeneathMe;
    var $blnRebuild;
    var $arrAreasBeneathMe;

    const AT_CACHE_KEY = 'AreaTraverse_Areas';
    
    /**
     * Used to create the cache in the event it doesn't exist
     * @return void
     */
    function FirstCacheBuild() {
      
      global $CMS;
      
      if (!$CMS->CacheFile->Exists(self::AT_CACHE_KEY)) {
        $this->RebuildCache();
      }
      
    }
    
    /**
     * Used to forcefully rebuild the AreaTraverse class cache from the database
     * @return void
     */
    function RebuildCache() {
      
      global $CMS;
      
      $this->arrAreaData = array();
      
      $this->RebuildAreaArray();
      
      //$CMS->CacheBuild->ArrayBuild("AreaTraverse_".$strNavType, $this->arrAreaData);
      
    }
    
    /**
     * Quick method for updating the area hierarchy
     * @return void
     */
    function RebuildAreaArray() {
    	
        $arrAreas = $this->BuildAreaArray(2);
        $this->DoRebuildLoop($arrAreas);

        $this->arrAreaData = array();
	      
    }
    
    /**
     * Updates the area level and hierarchy values for the specified area array
     * @param $arrAreas
     * @return void
     */
    function DoRebuildLoop($arrAreas) {
      if (is_array($arrAreas)) {
        for ($i=0; $i<count($arrAreas); $i++) {
          $intID    = $arrAreas[$i]['id'];
          $strName  = $arrAreas[$i]['name'];
          $strType  = $arrAreas[$i]['type'];
          $intLevel = $arrAreas[$i]['level'];
          $intOrder = $arrAreas[$i]['order'];
          $intLeft  = $arrAreas[$i]['left'];
          $intRight = $arrAreas[$i]['right'];
          $strSQL = "UPDATE {IFW_TBL_AREAS} ".
            "SET area_level = $intLevel, ".
            "hier_left = $intLeft, ".
            "hier_right = $intRight ".
            "WHERE id = $intID";
          $this->Query($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
        }
      }
    }
    
    /**
     * Used to build an array of all areas.
     * It can also regenerate the hier_left and hier_right values.
     * @param $intMode (1 = get, 2 = rebuild)
     * @return array
     */
    function BuildAreaArray($intMode) {
    	
    	global $CMS;
    	
      // Setup
      if ($intMode == 1) {
        $this->blnRebuild = false;
      } elseif ($intMode == 2) {
        $this->blnRebuild = true;
      }
      
      // Load from the cache
      if ($this->blnRebuild) {
        $this->arrAreaData = array(); // Clear the array
      	$CMS->CacheFile->Delete(self::AT_CACHE_KEY);
      } else {
      	if ($CMS->CacheFile->Exists(self::AT_CACHE_KEY)) {
      		return $CMS->CacheBuild->ArrayGet(self::AT_CACHE_KEY);
      	} else {
      		$this->FirstCacheBuild();
      	}
      }
      
      // Continue
      	$this->intNumAreas = $CMS->AR->CountAreas();

      $this->intLeft = 1;
      $this->intRight = ($this->intNumAreas) * 2;
      
      // Get top level areas
      $arrTopLevelAreas = $this->GetParentedAreas("", "All");
      
      // Exit if we have no areas with this nav type
      if (count($arrTopLevelAreas) == 0) {
      	return false;
      }
      
      for ($i=0; $i<count($arrTopLevelAreas); $i++) {
      	
        $j = $this->GetNextArrayIndex(); // Only used for $this->arrAreaData
        $intID = $arrTopLevelAreas[$i]['id'];
        $this->arrAreaData[$j]['id']   = $arrTopLevelAreas[$i]['id'];
        $this->arrAreaData[$j]['name'] = $arrTopLevelAreas[$i]['name'];
        $this->arrAreaData[$j]['type'] = $arrTopLevelAreas[$i]['area_type'];
        if ($this->blnRebuild) {
          $this->arrAreaData[$j]['level'] = $this->GetRelativeDepth($intID, "0");
        } else {
          $this->arrAreaData[$j]['level'] = $arrTopLevelAreas[$i]['area_level'];
        }
        $this->arrAreaData[$j]['order']     = $arrTopLevelAreas[$i]['area_order'];
        $this->arrAreaData[$j]['parent_id'] = $arrTopLevelAreas[$i]['parent_id'];
        $this->arrAreaData[$j]['area_url']  = $arrTopLevelAreas[$i]['area_url'];
        $this->arrAreaData[$j]['seo_name']  = $arrTopLevelAreas[$i]['seo_name'];
        // Check if there are any children
        $arrParentedAreas = $this->ResultQuery("SELECT count(*) AS count ".
          "FROM {IFW_TBL_AREAS} WHERE parent_id = $intID ORDER BY area_order",
          __CLASS__ . "::" . __FUNCTION__, __LINE__);
        if ($this->blnRebuild) {
          $this->arrAreaData[$j]['left'] = $this->intLeft;
        } else {
          $this->arrAreaData[$j]['left'] = $arrTopLevelAreas[$i]['hier_left'];
        }
        if ($this->blnRebuild) {
          $this->CountAreasBeneathMe($intID, 0); // regenerate value
          $this->arrAreaData[$j]['right'] = $this->intLeft + ($this->intAreasBeneathMe * 2) + 1;
          $this->intLeft++;
        } else {
          $this->arrAreaData[$j]['right'] = $arrTopLevelAreas[$i]['hier_right'];
        }
        if ($arrParentedAreas[0]['count'] > 0) {
          $this->AppendParented($intID);
        }
        if ($this->blnRebuild) {
          $this->intLeft++;
        }
        
      }
      
      $CMS->CacheBuild->ArrayBuild(self::AT_CACHE_KEY, $this->arrAreaData);
      return $this->arrAreaData;
      
    }
    
    /**
     * Called as part of BuildAreaArray, this adds the areas immediately beneath
     * the specified area to the area array. It is also a recursive method,
     * which will loop until all areas have been accounted for.
     * @param $intParentID - the ID of the area whose children we want to retrieve
     * @return void
     */
    function AppendParented($intParentID) {
      $arrParentedAreas = $this->GetParentedAreas($intParentID, "All");
      for ($i=0; $i<count($arrParentedAreas); $i++) {
        $j = $this->GetNextArrayIndex(); // Only used for $this->arrAreaData
        $intID = $arrParentedAreas[$i]['id'];
        $this->arrAreaData[$j]['id']   = $arrParentedAreas[$i]['id'];
        $this->arrAreaData[$j]['name'] = $arrParentedAreas[$i]['name'];
        $this->arrAreaData[$j]['type'] = $arrParentedAreas[$i]['area_type'];
        if ($this->blnRebuild) {
          $this->arrAreaData[$j]['level'] = $this->GetRelativeDepth($intID, "0");
        } else {
          $this->arrAreaData[$j]['level'] = $arrParentedAreas[$i]['area_level'];
        }
        $this->arrAreaData[$j]['order']     = $arrParentedAreas[$i]['area_order'];
        $this->arrAreaData[$j]['parent_id'] = $arrParentedAreas[$i]['parent_id'];
        $this->arrAreaData[$j]['area_url']  = $arrParentedAreas[$i]['area_url'];
        $this->arrAreaData[$j]['seo_name']  = $arrParentedAreas[$i]['seo_name'];
        // Check if there are any children (recursive)
        $arrChildAreas = $this->ResultQuery("SELECT count(*) AS count FROM {IFW_TBL_AREAS} ".
          "WHERE parent_id = $intID ORDER BY area_order",
          __CLASS__ . "::" . __FUNCTION__, __LINE__);
        if ($this->blnRebuild) {
          $this->arrAreaData[$j]['left'] = $this->intLeft;
        } else {
          $this->arrAreaData[$j]['left'] = $arrParentedAreas[$i]['hier_left'];
        }
        if ($this->blnRebuild) {
          $this->CountAreasBeneathMe($intID, 0); // regenerate value
          $this->arrAreaData[$j]['right'] = $this->intLeft + ($this->intAreasBeneathMe * 2) + 1;
          $this->intLeft++;
        } else {
          $this->arrAreaData[$j]['right'] = $arrParentedAreas[$i]['hier_right'];
        }
        if ($arrChildAreas[0]['count'] > 0) {
          $this->AppendParented($intID);
        }
        if ($this->blnRebuild) {
          $this->intLeft++;
        }
      }
    }
    
    /**
     * Counts the number of array items, thereby giving the next array index
     * @return integer
     */
    function GetNextArrayIndex() {
      return count($this->arrAreaData);
    }
    
    /**
     * Counts the number of areas beneath the specified area.
     * Recursive; will keep looping until all areas have been accounted for. 
     * @param $intAreaID - the area you wish to use for the calculation
     * @param $intContinue - always set to 0, this is used for recursion
     * @return integer
     */
    function CountAreasBeneathMe($intAreaID, $intContinue) {
      if ($intContinue == 0) {
        $this->intAreasBeneathMe = 0;
      }
      $arrAreas = $this->ResultQuery("SELECT id FROM {IFW_TBL_AREAS} ".
        "WHERE parent_id = $intAreaID", __CLASS__ . "::" . __FUNCTION__, __LINE__);
      $this->intAreasBeneathMe += count($arrAreas);
      for ($i=0; $i<count($arrAreas); $i++) {
        $intNextID = $arrAreas[$i]['id'];
        $this->CountAreasBeneathMe($intNextID, 1); // don't regenerate
      }
    }
    
    /**
     * Gets an area's depth from the database
     * @param $intAreaID
     * @return integer
     */
    function GetDepth($intAreaID) {
    	$arrDepth = $this->ResultQuery("SELECT area_level AS depth ".
        "FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", 
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrDepth[0]['depth'];
    }
    
    /**
     * Gets an area's depth based on its parent's position in the hierarchy
     * @param $intAreaID
     * @return unknown_type
     */
    function GetRelativeDepth($intAreaID, $intCounter) {
      $arrDepth = $this->ResultQuery("SELECT parent_id, area_level ".
        "FROM {IFW_TBL_AREAS} WHERE id = $intAreaID", 
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      if ($arrDepth[0]['parent_id'] == "0") {
        return $arrDepth[0]['area_level'] + $intCounter;
      } else {
      	$intCounter++;
        return $this->GetRelativeDepth($arrDepth[0]['parent_id'], $intCounter);
      }
    }
    
    /**
     * Calculates an area's depth within the hierarchy
     * NOTE: this isn't working!
     * @param $intAreaID
     * @return integer
     */
    function GetAreaDepth($intAreaID) {
      $arrDepth = $this->ResultQuery("SELECT node.name, COUNT(parent.name) AS depth ".
        "FROM {IFW_TBL_AREAS} AS node, {IFW_TBL_AREAS} AS parent ".
        "WHERE node.hier_left BETWEEN parent.hier_left AND parent.hier_right ".
        "AND node.id = $intAreaID GROUP BY node.name ORDER BY node.hier_left", 
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrDepth[0]['depth'];
    }
    
    /**
     * Gets the lowest point in the hierarchy
     * NOTE: this isn't used anywhere, and may not actually work.
     * @return integer
     */
    function GetLowestDepth() {
      $arrDepth = $this->ResultQuery("SELECT node.name, COUNT(parent.name) AS depth ".
        "FROM {IFW_TBL_AREAS} AS node, {IFW_TBL_AREAS} AS parent ".
        "WHERE node.hier_left BETWEEN parent.hier_left AND parent.hier_right ".
        "GROUP BY node.name ORDER BY depth DESC LIMIT 1", 
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrDepth[0]['depth'];
    }
    
    function GetParentedAreas($intParentID, $strAreaType) {
      // Top level areas don't have a parent
      if (!$intParentID) {
        $intParentID = 0;
      }
      // Build area type clause
      $arrAreas = $this->ResultQuery("SELECT a.* FROM {IFW_TBL_AREAS} a ".
        "WHERE parent_id = $intParentID ORDER BY area_order",
        __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreas;
    }
    
    function GetAllParentedAreas($intParentID, $strAreaType) {
      // Used for determining access to areas
      if ($strAreaType == C_AREA_CONTENT) {
        $strWhereClause = "AND node.area_type = '{C_AREA_CONTENT}'";
      } elseif ($strAreaType == "File") {
        $strWhereClause = "AND node.area_type = '{C_AREA_FILE}'";
      } else {
        $strWhereClause = "";
      }
      // SQL
      $strSQL = "SELECT node.id, node.name FROM ".
        "({IFW_TBL_AREAS} AS node, {IFW_TBL_AREAS} AS parent) ".
        "WHERE node.hier_left BETWEEN parent.hier_left AND parent.hier_right ".
        "AND parent.id = $intParentID $strWhereClause ".
        "ORDER BY node.hier_left";
      $arrAreas = $this->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      return $arrAreas;
    }
    
    // Build breadcrumbs
    function BuildBreadcrumbs($intAreaID, $blnUseLinks, $intArticleID, $intFileID) {
    	
      global $CMS;
      
      $arrArea = $CMS->AR->GetArea($intAreaID);

      $strAreaInfo = "";
      $strSQL = "SELECT parent.name, parent.id, parent.seo_name ".
        "FROM {IFW_TBL_AREAS} AS node, {IFW_TBL_AREAS} AS parent ".
        "WHERE node.hier_left BETWEEN parent.hier_left AND parent.hier_right ".
        "AND node.id = $intAreaID ".
        "ORDER BY parent.hier_left";
      //print($strSQL);
      $arrAreaPath = $this->ResultQuery($strSQL, __CLASS__ . "::" . __FUNCTION__, __LINE__);
      
      if (($intArticleID > 0) || ($intFileID > 0)) {
        $blnPage = true;
      } else {
        $blnPage = false;
      }
      
      for ($i=0; $i<count($arrAreaPath); $i++) {
        $intID      = $arrAreaPath[$i]['id'];
        $strName    = $arrAreaPath[$i]['name'];
        $strSEOName = $arrAreaPath[$i]['seo_name'];
        if ($i > 0) {
          $strAreaInfo .= " &gt; ";
        }
        if ($blnUseLinks) {
          $CMS->PL->SetTitle($strSEOName);
          if ($blnPage) {
            $strLinkURL = $CMS->PL->ViewArea($intID);
            $strAreaInfo .= "<a href=\"$strLinkURL\">$strName</a>";
          } elseif ($intID == $intAreaID) {
            $strAreaInfo .= $strName;
          } else {
            $strLinkURL = $CMS->PL->ViewArea($intID);
            $strAreaInfo .= "<a href=\"$strLinkURL\">$strName</a>";
          }
        } else {
          $strAreaInfo .= $strName;
        }
      }
      
      return $strAreaInfo;
      
    }
  }
?>