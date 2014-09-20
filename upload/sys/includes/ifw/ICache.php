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

  /**
   * Drives the memory cache
   * @author Ben Barden
   */
  class ICache extends Helper {

  	var $arrCacheData;
  	
  	/**
  	 * Adds an item to the cache
  	 * @param $strKey
  	 * @param $strValue
  	 * @return void if successful, false is a value is missing
  	 */
  	function Add($strKey, $strValue) {
  		
  		if ((empty($strKey)) || (empty($strValue))) {
  			return false;
  		}
  		
  		$this->arrCacheData[$strKey] = $strValue;
  		
  	}
  	
  	/**
  	 * Deletes an item from the cache
  	 * @param $strKey
  	 * @return void if successful, null if the action could not complete
  	 */
  	function Delete($strKey) {
  		
      if (!is_array($this->arrCacheData)) {
        return null;
      }
      
  		if (array_key_exists($strKey, $this->arrCacheData)) {
        unset($this->arrCacheData[$strKey]);
      } else {
        return null;
      }
      
  	}
  	
  	/**
  	 * Verifies whether a cache item exists
  	 * @param $strKey
  	 * @return null if unsuccessful or the key does not exist, boolean(true) if it exists
  	 */
  	function Exists($strKey) {
  		
      if (empty($strKey)) {
        return null;
      }
      
      if (!is_array($this->arrCacheData)) {
        return null;
      }
      
      if (array_key_exists($strKey, $this->arrCacheData)) {
        return true;
      } else {
        return null;
      }
      
  	}
  	
  	/**
  	 * Retrieves an item from the cache
  	 * @param $strKey
  	 * @return null if unsuccessful, otherwise the value is returned
  	 */
  	function Get($strKey) {
  		
  		if (empty($strKey)) {
  			return null;
  		}
  		
  		if (!is_array($this->arrCacheData)) {
  			return null;
  		}
  		
  		if (array_key_exists($strKey, $this->arrCacheData)) {
  			return $this->arrCacheData[$strKey];
  		} else {
  			return null;
  		}
  		
  	}
  	
  	/**
  	 * Verifies whether a cache item matches a specified value
  	 * @param $strKey
  	 * @param $strValue
  	 * @return null if unsuccessful, boolean(true) if matched, else boolean(false)
  	 */
  	function Match($strKey, $strValue) {
  		
  		if (empty($strKey)) {
  			return null;
  		}
  		
  		if (!is_array($this->arrCacheData)) {
  			return null;
  		}
  		
  		if (!array_key_exists($strKey, $this->arrCacheData)) {
  			return null;
  		}
  		
  		if ($this->arrCacheData[$strKey] == $strValue) {
  			return true;
  		} else {
  			return false;
  		}
  		
  	}
  	
  	/**
  	 * Clears the file-based cache completely
  	 * @return void
  	 */
  	function ClearAll() {
  		
  		global $CMS;
  		
  		$CMS->CacheFile->Delete("System_SysPrefs");
  		$CMS->CacheFile->Delete("AreaTraverse_".C_NAV_PRIMARY);
      $CMS->CacheFile->Delete("AreaTraverse_".C_NAV_SECONDARY);
      $CMS->CacheFile->Delete("AreaTraverse_".C_NAV_TERTIARY);
      
  	}
  	
  }

?>