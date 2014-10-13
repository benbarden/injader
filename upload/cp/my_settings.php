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
    $CMS->RES->ValidateLoggedIn();
    if ($CMS->RES->IsError()) {
        $CMS->Err_MFail(M_ERR_NOT_LOGGED_IN, "");
    }
    $strPageTitle = "My Settings";

    $CMS->AP->SetTitle($strPageTitle);

    // ** Settings ** //
    // Profile links
    $intUserID = $CMS->RES->GetCurrentUserID();
    $strViewMyProfile = $CMS->PL->ViewUser($intUserID);

    // Are avatars allowed?
    if ($CMS->SYS->GetSysPref(C_PREF_AVATARS_PER_USER) > 0) {
        $strManageAvatars = "<li><a href=\"{FN_ADM_MANAGE_AVATARS}\">Manage Avatars</a></li>";
    } else {
        $strManageAvatars = "";
    }
    // Can users change their password?
    if ($CMS->SYS->GetSysPref(C_PREF_USER_CHANGE_PASS) == "Y") {
        $strChangePass = "<li><a href=\"{FN_ADM_CHANGE_PASSWORD}\">Change Password</a></li>";
    } else {
        $strChangePass = "";
    }

    $strHTML = <<<END
<h1 class="page-header">My Settings</h1>

<ul>
<li><a href="{FN_ADM_EDIT_PROFILE}">Edit Profile</a></li>
<li><a href="$strViewMyProfile">View Profile</a></li>
$strChangePass
$strManageAvatars
</ul>

END;
  
    $CMS->AP->Display($strHTML);
