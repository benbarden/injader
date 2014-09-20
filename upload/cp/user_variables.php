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
  $strPageTitle = "User Variables";
  $CMS->AP->SetTitle($strPageTitle);
  
  $arrVariables = $CMS->UV->GetAll();

  $strHTML = <<<MainContentStart
<h1>$strPageTitle</h1>
<p>User Variables can be used to store HTML for a theme. This may be useful for third party widgets. If you need to edit the code in future, you just edit the variable - no need to change your theme. For more information, consult the Help Docs.</p>
<ul>
<li><a href="{FN_ADM_USER_VARIABLE}?action=create">Add a new User Variable</a></li>
</ul>

MainContentStart;

	for ($i=0; $i<count($arrVariables); $i++) {
    if ($i==0) {
      $strHTML .= <<<TableHeader
<table id="tblSysResults" class="DefaultTable WideTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour" />
    <col class="BaseColour MediumCell" />
    <col class="BaseColour MediumCell" />
  </colgroup>
  <thead>
    <tr>
      <th>Name</th>
      <th>Variable</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody id="tblPluginsBody">

TableHeader;
    }
    $intID   = $arrVariables[$i]['id'];
    $strVar  = $arrVariables[$i]['user_variable'];
    $strName = $arrVariables[$i]['name'];
    $strHTML .= <<<TableRow
    <tr>
      <td class="Left">$strName</td>
      <td class="Left">$strVar</td>
      <td class="Centre"><a href="{FN_ADM_USER_VARIABLE}?action=edit&amp;id=$intID">Edit</a> : <a href="{FN_ADM_USER_VARIABLE}?action=delete&amp;id=$intID">Delete</a></td>
    </tr>

TableRow;
	}
  if (count($arrVariables) > 0) {
    $strHTML .= "  </tbody>\n</table>\n";
  } else {
    $strHTML .= "<p>You currently have no User Variables.</p>\n";
  }

  $CMS->AP->Display($strHTML);
?>