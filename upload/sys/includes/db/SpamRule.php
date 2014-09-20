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

  class SpamRule extends Helper {
    
  	/**
  	 * Adds a new spam rule
  	 * @param $strBlockRule
  	 * @param $strBlockType
  	 * @return void
  	 */
  	function Create($strBlockRule, $strBlockType) {
  		
  		$strQuery = sprintf("
        INSERT INTO {IFW_TBL_SPAM_RULES}(block_rule, block_type)
        VALUES('%s', '%s')
  		",
  		  mysql_real_escape_string($strBlockRule),
  		  mysql_real_escape_string($strBlockType)
  		);
  		$this->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
  		
  	}
  	
  	/**
  	 * Updates the content of a spam rule
  	 * @param $intRuleID
  	 * @param $strBlockRule
  	 * @return void
  	 */
  	function Update($intRuleID, $strBlockRule, $strBlockType) {
  		
      $strQuery = sprintf("
        UPDATE {IFW_TBL_SPAM_RULES}
        SET block_rule = '%s',
        block_type = '%s'
        WHERE id = %s
      ",
        mysql_real_escape_string($strBlockRule),
        mysql_real_escape_string($strBlockType),
        $intRuleID
      );
      $this->Query($strQuery, __CLASS__ . "::" . __FUNCTION__, __LINE__);
  		
  	}
  	
  	/**
  	 * Deletes a spam rule
  	 * @param $intRuleID
  	 * @return void
  	 */
  	function Delete($intRuleID) {
  		
  		$this->Query("
  		  DELETE FROM {IFW_TBL_SPAM_RULES}
  		  WHERE id = $intRuleID
  		");
  		
  	}
  	
  	/**
  	 * Gets a spam rule
  	 * @param $intRuleID
  	 * @return array
  	 */
  	function Get($intRuleID) {
  		
  		$arrResult = $this->ResultQuery("
  		  SELECT * FROM {IFW_TBL_SPAM_RULES}
  		  WHERE id = $intRuleID
  		");
  		return $arrResult[0];
  		
  	}
  	
  	/**
  	 * Gets all the spam rules
  	 * @return array
  	 */
  	function GetAll() {
      
  		$arrResult = $this->ResultQuery("
				SELECT * FROM {IFW_TBL_SPAM_RULES}
				ORDER BY block_type ASC, block_rule ASC
  		");
  		return $arrResult;
  		
  	}
  	
  	/**
  	 * Verifies whether any of the spam rules should be triggered by this comment
  	 * @param $strName
  	 * @param $strEmail
  	 * @param $strURL
  	 * @param $strContent
  	 * @param $strIP
  	 * @return boolean
  	 */
  	function MatchAll($strName, $strEmail, $strURL, $strContent, $strIP) {
      
  		$strURL = str_replace("http://", "", $strURL);
      
      $arrResult = $this->GetAll();
      
      $blnMatch = false;
      
      for ($i=0; $i<count($arrResult); $i++) {
      	
        $strBlockRule = $arrResult[$i]['block_rule'];
        $strBlockType = $arrResult[$i]['block_type'];
        
        switch ($strBlockType) {
        	
        	case C_SPAMRULE_NAME:    $strData = $strName;    break;
        	case C_SPAMRULE_EMAIL:   $strData = $strEmail;   break;
        	case C_SPAMRULE_URL:     $strData = $strURL;     break;
        	case C_SPAMRULE_COMMENT: $strData = $strContent; break;
        	case C_SPAMRULE_IP:      $strData = $strIP;      break;
        	default: return false; break;
        		
        }
        
				if (preg_match("/$strBlockRule/i", $strData)) {
					$blnMatch = true;
				}
				
        if ($blnMatch) {
        	break;
        }
        
      }
      
      return $blnMatch;
      
  	}
  	
  }

?>