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
  $intUserID = $CMS->RES->GetCurrentUserID();
  
  $blnCheckID = true;
  $strAction = $_GET['action'];
  switch ($strAction) {
    case "deletefile":            $strPageTitle = "Delete File"; break;
    case "suspenduser":           $strPageTitle = "Suspend User"; break;
    case "reinstateuser":         $strPageTitle = "Reinstate User"; break;
    case "deletecustomlink":      $strPageTitle = "Delete Custom Link"; break;
    case "deleteperprofile":      $strPageTitle = "Delete Permission Profile"; break;
    case "deletesession":         $strPageTitle = "Delete User Session"; break;
    case "deleteexpiredsessions": $strPageTitle = "Delete Expired Sessions"; $blnCheckID = false; break;
    case "applytheme":
      $strPageTitle = "Apply Theme";
      $strTheme = empty($_GET['theme']) ? "" : $_GET['theme'];
      if (!$strTheme) {
        $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "theme");
      }
      $blnCheckID = false;
      break;
    default: $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction"); break;
  }
  
  if ($blnCheckID) {
    $intItemID = $CMS->FilterNumeric($_GET['id']);
    if (!$intItemID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
  } else {
    $intItemID = "";
  }
  
  $strReturnURL = empty($_GET['back']) ? "" : $_GET['back'];
  
  /* stuff to do later
  if (empty($_GET['back'])) {
    $strReturnURL = FN_ADM_INDEX;
  } else {
    $strFullURL = $_SERVER['REQUEST_URI'];
    $intPos = strpos($strFullURL, "back=") + strlen("back=");
    $strReturnURL = substr($strFullURL, $intPos);
  }
  */
  
  $CMS->AP->SetTitle($strPageTitle);

  $strSubmitButton = $CMS->AC->Submit(M_BTN_PROCEED);
  $strCancelButton = $CMS->AC->CancelButton();
  $intUserID = $CMS->RES->GetCurrentUserID();
  if ($blnCheckID) {
    $strFormAction = "?action=$strAction&amp;id=$intItemID&amp;back=$strReturnURL";
  } elseif ($strAction == "applytheme") {
    $strFormAction = "?action=$strAction&amp;theme=$strTheme&amp;back=$strReturnURL";
  } else {
    $strFormAction = "?action=$strAction&amp;back=$strReturnURL";
  }
  // ** Begin custom fields ** //
  $strCustomFields = "";
  $strName         = "";
  $strNameField    = "";
  $strNameBlank    = "";
  // ** End custom fields ** //
  $blnUCPRedirect = false;
  switch ($strAction) {
    case "deletefile":
      if ($_POST) {
        $CMS->FL->Delete($intItemID, $intUserID, "");
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>File deleted. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to delete the file with ID: $intItemID.";
      }
      break;
    case "suspenduser":
      if ($_POST) {
        $CMS->US->Suspend($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>User suspended. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to suspend the user with ID: $intItemID.";
      }
      break;
    case "reinstateuser":
      if ($_POST) {
        $CMS->US->Reinstate($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>User reinstated. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to reinstate the user with ID: $intItemID.";
      }
      break;
    case "deleteperprofile":
      if ($_POST) {
        $CMS->PP->Delete($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Permission profile deleted. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to delete the permission profile with ID: $intItemID.";
      }
      break;
    case "deletesession":
      if ($_POST) {
        $CMS->USess->Delete($intItemID);
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>User session deleted. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to delete the user session with ID: $intItemID.";
      }
      break;
    case "deleteexpiredsessions":
      if ($_POST) {
        $CMS->USess->DeleteAllExpiredSessions();
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>All expired user sessions deleted. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to delete all expired user sessions.";
      }
      break;
    case "applytheme":
      if ($_POST) {
        if ($CMS->SYS->GetSysPref(C_PREF_DEFAULT_THEME) != $strTheme) {
          $CMS->SYS->WriteSysPref(C_PREF_DEFAULT_THEME, $strTheme);
        }
        $strHTML = "<h1 class=\"page-header\">$strPageTitle</h1>\n<p>Default theme updated. <a href=\"$strReturnURL\">Return</a></p>\n";
        $CMS->AP->Display($strHTML);
      } else {
        $strFormMsg = "You are about to set <b>$strTheme</b> as the default theme.";
      }
      break;
  }
  
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<p>$strFormMsg</p>
<form action="{FN_ADMIN_TOOLS}$strFormAction" method="post">
$strCustomFields
<p><input type="hidden" name="dummy" value="$intItemID" /></p>
<p>$strSubmitButton $strCancelButton</p>
</form>

END;

  $CMS->AP->Display($strHTML);
