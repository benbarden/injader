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
  $strPageTitle = "Database Connections";
  $CMS->AP->SetTitle($strPageTitle);
  
  $arrConn = $CMS->CON->GetAll();

  $strHTML = <<<MainContentStart
<h1>$strPageTitle</h1>
<p>Database Connections are primarily used by Custom Data Plugins so you can access information in other databases. You do not need to create a connection for any information that is stored in your {PRD_PRODUCT_NAME} database.</p>
<p>Small sites may benefit from installing multiple systems into one database as {PRD_PRODUCT_NAME} will not need to disconnect and reconnect. This may not be feasible for large sites as some systems may use up a lot of space in your database.</p>
<p><a href="{FN_ADM_CONNECTION}?action=create">Add a new connection</a>.</p>

MainContentStart;

	for ($i=0; $i<count($arrConn); $i++) {
    if ($i==0) {
      $strHTML .= <<<TableHeader
<table id="tblSysResults" class="DefaultTable MediumTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour" />
    <col class="BaseColour MediumCell" />
  </colgroup>
  <thead>
    <tr>
      <th>Name</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody id="tblPluginsBody">

TableHeader;
    }
    $intID   = $arrConn[$i]['id'];
    $strName = $arrConn[$i]['conn_name'];
    $strHTML .= <<<TableRow
    <tr>
      <td class="Left">$strName</td>
      <td class="Centre"><a href="{FN_ADM_CONNECTION}?action=edit&amp;id=$intID">Edit</a></td>
    </tr>

TableRow;
	}
  if (count($arrConn) > 0) {
    $strHTML .= "  </tbody>\n</table>\n";
  } else {
    $strHTML .= "<p>You currently have no connections.</p>\n";
  }

  $CMS->AP->Display($strHTML);
?>