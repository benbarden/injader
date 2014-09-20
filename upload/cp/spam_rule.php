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
  
  $strAction = empty($_GET['action']) ? "" : $_GET['action'];
  $blnCheckID = true;
  $blnCreate = false; $blnEdit = false; $blnDelete = false;
  switch ($strAction) {
  	case "create": $blnCreate = true; $strPageTitle = "Create Spam Rule"; $blnCheckID = false; break;
  	case "edit":   $blnEdit   = true; $strPageTitle = "Edit Spam Rule";   break;
  	case "delete": $blnDelete = true; $strPageTitle = "Delete Spam Rule"; break;
    default: $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");    break;
  }
  
  $intRuleID = ""; $strBlockRule = ""; $strBlockType = ""; $strMissingRule = "";
  
  if ($blnCheckID) {
    $intRuleID = $CMS->FilterNumeric($_GET['id']);
    if (!$intRuleID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
  }
  
  if ($_POST) {
  	
  	$blnSubmitForm = true;
  	if (!$blnDelete) {
  		$strBlockRule = empty($_POST['txtBlockRule']) ? "" : strip_tags($_POST['txtBlockRule']);
  		if (!$strBlockRule) {
  			$blnSubmitForm = false;
  			$strMissingRule = $CMS->AC->InvalidFormData("");
  		}
  		$strBlockType = $_POST['optBlockType'];
  	}
  	
  	if ($blnSubmitForm) {
  		
	  	if ($blnCreate) {
	  		$CMS->SR->Create($strBlockRule, $strBlockType); $strDidWhat = "created";
	  	} elseif ($blnEdit) {
	  		$CMS->SR->Update($intRuleID, $strBlockRule, $strBlockType); $strDidWhat = "updated";
	  	} elseif ($blnDelete) {
	  		$CMS->SR->Delete($intRuleID); $strDidWhat = "deleted";
	  	}
	  	
	  	$strHTML = <<<ConfPage
<h1>$strPageTitle - Results</h1>
<p>Spam rule $strDidWhat. <a href="{FN_ADM_SPAM_RULES}">Spam Rules</a></p>

ConfPage;
	  	
	  	$CMS->AP->SetTitle($strPageTitle);
	  	$CMS->AP->Display($strHTML);
	  	
  	}
  	
  } else {
  	
  	if ($blnCheckID) {
  		
  		$arrRuleData = $CMS->SR->Get($intRuleID);
  	  if (count($arrRuleData) == 0) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Spam Rule: $intRuleID");
      }
  		$strBlockRule = $arrRuleData['block_rule'];
  		$strBlockType = $arrRuleData['block_type'];
      
  	}
  	
  }
  
  $CMS->AP->SetTitle($strPageTitle);
  
  $strTypeList = $CMS->DD->SpamRuleType($strBlockType);
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->LocationButton("Cancel", FN_ADM_SPAM_RULES);
  
  // ** BUILD FORM HTML ** //
  
  if ($blnDelete) {
  	
  	$strHTML = <<<PageBody
<h1>$strPageTitle</h1>
<p>You are about to delete the following Spam Rule: $strBlockRule (ID: $intRuleID)</p>
<form action="{FN_ADM_SPAM_RULE}?action=$strAction&amp;id=$intRuleID" method="post">
<input type="hidden" name="dummy" value="dummy" />
<p>$strSubmitButton $strCancelButton</p>
</form>

PageBody;
    
  } else {
  
  $strHTML = <<<PageBody
<h1>$strPageTitle</h1>
<form action="{FN_ADM_SPAM_RULE}?action=$strAction&amp;id=$intRuleID" method="post">
<table class="DefaultTable WideTable FixedTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour NarrowCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td><label for="txtBlockRule">Block Rule:</label></td>
    <td>
      $strMissingRule
      <input type="text" id="txtBlockRule" name="txtBlockRule" maxlength="255" 
       size="60" value="$strBlockRule" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="optBlockType">Block Type:</label>
    </td>
    <td>
      <select id="optBlockType" name="optBlockType">
$strTypeList
      </select>
    </td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</form>

PageBody;
    
  }
  
  $CMS->AP->Display($strHTML);
?>