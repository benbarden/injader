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
  $intParentID = "";
  $strAction = $_GET['action'];
  $blnCreate = false; $blnEdit = false; $blnDelete = false;
  $blnCheckID = false;
  if ($strAction == "create") {
    $intAreaID = "";
    $strPageTitle = "Create ";
    $strFormAction = "action=create";
    $blnCreate = true;
  } elseif ($strAction == "edit") {
    $blnCheckID = true;
    $strPageTitle = "Edit ";
    $strFormAction = "action=edit";
    $blnEdit = true;
  } elseif ($strAction == "delete") {
    $blnCheckID = true;
    $strPageTitle = "Delete ";
    $strFormAction = "action=delete";
    $blnDelete = true;
  } else {
    $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAction");
  }
  
  if ($blnCheckID) {
    $intAreaID = $CMS->FilterNumeric($_GET['id']);
    if (!$intAreaID) {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "ID");
    }
    $strFormAction .= "&amp;id=$intAreaID";
  }
  
  $blnContentArea = false;
  $blnSmartArea = false;
  $blnLinkedArea = false;

  $strNavType = "";
  
  if ($blnDelete) {
    $strPageTitle .= "Area";
  } else {
    $strAreaType = $_GET['type'];
    if ($strAreaType == "content") {
      $strPageTitle .= "Content Area";
      $strAreaTypeName = C_AREA_CONTENT;
      $blnContentArea = true;
    } elseif ($strAreaType == "smart") {
      $strPageTitle .= "Smart Area";
      $strAreaTypeName = C_AREA_SMART;
      $blnSmartArea = true;
    } elseif ($strAreaType == "linked") {
      $strPageTitle .= "Linked Area";
      $strAreaTypeName = C_AREA_LINKED;
      $blnLinkedArea = true;
    } else {
      $CMS->Err_MFail(M_ERR_MISSINGPARAMS_SYSTEM, "strAreaType");
    }
    $strFormAction .= "&amp;type=$strAreaType";
  }
  
  $strAreaName = ""; $strAreaDesc = "";
  $intAreaOrder = "";
  $intItemsPerPage = "5";
  $strSortRuleField = ""; $strSortRuleOrder = ""; 
  $strIncludeInRSSFeedChecked = ""; $strSubareaContentOnIndexChecked = "";
  $intAreaGraphicID = ""; $intPerProfileID = "";
  $intMaxFileSizeMB = ""; $intMaxFilesPerUser = "";
  $strAreaURL = ""; $strAreaTheme = ""; $strLayoutStyle = "";
  $strMissingAreaName = ""; $strMissingAreaOrder = "";
  $strMissingItemsPerPage = ""; $strMissingMaxFileSizeMB = ""; $strMissingMaxFilesPerUser = "";
  $strMissingAreaURL = ""; $strMissingSmartTags = "";

  if ($_POST) {

    // ** GRAB POST DATA ** //

    $arrPostData  = $CMS->ArrayAddSlashes($_POST);
    $strAreaName  = $arrPostData['txtAreaName'];
    
    if ($blnDelete) {
      $blnSubmitForm = true;
    } else {
      $strAreaDesc  = $CMS->PrepareTemplateForSaving($_POST['txtAreaDesc']);
      $intParentID  = $CMS->FilterNumeric($arrPostData['optParent']);
      $intAreaOrder = $CMS->FilterNumeric($arrPostData['txtAreaOrder']);
      if ($blnLinkedArea) {
        $strAreaURL = $arrPostData['txtAreaURL'];
        $intItemsPerPage = 0;
      } else {
        $strAreaURL = "";
        $intItemsPerPage  = $CMS->FilterNumeric($arrPostData['txtItemsPerPage']);
        $strSortRuleField = $arrPostData['optSortRuleField'];
        $strSortRuleOrder = $arrPostData['optSortRuleOrder'];
      }
      if ($blnContentArea) {
        $strIncludeInFeed  = !empty($arrPostData['chkIncludeInRSSFeed']) ? "Y" : "N";
        $strSubareaContent = !empty($arrPostData['chkSubareaContent']) ? "Y" : "N";
      } else {
        $strIncludeInFeed  = "N";
        $strSubareaContent = "N";
      }
      if ($blnSmartArea) {
        $intTagID1 = $arrPostData['optTagList1'];
        $intTagID2 = $arrPostData['optTagList2'];
        $intTagID3 = $arrPostData['optTagList3'];
      }
      $intMaxFileSizeBytes = 0;
      $intMaxFilesPerUser = 0;
      if ($blnLinkedArea) {
        $intAreaGraphicID = 0;
        $strAreaTheme     = "";
      } else {
        $intAreaGraphicID = $arrPostData['txtAreaGraphicID'];
        $strAreaTheme     = $arrPostData['optAreaTheme'];
        $strLayoutStyle   = ""; // option removed
      }
      if ($blnContentArea) {
        $intPerProfileID = $arrPostData['optPerProfile'];
      } else {
        $intPerProfileID = 0;
      }

      // ** VALIDATE POST DATA ** //

      $blnSubmitForm = true;
      if (!$strAreaName) {
        $strMissingAreaName = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      } else {
        if (
            (strtolower($strAreaName) == "cp") ||
            (strtolower($strAreaName) == "custom") ||
            (strtolower($strAreaName) == "data") ||
            (strtolower($strAreaName) == "ext") ||
            (strtolower($strAreaName) == "info") ||
            (strtolower($strAreaName) == "installer") ||
            (strtolower($strAreaName) == "sys")
            )
          {
          $blnSubmitForm = false;
          $strMissingAreaName = $CMS->AC->InvalidFormData(M_ERR_SYSTEM_SEO_AREA_NAME);
        } else {
            $blnInvalid = false;
            // Check if the link has been used
            $blnCheckLink = false;
            if ($blnCreate) {
                $intLinkStyle = $CMS->SYS->GetSysPref(C_PREF_LINK_STYLE);
                if (!in_array($intLinkStyle, array("1", "2"))) {
                    $blnCheckLink = true;
                    $intCheckAreaID = 0;
                }
            } elseif ($blnEdit) {
                $blnCheckLink = true;
                $intCheckAreaID = $intAreaID;
            }
            if ($blnCheckLink) {
                $strSEOTitle = $CMS->MakeSEOTitle($strAreaName);
                $CMS->PL->SetTitle($strSEOTitle);
                $strLink = $CMS->PL->ViewArea($intCheckAreaID);
                $CMS->PL->SetTitle("");
                $blnInvalid = $CMS->UM->isUrlInUse($strLink, "", $intCheckAreaID);
                // Tell the user if it's invalid
                if ($blnInvalid) {
                  $blnSubmitForm = false;
                  $strMissingAreaName = $CMS->AC->InvalidFormData(M_ERR_DUPLICATE_SEO_TITLE);
                }
            }
        }
      }
      if (!$intParentID) {
        $intParentID = 0;
        $intLevel = 1;
      } else {
        $intLevel = $CMS->AT->GetAreaDepth($intParentID);
        $intLevel++;
      }
      if (!$intAreaOrder) {
        $strMissingAreaOrder = $CMS->AC->InvalidFormData("");
        $blnSubmitForm = false;
      }
      if ($blnLinkedArea) {
        if (!$strAreaURL) {
          $strMissingAreaURL = $CMS->AC->InvalidFormData("");
          $blnSubmitForm = false;
        }
      } else {
        if (!$intItemsPerPage) {
          $strMissingItemsPerPage = $CMS->AC->InvalidFormData("");
          $blnSubmitForm = false;
        }
      }
      if ($blnSmartArea) {
        if ((!$intTagID1) && (!$intTagID2) && (!$intTagID3)) {
          $strMissingSmartTags = $CMS->AC->InvalidFormData(M_ERR_NO_SMART_TAGS);
          $blnSubmitForm = false;
        }
      }
      if (!$blnLinkedArea) {
        if (!$intAreaGraphicID) {
          $intAreaGraphicID = 0;
        }
      }
      
    }

    if ($blnSubmitForm) {

      // ** PREPARE FOR DATABASE ** //
      
      if (!$blnDelete) {
        if ($blnLinkedArea) {
          $strSortRule = "";
        } else {
          $strSortRule = $strSortRuleField."|".$strSortRuleOrder;
        }
        $strSmartTags = "";
        if ($blnSmartArea) {
          if ($intTagID1) {
            $strSmartTags = $intTagID1;
          }
          if ($intTagID2) {
            if ($strSmartTags) {
              $strSmartTags .= "|".$intTagID2;
            } else {
              $strSmartTags = $intTagID2;
            }
          }
          if ($intTagID3) {
            if ($strSmartTags) {
              $strSmartTags .= "|".$intTagID3;
            } else {
              $strSmartTags = $intTagID3;
            }
          }
        }
      }
      
      // ** WRITE TO DATABASE ** //
      $blnRebuild = true;
      if ($blnCreate) {
        $intAreaID = $CMS->AR->CreateArea($strAreaName, $intLevel, $intAreaOrder, 0, 0, 
          $intParentID, $intPerProfileID, $intAreaGraphicID, $intItemsPerPage, 
          $strSortRule, $strIncludeInFeed, $intMaxFileSizeBytes, $intMaxFilesPerUser, 
          $strAreaURL, $strSmartTags, $strAreaDesc, $strAreaTypeName, $strAreaTheme, 
          $strLayoutStyle, $strSubareaContent);
        $strMsg = "created";
      } elseif ($blnEdit) {
        $CMS->AR->EditArea($intAreaID, $strAreaName, $intLevel, $intAreaOrder, 0, 0, 
          $intParentID, $intPerProfileID, $intAreaGraphicID, $intItemsPerPage, 
          $strSortRule, $strIncludeInFeed, $intMaxFileSizeBytes, $intMaxFilesPerUser, 
          $strAreaURL, $strSmartTags, $strAreaDesc, $strAreaTypeName, $strAreaTheme, 
          $strLayoutStyle, $blnRebuild, $strSubareaContent);
        $strMsg = "edited";
      } elseif ($blnDelete) {
        $CMS->AR->DeleteArea($intAreaID);
        $strMsg = "deleted";
      }
      $CMS->AT->RebuildAreaArray("");
      $strHTML = "<h1>$strPageTitle</h1>\n<p>Area was successfully $strMsg. <a href=\"{FN_ADM_AREAS}\">Manage Areas</a></p>";
      $strPageTitle .= " - Results";
      $CMS->AP->SetTitle($strPageTitle);
      $CMS->AP->Display($strHTML);
    }
  }

  // ** NO POST ** //

  $CMS->AP->SetTitle($strPageTitle);

  if ($_POST) {
    // Fields where slashes need to be stripped
    $arrData = $CMS->ArrayStripSlashes($_POST);
    $strAreaName = $arrData['txtAreaName'];
    if ($strIncludeInFeed == "Y") {
      $strIncludeInRSSFeedChecked = 'checked="checked"';
    }
    if ($strSubareaContent == "Y") {
      $strSubareaContentOnIndexChecked = 'checked="checked"';
    }
    $strAreaDesc  = $CMS->PrepareTemplateForEditing($_POST['txtAreaDesc']);
    $strLayoutStyle = ""; // option removed
  } else {
    if (!$blnCreate) {
      $arrArea = $CMS->AR->GetArea($intAreaID);
      if (count($arrArea) == 0) {
        $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Area: $intAreaID");
      }
      $strAreaName = $CMS->StripSlashesIFW($arrArea['name']);
      $strAreaDesc = $CMS->PrepareTemplateForEditing($arrArea['area_description']);
    }
    if ($blnCreate) {
      $strIncludeInRSSFeedChecked = "checked=\"checked\"";
      if (!$intAreaOrder) {
        $intAreaOrder = "1";
      }
    } elseif ($blnEdit) {
      $intParentID  = $arrArea['parent_id'];
      $intAreaOrder = $arrArea['area_order'];
      if ($blnLinkedArea) {
        $strAreaURL = str_replace("{", "{".ZZZ_TEMP, $arrArea['area_url']);
        $strAreaURL = str_replace("}", ZZZ_TEMP."}", $strAreaURL);
      } else {
        $intItemsPerPage = $arrArea['content_per_page'];
        $arrSortRule = explode("|", $arrArea['sort_rule']);
        $strSortRuleField = $arrSortRule[0];
        $strSortRuleOrder = $arrSortRule[1];
      }
      if ($blnContentArea) {
        if ($arrArea['include_in_rss_feed'] == "Y") {
          $strIncludeInRSSFeedChecked = 'checked="checked"';
        }
        if ($arrArea['subarea_content_on_index'] == "Y") {
          $strSubareaContentOnIndexChecked = 'checked="checked"';
        }
      }
      if ($blnSmartArea) {
        $arrSmartTags = explode("|", $arrArea['smart_tags']);
        $intTagID1 = empty($arrSmartTags[0]) ? "" : $CMS->StripSlashesIFW($arrSmartTags[0]);
        $intTagID2 = empty($arrSmartTags[1]) ? "" : $CMS->StripSlashesIFW($arrSmartTags[1]);
        $intTagID3 = empty($arrSmartTags[2]) ? "" : $CMS->StripSlashesIFW($arrSmartTags[2]);
      }
      if (!$blnLinkedArea) {
        $intAreaGraphicID = $arrArea['area_graphic_id'];
        $strAreaTheme     = $arrArea['theme_path'];
        $strLayoutStyle   = $arrArea['layout_style'];
      }
      if ($blnContentArea) {
        $intPerProfileID = $arrArea['permission_profile_id'];
      }
    }
  }

  if ($blnDelete) {
  
    $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<form id="frmMajesticForm" action="{FN_ADM_AREA}?$strFormAction" method="post">
<table class="DefaultTable MediumTable FixedTable" cellspacing="1">
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>Delete Area</b></td>
  </tr>
  <tr>
    <td class="BaseColour" colspan="2">
      <input type="hidden" name="txtAreaName" value="$strAreaName" />
      You are about to delete the following area: $strAreaName
    </td>
  </tr>

END;

  } else {
  
    // ** BUILD FORM HTML ** // -- General settings
    
    $CMS->AT->arrAreaData = array();
    $CMS->DD->strEmptyItem = "None";
    $strAreaListPrimary = $CMS->DD->AreaHierarchy($intParentID, $intAreaID, "All", true, false);
    
    $strHTML = <<<END
<h1 class="page-header">$strPageTitle</h1>
<p>* = Required field</p>
<form id="frmMajesticForm" action="{FN_ADM_AREA}?$strFormAction" method="post">
<script type="text/javascript">
  if (!document.all) {
    frmMajesticForm = document.getElementById('frmMajesticForm');
  }
</script>
<div class="table-responsive">
<table class="table table-striped">
  <tr class="separator-row">
    <td colspan="2">General Settings</td>
  </tr>
  <tr>
    <td>
      <label for="txtAreaName"><b>Area Name</b> *</label>
    </td>
    <td>
      $strMissingAreaName
      <input type="text" id="txtAreaName" name="txtAreaName" maxlength="125" size="40" value="$strAreaName" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtAreaDesc"><b>Area Description</b></label>
      <br /><em>HTML is ok here</em>
    </td>
    <td>
      <textarea id="txtAreaDesc" name="txtAreaDesc" rows="10" cols="60">$strAreaDesc</textarea>
    </td>
  </tr>
  <tr class="separator-row">
    <td colspan="2">Navigation Settings</td>
  </tr>
  <tr>
    <td>
      <b>Parent</b>
    </td>
    <td>
      <select id="optParent" name="optParent">
$strAreaListPrimary
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtAreaOrder"><b>Order</b> *</label>
    </td>
    <td>
      $strMissingAreaOrder
      <input type="text" id="txtAreaOrder" name="txtAreaOrder" maxlength="5" size="5" value="$intAreaOrder" />
    </td>
  </tr>
    
END;

    // ** CONTENT SETTINGS HEADING ** // -- All
    
    $strHTML .= <<<ContentSettings
    <tr class="separator-row">
      <td colspan="2">Content Settings</td>
    </tr>

ContentSettings;

    // ** AREA URL ** // -- Linked areas only
    
    if ($blnLinkedArea) {
      $strHTML .= <<<AreaURL
    <tr>
      <td>
        <label for="txtAreaURL"><b>URL</b></label>
      </td>
      <td>
        $strMissingAreaURL
        <input type="text" id="txtAreaURL" name="txtAreaURL" maxlength="200" size="70" value="$strAreaURL" />
      </td>
    </tr>

AreaURL;
    }

      // ** SORT RULE ** // -- All except linked areas

      if (!$blnLinkedArea) {
          $strSRFieldDD = $CMS->DD->SortRuleField($strSortRuleField);
          $strSROrderDD = $CMS->DD->SortRuleOrder($strSortRuleOrder);
          $strHTML .= <<<SortRule
    <tr>
      <td>
        <label for="optSortRuleField"><b>Sort by</b></label>
      </td>
      <td>
        <select id="optSortRuleField" name="optSortRuleField">
          $strSRFieldDD
        </select>
        <select id="optSortRuleOrder" name="optSortRuleOrder">
          $strSROrderDD
        </select>
      </td>
    </tr>

SortRule;
      }

      // ** ITEMS PER PAGE ** // -- All except linked areas

    if (!$blnLinkedArea) {
      $strHTML .= <<<ItemsPerPage
    <tr>
      <td>
        <label for="txtItemsPerPage"><b>Items per page</b> *</label>
      </td>
      <td>
        $strMissingItemsPerPage
        <input type="text" id="txtItemsPerPage" name="txtItemsPerPage" maxlength="5" size="5" value="$intItemsPerPage" />
      </td>
    </tr>

ItemsPerPage;
    }

    // ** SMART TAGS ** // -- Smart areas only
    
    if ($blnSmartArea) {
      $strTagList1 = $CMS->DD->TagList(empty($intTagID1) ? "" : $intTagID1);
      $strTagList2 = $CMS->DD->TagList(empty($intTagID2) ? "" : $intTagID2);
      $strTagList3 = $CMS->DD->TagList(empty($intTagID3) ? "" : $intTagID3);
      $strHTML .= <<<SmartTags
    <tr class="separator-row">
      <td colspan="2">Smart Tags</td>
    </tr>
    <tr>
      <td><label for="optTagList1">Tag 1</label></td>
      <td>
        $strMissingSmartTags
        <select id="optTagList1" name="optTagList1">
$strTagList1
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="optTagList2">Tag 2</label></td>
      <td>
        <select id="optTagList2" name="optTagList2">
$strTagList2
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="optTagList3">Tag 3</label></td>
      <td>
        <select id="optTagList3" name="optTagList3">
$strTagList3
        </select>
      </td>
    </tr>

SmartTags;
    }
    
    // ** SUBAREA / RSS OPTIONS ** // -- Content areas only
    
    if ($blnContentArea) {
      $strHTML .= <<<IncludeInRSSFeed
    <tr>
      <td>
        <label for="chkIncludeInRSSFeed"><b>Include in main site feed</b></label>
      </td>
      <td>
        <input type="checkbox" id="chkIncludeInRSSFeed" name="chkIncludeInRSSFeed" $strIncludeInRSSFeedChecked />
      </td>
    </tr>
    <tr>
      <td>
        <label for="chkSubareaContent"><b>Show subarea content on index</b></label>
      </td>
      <td>
        <input type="checkbox" id="chkSubareaContent" name="chkSubareaContent" $strSubareaContentOnIndexChecked />
      </td>
    </tr>

IncludeInRSSFeed;
    }
    
    // ** THEMES ** // -- All except linked areas
    
    if (!$blnLinkedArea) {
      $strThemeDD = $CMS->DD->ThemeList($strAreaTheme);
      $strHTML .= <<<AreaTheme
    <tr class="separator-row">
      <td colspan="2">Design</td>
    </tr>
    <tr>
      <td>
        <label for="optAreaTheme"><b>Theme override</b></label>
      </td>
      <td>
        <select id="optAreaTheme" name="optAreaTheme">
          <option value="">Use default theme</option>
          $strThemeDD
        </select>
      </td>
    </tr>
    <tr>
      <td>
        <label for="txtAreaGraphicID"><b>Graphic ID</b>:</label>
      </td>
      <td>
        <input type="text" id="txtAreaGraphicID" name="txtAreaGraphicID" maxlength="5" size="5" value="$intAreaGraphicID" />
      </td>
    </tr>

AreaTheme;
    }

    // ** PERMISSION PROFILES ** // -- Content Areas
    
    if ($blnContentArea) {
      $strPerProfileDD = $CMS->DD->PermissionProfile($intPerProfileID);
      $strHTML .= <<<PermissionProfile
    <tr class="separator-row">
      <td colspan="2">Override permissions</td>
    </tr>
    <tr>
      <td>
        <label for="optPerProfile"><b>Permissions</b>:</label>
      </td>
      <td>
        <select id="optPerProfile" name="optPerProfile">
        <option value="0">Use system-wide permission profile</option>
          $strPerProfileDD
        </select>
      </td>
    </tr>

PermissionProfile;
    }
    
  } // -- Delete
  
  // ** SUBMIT / CANCEL BUTTONS ** //
  
  $strSubmitButton = $CMS->AC->SubmitButton();
  $strCancelButton = $CMS->AC->LocationButton("Cancel", FN_ADM_AREAS);
  $strHTML .= <<<SubmitCancel
  <tr>
    <td class="FootColour SpanCell Centre" colspan="2">
      $strSubmitButton $strCancelButton
    </td>
  </tr>

SubmitCancel;

  // ** END FORM HTML ** //
  $strHTML .= "</table>\n</div>\n</form>\n";

  // ** DISPLAY ** //
  $CMS->AP->Display($strHTML);
