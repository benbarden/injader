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
  $strPageTitle = "Form Recipients";

  $CMS->AP->SetTitle($strPageTitle);

  $strNewButton = $CMS->AC->LocationButton(M_BTN_ADD_RECIPIENT, "{FN_ADM_FORMS_RECIPIENT}?action=create");

  $strHTML = <<<END
<h1>$strPageTitle</h1>
<p>Form recipients allow visitors to choose who they wish to send a form to, without revealing the recipient&apos;s email address.</p>
<table id="tblSysResults" class="DefaultTable WideTable CentreTable" cellspacing="1">
  <colgroup>
    <col class="BaseColour TinyCell" />
    <col class="BaseColour TinyCell" />
    <col class="BaseColour" />
    <col class="BaseColour" />
    <col class="BaseColour NarrowCell" />
  </colgroup>
  <thead>
    <tr>
      <th>ID</th>
      <th>Order</th>
      <th>Name</th>
      <th>Email</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody id="tblRecipientsBody">

END;

  $arrRecipients = $CMS->FR->GetAll();
  for ($i=0; $i<count($arrRecipients); $i++) {
    $intID    = $arrRecipients[$i]['id'];
    $strName  = $arrRecipients[$i]['name'];
    $strEmail = $arrRecipients[$i]['email'];
    $intOrder = $arrRecipients[$i]['recipient_order'];
    $strHTML .= <<<TableRow
    <tr>
      <td class="Centre id">$intID</td>
      <td class="Centre order">$intOrder</td>
      <td class="Centre name">$strName</td>
      <td class="Left email">$strEmail</td>
      <td class="Centre options"><a href="{FN_ADM_FORMS_RECIPIENT}?action=edit&amp;id=$intID">Edit</a> <a href="{FN_ADMIN_TOOLS}?action=deleteformrecipient&amp;id=$intID&amp;back={FN_ADM_FORMS_RECIPIENTS}">Delete</a></td>
    </tr>

TableRow;
  }
  
  $strHTML .= <<<END
    <tr>
      <td class="FootColour SpanCell Centre" colspan="99">$strNewButton</td>
    </tr>
  </tbody>
</table>

END;

  $CMS->AP->Display($strHTML);
?>