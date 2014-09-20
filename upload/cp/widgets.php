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
  $strPageTitle = "Data Widgets";
  $CMS->AP->SetTitle($strPageTitle);
  
  $arrWidgets = $CMS->WGT->GetAll();

  $strHTML = <<<MainContentStart
<h1>$strPageTitle</h1>
<p>Data Widgets retrieve information from a database and display it in a list. 
Data can be retrieved from {PRD_PRODUCT_NAME} or from other systems. 
If data is not stored in the same database as {PRD_PRODUCT_NAME}, 
you'll need to create a new Database Connection.</p>
<ul>
<li><a href="{FN_ADM_WIDGET}?action=create">Add a new Data Widget</a></li>
<li><a href="{FN_ADM_CONNECTIONS}" title="Manage connections to other databases">Manage Database Connections</a></li>
</ul>

MainContentStart;

    for ($i=0; $i<count($arrWidgets); $i++) {
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
  <tbody id="tblWidgetsBody">

TableHeader;
    }
    $intID   = $arrWidgets[$i]['id'];
    $strVar  = $arrWidgets[$i]['widget_variable'];
    $strName = $arrWidgets[$i]['name'];
    $strHTML .= <<<TableRow
    <tr>
      <td class="Left">$strName</td>
      <td class="Left">$strVar</td>
      <td class="Centre">
        <a href="{FN_ADM_WIDGET}?action=edit&amp;id=$intID">Edit</a> : 
        <!--<a href="{FN_ADM_EXPORT}?action=exportplugin&amp;id=$intID">Export</a> : -->
        <a href="{FN_ADM_WIDGET}?action=delete&amp;id=$intID">Delete</a></td>
    </tr>

TableRow;
    }
  if (count($arrWidgets) > 0) {
    $strHTML .= "  </tbody>\n</table>\n";
  } else {
    $strHTML .= "<p>You currently have no Data Widgets installed.</p>\n";
  }

  $CMS->AP->Display($strHTML);
?>