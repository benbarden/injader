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
   * Drives the file-based cache
   * @author Ben Barden
   */
  class ICacheFile extends Helper {
    
  	/**
  	 * Creates a new file in the cache folder
  	 * @param $strName - the name of the file to be created
  	 * @param $strContents - the data to save into the file
  	 * @return error text if unsuccessful, else boolean(true)
  	 */
  	function Create($strName, $strContents) {
  		
  		if ($this->Exists($strName)) {
  			$this->Delete($strName);
  			//return "Error creating cache item - file already exists: $strName";
  		}
  		
  		return $this->Write($strName, $strContents, "w");
  		
  	}
  	
  	/**
  	 * Verifies whether a file exists
  	 * @param $strName
  	 * @return boolean
  	 */
  	function Exists($strName) {
  		
      return file_exists(ABS_CACHE.$strName);
  		
  	}
  	
  	/**
  	 * Deletes a file from the cache
  	 * @param $strName
  	 * @return error text if unsuccessful, else boolean(true)
  	 */
  	function Delete($strName) {
  		
  		if (!$this->Exists($strName)) {
  			return "File does not exist: $strName";
  		}
  		
  		@ $blnDelete = unlink(ABS_CACHE.$strName);
  		if (!$blnDelete) {
  			return "Error deleting file: $strName";
  		}
  		
  		return true;
  		
  	}
  	
  	/**
  	 * Writes to an existing cache item, creating it if necessary
  	 * @param $strName
  	 * @param $strContents
  	 * @param $strMode (w = write, a = append)
  	 * @return error text if unsuccessful, else boolean(true)
  	 */
  	function Save($strName, $strContents, $strMode = "w") {
  		
  		if (!$this->Exists($strName)) {
  			$blnCreate = $this->Create($strName, $strContents);
  			if ($blnCreate == true) {
  				return true;
  			} else {
  				return $blnCreate;
  			}
  		}
  		
  		return $this->Write($strName, $strContents, $strMode);
  		
  	}
  	
  	/**
  	 * Writes to a file
  	 * @param $strName
  	 * @param $strContents
  	 * @return unknown_type
  	 */
  	function Write($strName, $strContents, $strMode = "w") {
  		
      @ $blnTouch = touch(ABS_CACHE.$strName);
      if (!$blnTouch) {
        return "Unable to touch() file: $strName - check the permissions on the cache folder";
      }
      
      if ($strMode == "a") {
	      $strExistingContents = $this->Load($strName);
	      if (!empty($strExistingContents)) {
	      	$strContents = "\r\n".$strContents;
	      }
      }
      
      @ $objFile = fopen(ABS_CACHE.$strName, $strMode);
      if (!$objFile) {
        @ fclose($objFile);
        return "Unable to open file: $strName";
      }
      
      @ $blnWrite = fwrite($objFile, $strContents);
      if (!$blnWrite) {
        @ fclose($objFile);
        return "Unable to write to file: $strName";
      }
      
      @ $blnClose = fclose($objFile);
      if (!$blnClose) {
        return "Unable to close file: $strName";
      }
      
      return true;
      
  	}
  	
  	/**
  	 * Loads the contents of a file
  	 * @param $strName
  	 * @return the contents of the file if successful, else boolean(false)
  	 */
  	function Load($strName) {
  		
  		if (!$this->Exists($strName)) {
        return "File does not exist: $strName";
    	}
    	
  		@ $strData = file_get_contents(ABS_CACHE.$strName);
  		return $strData;
  		
  	}
  	
  }

?>