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
  $strPageTitle = "Manage Areas";
  
  $strNavType = empty($_GET['navtype']) ? "" : $_GET['navtype'];
  switch ($strNavType) {
    case C_NAV_PRIMARY:
    case C_NAV_SECONDARY:
    case C_NAV_TERTIARY:
      break;
    default:
      $strNavType = C_NAV_PRIMARY;
      break;
  }
  $strPageTitle .= " - ".$strNavType;
  $CMS->AP->SetTitle($strPageTitle);
  
  $strReorderOutput = "";
  
  if ($_POST) {
    //$strHTML = "<h1>$strPageTitle</h1>\n\n";
		$intArrayCounter = 0;
    $arrAreas = $CMS->ResultQuery("SELECT id, area_order FROM {IFW_TBL_AREAS} WHERE nav_type = '$strNavType'", basename(__FILE__), __LINE__);
		for ($i=0; $i<count($arrAreas); $i++) {
			$intID    = $arrAreas[$i]['id'];
			$intOrder = $arrAreas[$i]['area_order'];
			$intNewOrder = $_POST['txtOrder'.$intID];
			if ($intOrder <> $intNewOrder) {
				$arrReorders[$intArrayCounter]['id']    = $intID;
				$arrReorders[$intArrayCounter]['order'] = $intNewOrder;
				$intArrayCounter++;
			}
		}
		if ($intArrayCounter > 0) {
			$strReorderOutput .= "<ul>\n";
			for ($i=0; $i<$intArrayCounter; $i++) {
				$intItemID   = $arrReorders[$i]['id'];
				$intNewOrder = $arrReorders[$i]['order'];
				$strReorderOutput .= "<li>Reordering item with ID: $intItemID</li>\n";
        $CMS->AR->ReorderArea($intItemID, $intNewOrder);
			}
      $strReorderOutput .= "<li>Rebuilding site hierarchy...</li>\n";
      $CMS->AT->RebuildAreaArray($strNavType);
      $intUserID = $CMS->RES->GetCurrentUserID();
      $CMS->SYS->CreateAccessLog("Reordered areas", AL_TAG_AREA_REORDER, $intUserID, "");
			$strReorderOutput .= "</ul>\n<p><b>Areas reordered successfully</b>.</p>\n";
		} else {
			$strReorderOutput .= "<p><b>No changes made to area order</b>.</p>\n";
		}
    //$strReorderOutput .= "</div>\n";
    //$CMS->AP->Display($strReorderOutput);
    //$CMS->AT->arrAreaData = ""; // Reset - added 2.2.1
  }

  $strSubmitButton = $CMS->AC->SubmitButton();
  
  $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
$strReorderOutput

<p><a href="{FN_ADM_AREAS}?navtype={C_NAV_PRIMARY}">Primary</a> : <a href="{FN_ADM_AREAS}?navtype={C_NAV_SECONDARY}">Secondary</a> : <a href="{FN_ADM_AREAS}?navtype={C_NAV_TERTIARY}">Tertiary</a></p>

<button onclick="top.location.href = '{FN_ADM_AREA}?action=create&amp;type=content&amp;navtype=$strNavType';">New Content Area</button>
<button onclick="top.location.href = '{FN_ADM_AREA}?action=create&amp;type=linked&amp;navtype=$strNavType';">New Linked Area</button>
<button onclick="top.location.href = '{FN_ADM_AREA}?action=create&amp;type=smart&amp;navtype=$strNavType';">New Smart Area</button>

<div style="clear: both; float: none;">&nbsp;</div>

<form id="frmAdminReorderAreas" action="{FN_ADM_AREAS}?navtype=$strNavType" method="post">
<div class="table-responsive">
<table class="table table-striped">
  <thead>
    <tr>
      <th>Level</th>
      <th>Order</th>
      <th>Name</th>
      <th>Type</th>
      <th>Options</th>
    </tr>
  </thead>
  <tbody>

END;

  if ($_POST) {
    // Important: reuse cached data or the table gets doubled up!
    $arrAreas = $CMS->AT->arrAreaData;
  } else {
    // Retrieve area data
    $arrAreas = $CMS->AT->BuildAreaArray(1, $strNavType);
  }
  for ($i=0; $i<count($arrAreas); $i++) {
    $intID    = $arrAreas[$i]['id'];
    $strName  = $arrAreas[$i]['name'];
    $strType  = $arrAreas[$i]['type'];
    $intLevel = $arrAreas[$i]['level'];
    $intOrder = $arrAreas[$i]['order'];
    $intLeft  = (integer) $arrAreas[$i]['left'];
    $intRight = (integer) $arrAreas[$i]['right'];
    $intDiff  = $intRight - $intLeft;
    $strRowClass = " class=\"admAreaRow$intLevel\"";
    $strRowIndent = ($intLevel * 15)."px";
    /* --- Used for debugging only
    $strIndent = "";
    for ($j=0; $j<$intLevel; $j++) {
      $strIndent .= "&gt;";
    }
    $strIndent .= " ";
    */
    $strHTML .= <<<TableRow
  <tr$strRowClass>
    <td class="Centre level">$intLevel</td>
    <td class="Centre options"><input type="text" name="txtOrder$intID" value="$intOrder" size="3" maxlength="3" /></td>
    <td class="Left name" style="padding-left: $strRowIndent">$strName</td>
    <td class="Centre type">$strType</td>

TableRow;
    // Edit link
    $strGETType = strtolower($strType);
    $strEditLink = "<a href=\"{FN_ADM_AREA}?action=edit&amp;type=$strGETType&amp;id=$intID\">Edit</a>";
    // Delete link
    if ($intDiff > 1) {
      $blnAreaCanBeDeleted = false;
    } else {
      // Check if any articles are in this area
      if ($strType == "Content") {
        $intNumItems = $CMS->AR->CountContentInArea($intID, "");
        if ($intNumItems > 0) {
          $intDeletedItems = $CMS->AR->CountContentInArea($intID, C_CONT_DELETED);
          if ($intNumItems == $intDeletedItems) {
            $blnAreaCanBeDeleted = true;
          } else {
            $blnAreaCanBeDeleted = false;
          }
        } else {
          $blnAreaCanBeDeleted = true;
        }
      } else {
        $blnAreaCanBeDeleted = true;
      }
    }
    // Delete link
    if ($blnAreaCanBeDeleted) {
      $strDeleteLink = "<a href=\"{FN_ADM_AREA}?action=delete&amp;id=$intID\">Delete</a>";
    } else {
      $strDeleteLink = "";
    }
    // Separator
    if (($strEditLink) && ($strDeleteLink)) {
      $strSeparator = " : ";
    } else {
      $strSeparator = "";
    }
    $strHTML .= "    <td class=\"BaseColour TinyCell Centre options\">$strEditLink$strSeparator$strDeleteLink</td>\n  </tr>\n";
  }
  
  $strHTML .= <<<END2
    <tr>
      <td class="FootColour SpanCell Centre" colspan="99">
        $strSubmitButton
      </td>
    </tr>
  </tbody>
</table>
</div>
</form>

END2;

  $CMS->AP->Display($strHTML);
?>