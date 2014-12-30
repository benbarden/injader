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
  $CMS->RES->ValidateLoggedIn();
  if ($CMS->RES->IsError()) {
    $CMS->Err_MFail(M_ERR_NOT_LOGGED_IN, "");
  }
  $intCurrentUserID = $CMS->RES->GetCurrentUserID();
  $strPageTitle = "Manage Avatars";
  $CMS->AP->SetTitle($strPageTitle);
  
  $arrCurrentAvatar = $CMS->ResultQuery("SELECT avatar_id FROM {IFW_TBL_USERS} WHERE id = $intCurrentUserID", basename(__FILE__), __LINE__);
  $intCurrentAvatarID = $arrCurrentAvatar[0]['avatar_id'];
  if ($intCurrentAvatarID) {
    $strNoAvatarText = "<p><a href=\"{FN_USER_TOOLS}?action=clearavatar\">Don't use an avatar</a>.</p>\n\n";
  } else {
    $strNoAvatarText = "<p>You do not have an avatar set. To use an avatar, click on \"Set as avatar\" beneath the image you wish to use.</p>\n\n";
  }
  
  $strHTML = <<<MainContentStart
<h1 class="page-header">$strPageTitle</h1>
<p>This page displays all of your avatars. <a href="{FN_ADM_UPLOAD_AVATAR}">Upload a new avatar</a></p>
$strNoAvatarText

MainContentStart;

  $arrAvatars = $CMS->ResultQuery("SELECT * FROM {IFW_TBL_UPLOADS} WHERE author_id = $intCurrentUserID AND is_avatar = 'Y'", basename(__FILE__), __LINE__);
	for ($i=0; $i<count($arrAvatars); $i++) {
    $intID = $arrAvatars[$i]['id'];
    if ($i == 0) {
      $strHTML .= <<<TableHeader
<div class="table-responsive">
<table class="table table-striped" style="width: 400px;">

TableHeader;
    }
    if (($i % 5) == 0) {
      $strHTML .= "  <tr>\n";
    }
    $strHTML .= "    <td><img src=\"{FN_FILE_DOWNLOAD}?id=$intID\" alt=\"Avatar\" /><br />";
    if ($intID == $intCurrentAvatarID) {
      $strHTML .= "This is your avatar";
    } else {
      $strHTML .= "<a href=\"{FN_USER_TOOLS}?action=setavatar&amp;id=$intID\">Set as avatar</a>";
    }
    $strHTML .= " : <a href=\"{FN_USER_TOOLS}?action=deleteavatar&amp;id=$intID&amp;back={FN_ADM_MANAGE_AVATARS}\">Delete</a>";
		$strHTML .= "</td>\n";
    if ((($i+1) % 5) == 0) {
      $strHTML .= "  </tr>\n";
    }
	}
  if (($i > 0) && ($i < 5)) {
    $strHTML .= "  </tr>\n";
  } elseif (($i % 5) != 0) {
    $j = 0;
    do {
      $strHTML .= "    <td>&nbsp;</td>\n";
      $i++;
      $j++;
    } while ((($i % 5) != 0) && ($j < 5));
    $strHTML .= "  </tr>\n";
  }
  
  if ($i > 0) {
    $strHTML .= "</table></div>\n";
  }

  $CMS->AP->Display($strHTML);
