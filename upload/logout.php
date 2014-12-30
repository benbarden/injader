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

  require 'sys/header.php';
  $CMS->RES->ValidateLoggedIn();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_ALREADY_LOGGED_OUT, "");
  }
  $CMS->US->Logout($CMS->RES->GetCurrentUserID());
  $strReferrer = empty($_SERVER['HTTP_REFERER']) ? "" : $_SERVER['HTTP_REFERER'];
  if ($strReferrer) {
    // Fix broken page number links
    if ((strpos($strReferrer, "?") === false) && (strpos($strReferrer, "&") !== false)) {
      $strReferrer = str_replace("&", "?", $strReferrer);
    }
    $strGoBack = "<li><a href=\"$strReferrer\">Go back to the page you were just viewing</a></li>";
  } else {
    $strGoBack = "";
  }
      // ** Display results ** //
      $strHTML = <<<END
<h1>Logged out</h1>
<p>You have successfully logged out.</p>
<ul>
$strGoBack
<li><a href="{FN_INDEX}">Go to the home page</a></li>
</ul>

END;
  $CMS->LP->SetTitle("Logged out");
  $CMS->LP->Display($strHTML);
?>