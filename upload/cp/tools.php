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
  $strPageTitle = "Tools";

  $CMS->AP->SetTitle($strPageTitle);

  $strHTML = <<<END
<h1 class="page-header">Tools</h1>

<ul>
<li><a href="{FN_ADM_SPAM_RULES}" title="Spam Rules">Spam Rules</a></li>
<li><a href="{FN_ADM_TOOLS_SPAM_STATS}" title="Comment Spam Stats">Comment Spam Stats</a></li>
<li><a href="{FN_ADM_TOOLS_USER_SESSIONS}" title="View logged in users, force a logout">User Sessions</a></li>
<li><a href="{FN_ADM_ACCESS_LOG}" title="View recent activity on your site">Access Log</a></li>
<li><a href="{FN_ADM_ERROR_LOG}" title="View errors that have occurred on your site">Error Log</a></li>
</ul>

END;
  
  $CMS->AP->Display($strHTML);
