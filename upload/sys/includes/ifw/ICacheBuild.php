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

  /**
   * Combines other caching classes to build the Injader cache
   * @author Ben Barden
   */
  class ICacheBuild extends Helper {
    
    /**
     * Builds the cache for a given class
     * @param $strClassName
     * @return boolean
     */
    function ClassBuild($strClassName) {
      
      global $CMS;
      
    	if (in_array($strClassName, get_declared_classes()) == false) {
    		return false;
    	}
    	
    	$arrClassVars = get_class_vars($strClassName);
    	$strBuildData = serialize($arrClassVars);
    	
    	return $CMS->CacheFile->Save($strClassName, $strBuildData, "w");
    	
    }
    
    /**
     * Builds the cache for a given array
     * @param $strCacheName
     * @param $arrData
     * @return boolean
     */
    function ArrayBuild($strCacheName, $arrData) {
    	
    	global $CMS;
    	
    	if (!is_array($arrData)) {
    		return false;
    	}
    	
    	$strData = serialize($arrData);
    	
    	$CMS->Cache->Add($strCacheName, $strData);
    	
    	return $CMS->CacheFile->Save($strCacheName, $strData, "w");
    	
    }
    
    /**
     * Gets an array from the cache
     * @param $strCacheName
     * @return unknown_type
     */
    function ArrayGet($strCacheName) {
    	
    	global $CMS;
    	
    	if (!$CMS->Cache->Exists($strCacheName)) {
    		$strData = $CMS->CacheFile->Load($strCacheName);
    		$CMS->Cache->Add($strCacheName, $strData);
    	}
    	
    	return unserialize($CMS->Cache->Get($strCacheName));
    	
    }
    
  }

?>