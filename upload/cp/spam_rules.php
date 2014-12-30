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

  require '../sys/header.php';
  $CMS->RES->Admin();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
  }
  $strPageTitle = "Spam Rules";

  $CMS->AP->SetTitle($strPageTitle);
  
  $strHTML = <<<PageBody
<h1 class="page-header">$strPageTitle</h1>
<p><a href="spam_rule.php?action=create">Add Spam Rule</a></p>

PageBody;
  
  $arrSpamRules = $CMS->SR->GetAll();
  
  $strHTML .= <<<TableHeader
<div class="table-responsive">
<table class="table table-striped" style="width: 600px;">
  <tr class="separator-row">
    <td>Rule</td>
    <td>Type</td>
    <td>Options</td>
  </tr>

TableHeader;
      
  for ($i=0; $i<count($arrSpamRules); $i++) {
  	
  	$intID   = $arrSpamRules[$i]['id'];
  	$strRule = $arrSpamRules[$i]['block_rule'];
  	$strType = $arrSpamRules[$i]['block_type'];
  	$strHTML .= <<<TableData
  	
  <tr>
    <td>$strRule</td>
    <td>$strType</td>
    <td>
      <a href="{FN_ADM_SPAM_RULE}?action=edit&amp;id=$intID">Edit</a> : 
      <a href="{FN_ADM_SPAM_RULE}?action=delete&amp;id=$intID">Delete</a>
    </td>
  </tr>
  	
TableData;
  	
  }
  
  $strHTML .= <<<TableFooter
</table>
</div>

TableFooter;
  
  $CMS->AP->Display($strHTML);
