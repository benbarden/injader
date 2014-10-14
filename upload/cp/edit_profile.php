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
  $strPageTitle = "Edit Profile";
  $intUserID = $CMS->RES->GetCurrentUserID();
  
  if ($_POST) {
    $strPageTitle .= " - Results";
    $CMS->AP->SetTitle($strPageTitle);
    $strAllowedChars = " .,'!?:;\/@";
    $strForename   = $CMS->FilterAlphanumeric($_POST['txtForename'], $strAllowedChars);
    $strForename   = $CMS->AddSlashesIFW($strForename);
    $strEmail      = $CMS->FilterAlphanumeric($_POST['txtEmail'], $strAllowedChars);
    $strEmail      = $CMS->AddSlashesIFW($strEmail);
    $strSurname    = $CMS->FilterAlphanumeric($_POST['txtSurname'], $strAllowedChars);
    $strSurname    = $CMS->AddSlashesIFW($strSurname);
    $strLocation   = $CMS->FilterAlphanumeric($_POST['txtLocation'], $strAllowedChars);
    $strLocation   = $CMS->AddSlashesIFW($strLocation);
    $strOccupation = $CMS->FilterAlphanumeric($_POST['txtOccupation'], $strAllowedChars);
    $strOccupation = $CMS->AddSlashesIFW($strOccupation);
    $strInterests  = $CMS->FilterAlphanumeric($_POST['txtInterests'], $strAllowedChars);
    $strInterests  = $CMS->AddSlashesIFW($strInterests);
    $strHomeLink   = $CMS->FilterAlphanumeric($_POST['txtHomeLink'], $strAllowedChars);
    $strHomeLink   = $CMS->AddSlashesIFW($strHomeLink);
    $strHomeText   = $CMS->FilterAlphanumeric($_POST['txtHomeText'], $strAllowedChars);
    $strHomeText   = $CMS->AddSlashesIFW($strHomeText);
    $CMS->US->EditProfile($intUserID, $strForename, $strSurname, $strEmail, $strLocation, $strOccupation, $strInterests, $strHomeLink, $strHomeText);
    $strViewUser = $CMS->PL->ViewUser($intUserID);
    $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Profile updated successfully. <a href=\"{FN_ADM_INDEX}\">Control Panel</a> : <a href=\"$strViewUser\">View your profile</a></p>\n";
    $CMS->AP->Display($strHTML);
  }
  $CMS->AP->SetTitle($strPageTitle);

  $arrUserProfile = $CMS->US->Get($intUserID);
  $userName       = $arrUserProfile['username'];
  $userForename   = $arrUserProfile['forename'];
  $userSurname    = $arrUserProfile['surname'];
  $userEmail      = $arrUserProfile['email'];
  $userLoc        = $arrUserProfile['location'];
  $userOccupation = $arrUserProfile['occupation'];
  $userInterests  = $arrUserProfile['interests'];
  $userHomeLink   = $arrUserProfile['homepage_link'];
  $userHomeText   = $arrUserProfile['homepage_text'];

  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->CancelButton();

  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<form action="{FN_ADM_EDIT_PROFILE}" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <tr>
    <td><strong>Username:</strong></td>
    <td>
      $userName
      <input type="hidden" name="txtUserID" value="$intUserID" />
    </td>
  </tr>
  <tr>
    <td><label for="txtForename">Forename:</label></td>
    <td><input type="text" id="txtForename" name="txtForename" value="$userForename" maxlength="45" size="45" /></td>
  </tr>
  <tr>
    <td><label for="txtSurname">Surname:</label></td>
    <td><input type="text" id="txtSurname" name="txtSurname" value="$userSurname" maxlength="45" size="45" /></td>
  </tr>
  <tr>
    <td><label for="txtLocation">Location:</label></td>
    <td><input type="text" id="txtLocation" name="txtLocation" value="$userLoc" maxlength="100" size="45" /></td>
  </tr>
  <tr>
    <td><label for="txtOccupation">Occupation:</label></td>
    <td><input type="text" id="txtOccupation" name="txtOccupation" value="$userOccupation" maxlength="100" size="45" /></td>
  </tr>
  <tr>
    <td><label for="txtInterests">Interests:</label></td>
    <td><textarea id="txtInterests" name="txtInterests" rows="4" cols="34">$userInterests</textarea></td>
  </tr>
  <tr>
    <td><label for="txtHomeLink">Homepage URL:</label></td>
    <td><input type="text" id="txtHomeLink" name="txtHomeLink" value="$userHomeLink" maxlength="150" size="45" /></td>
  </tr>
  <tr>
    <td><label for="txtHomeText">Homepage Text:</label></td>
    <td><input type="text" id="txtHomeText" name="txtHomeText" value="$userHomeText" maxlength="100" size="45" /></td>
  </tr>
  <tr>
    <td><label for="txtEmail">Email:</label></td>
    <td><input type="text" id="txtEmail" name="txtEmail" value="$userEmail" maxlength="100" size="45" /></td>
  </tr>
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>
</table>
</div>
</form>

END;
  $CMS->AP->Display($strHTML);
