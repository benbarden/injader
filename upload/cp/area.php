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
    $strFormAction .= "&amp;navtype=$strNavType";
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
      $strNavType   = empty($_POST['optNavType']) ? C_NAV_PRIMARY : $_POST['optNavType'];
      switch ($strNavType) {
        case "1":
          $strNavType  = C_NAV_PRIMARY;
          break;
        case "2":
          $strNavType = C_NAV_SECONDARY;
          break;
        case "3":
          $strNavType = C_NAV_TERTIARY;
          break;
        default:
          $strNavType = C_NAV_PRIMARY;
          break;
      }
      $intParentID  = $CMS->FilterNumeric($arrPostData['optParent'.$strNavType]);
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
        $strLayoutStyle   = $arrPostData['txtLayoutStyle'];
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
                $strLink = str_replace("?loggedin=1", "", $strLink);
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
          $strLayoutStyle, $strNavType, $strSubareaContent);
        $strMsg = "created";
      } elseif ($blnEdit) {
        $CMS->AR->EditArea($intAreaID, $strAreaName, $intLevel, $intAreaOrder, 0, 0, 
          $intParentID, $intPerProfileID, $intAreaGraphicID, $intItemsPerPage, 
          $strSortRule, $strIncludeInFeed, $intMaxFileSizeBytes, $intMaxFilesPerUser, 
          $strAreaURL, $strSmartTags, $strAreaDesc, $strAreaTypeName, $strAreaTheme, 
          $strLayoutStyle, $strNavType, $blnRebuild, $strSubareaContent);
        $strMsg = "edited";
      } elseif ($blnDelete) {
        $CMS->AR->DeleteArea($intAreaID);
        $strMsg = "deleted";
      }
      $CMS->AT->RebuildAreaArray("");
      $strHTML = "<h1>$strPageTitle</h1>\n<p>Area was successfully $strMsg. <a href=\"{FN_ADM_AREAS}?navtype=$strNavType\">Manage Areas</a></p>";
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
    $strLayoutStyle = $arrData['txtLayoutStyle'];
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
      $strNavType   = $arrArea['nav_type'];
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
<h1>$strPageTitle</h1>
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
    $strNavType1Checked = "";
    $strNavType2Checked = "";
    $strNavType3Checked = "";
    switch ($strNavType) {
      case C_NAV_PRIMARY:
        $strNavType1Checked = " checked=\"checked\"";
        break;
      case C_NAV_SECONDARY:
        $strNavType2Checked = " checked=\"checked\"";
        break;
      case C_NAV_TERTIARY:
        $strNavType3Checked = " checked=\"checked\"";
        break;
      default:
        $strNavType = C_NAV_PRIMARY;
        $strNavType1Checked = " checked=\"checked\"";
        break;
    }
    
    $CMS->AT->arrAreaData = array();
    $CMS->DD->strEmptyItem = "None";
    $strAreaListPrimary = $CMS->DD->AreaHierarchy($intParentID, $intAreaID, "All", true, false, C_NAV_PRIMARY);
    
    $CMS->AT->arrAreaData = array();
    $CMS->DD->strEmptyItem = "None";
    $strAreaListSecondary = $CMS->DD->AreaHierarchy($intParentID, $intAreaID, "All", true, false, C_NAV_SECONDARY);
    
    $CMS->AT->arrAreaData = array();
    $CMS->DD->strEmptyItem = "None";
    $strAreaListTertiary = $CMS->DD->AreaHierarchy($intParentID, $intAreaID, "All", true, false, C_NAV_TERTIARY);
    
    $strHTML = <<<END
<h1>$strPageTitle</h1>
<p>* = Required field</p>
<form id="frmMajesticForm" action="{FN_ADM_AREA}?$strFormAction" method="post">
<script type="text/javascript">
  if (!document.all) {
    frmMajesticForm = document.getElementById('frmMajesticForm');
  }
</script>
<table class="DefaultTable PageTable" cellspacing="1">
  <colgroup>
    <col class="InfoColour WideCell" />
    <col class="BaseColour" />
  </colgroup> 
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>General Settings</b></td>
  </tr>
  <tr>
    <td>
      <label for="txtAreaName"><b>Area Name</b>: *</label>
      <br />The name of this area. Will be displayed in navigation links.
      <br /><i>Do not use HTML in this field.</i>
    </td>
    <td>
      $strMissingAreaName
      <input type="text" id="txtAreaName" name="txtAreaName" maxlength="125" size="40" value="$strAreaName" />
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtAreaDesc"><b>Area Description</b>:</label>
      <br />The description of this area. Can be displayed in your theme.
      <br /><i>You can use HTML in this field.</i>
    </td>
    <td>
      <textarea id="txtAreaDesc" name="txtAreaDesc" rows="10" cols="60">$strAreaDesc</textarea>
    </td>
  </tr>
  <tr>
    <td class="HeadColour SpanCell" colspan="2"><b>Navigation Settings</b></td>
  </tr>
  <tr>
    <td>
      <b>Navigation Type:</b>
      <br />This determines where the area will appear on your site.
    </td>
    <td>
      <input type="radio" id="optNavType1" name="optNavType" onclick="SwitchDropDown('Primary');" value="1"$strNavType1Checked /><label for="optNavType1">Primary</label>
      <br />
      <input type="radio" id="optNavType2" name="optNavType" onclick="SwitchDropDown('Secondary');" value="2"$strNavType2Checked /><label for="optNavType2">Secondary</label>
      <br />
      <input type="radio" id="optNavType3" name="optNavType" onclick="SwitchDropDown('Tertiary');" value="3"$strNavType3Checked /><label for="optNavType3">Tertiary</label>
    </td>
  </tr>
  <tr>
    <td>
      <b>Parent</b>:
      <br />Choose "None" to create a top-level area, or choose a parent to create a subarea.
    </td>
    <td>
      <select id="optParentPrimary" name="optParentPrimary">
$strAreaListPrimary
      </select>
      <select id="optParentSecondary" name="optParentSecondary">
$strAreaListSecondary
      </select>
      <select id="optParentTertiary" name="optParentTertiary">
$strAreaListTertiary
      </select>
    </td>
  </tr>
  <tr>
    <td>
      <label for="txtAreaOrder"><b>Order</b>: *</label>
      <br />This determines how your areas should be ordered (numerical order, lowest to highest).
    </td>
    <td>
      $strMissingAreaOrder
      <input type="text" id="txtAreaOrder" name="txtAreaOrder" maxlength="5" size="5" value="$intAreaOrder" />
    </td>
  </tr>
    
END;

    // ** CONTENT SETTINGS HEADING ** // -- All
    
    $strHTML .= <<<ContentSettings
    <tr>
      <td class="HeadColour SpanCell" colspan="2"><b>Content Settings</b></td>
    </tr>

ContentSettings;

    // ** AREA URL ** // -- Linked areas only
    
    if ($blnLinkedArea) {
      $strHTML .= <<<AreaURL
    <tr>
      <td>
        <label for="txtAreaURL"><b>URL</b>:</label>
        <br />The area will link to the URL you specify in this field.
      </td>
      <td>
        $strMissingAreaURL
        <input type="text" id="txtAreaURL" name="txtAreaURL" maxlength="200" size="70" value="$strAreaURL" />
      </td>
    </tr>

AreaURL;
    }
    
    // ** ITEMS PER PAGE ** // -- All except linked areas

    if (!$blnLinkedArea) {
      $strHTML .= <<<ItemsPerPage
    <tr>
      <td>
        <label for="txtItemsPerPage"><b>Items per page</b>: *</label>
        <br />The number of articles that will be displayed on each page of this area.
      </td>
      <td>
        $strMissingItemsPerPage
        <input type="text" id="txtItemsPerPage" name="txtItemsPerPage" maxlength="5" size="5" value="$intItemsPerPage" />
      </td>
    </tr>

ItemsPerPage;
    }

    // ** SORT RULE ** // -- All except linked areas

    if (!$blnLinkedArea) {
      $strSRFieldDD = $CMS->DD->SortRuleField($strSortRuleField);
      $strSROrderDD = $CMS->DD->SortRuleOrder($strSortRuleOrder);
      $strHTML .= <<<SortRule
    <tr>
      <td>
        <label for="optSortRuleField"><b>Sort by</b>:</label>
        <br />How the content in this area should be ordered.
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
    
    // ** SMART TAGS ** // -- Smart areas only
    
    if ($blnSmartArea) {
      $strTagList1 = $CMS->DD->TagList(empty($intTagID1) ? "" : $intTagID1);
      $strTagList2 = $CMS->DD->TagList(empty($intTagID2) ? "" : $intTagID2);
      $strTagList3 = $CMS->DD->TagList(empty($intTagID3) ? "" : $intTagID3);
      $strHTML .= <<<SmartTags
    <tr>
      <td class="HeadColour SpanCell" colspan="2"><b>Smart Tags</b></td>
    </tr>
    <tr>
      <td><label for="optTagList1">Tag 1:</label></td>
      <td>
        $strMissingSmartTags
        <select id="optTagList1" name="optTagList1">
$strTagList1
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="optTagList2">Tag 2:</label></td>
      <td>
        <select id="optTagList2" name="optTagList2">
$strTagList2
        </select>
      </td>
    </tr>
    <tr>
      <td><label for="optTagList3">Tag 3:</label></td>
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
        <label for="chkSubareaContent"><b>Show subarea content on index</b></label>
        <br />This will display subarea content on the index page for this area.
      </td>
      <td>
        <input type="checkbox" id="chkSubareaContent" name="chkSubareaContent" $strSubareaContentOnIndexChecked />
      </td>
    </tr>
    <tr>
      <td class="HeadColour SpanCell" colspan="2"><b><abbr title="Really Simple Syndication">RSS</abbr> Settings</b></td>
    </tr>
    <tr>
      <td>
        <label for="chkIncludeInRSSFeed"><b>Include in site feed</b></label>
        <br />If this option is checked, all content in this area will appear in the site-wide articles feed.
      </td>
      <td>
        <input type="checkbox" id="chkIncludeInRSSFeed" name="chkIncludeInRSSFeed" $strIncludeInRSSFeedChecked />
      </td>
    </tr>

IncludeInRSSFeed;
    }
    
    // ** THEMES ** // -- All except linked areas
    
    if (!$blnLinkedArea) {
      $strThemeDD = $CMS->DD->ThemeList($strAreaTheme);
      $strHTML .= <<<AreaTheme
    <tr>
      <td class="HeadColour SpanCell" colspan="2"><b>Design</b></td>
    </tr>
    <tr>
      <td>
        <label for="optAreaTheme"><b>Theme:</b></label>
        <br />Either use the default theme, or choose a specific theme for this area.
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
        <label for="txtLayoutStyle"><b>Layout Style</b>:</label>
        <br />This is for themes that support multiple styles. Consult the theme help files for more information.
      </td>
      <td>
        <input type="text" id="txtLayoutStyle" name="txtLayoutStyle" size="20" maxlength="50" value="$strLayoutStyle" />
      </td>
    </tr>
    <tr>
      <td>
        <label for="txtAreaGraphicID"><b>Graphic ID</b>:</label>
        <br />Optionally, enter the ID of an image stored under Site Files and the image can be displayed in your theme.
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
    <tr>
      <td class="HeadColour SpanCell" colspan="2"><b>Override permissions</b></td>
    </tr>
    <tr>
      <td>
        <label for="optPerProfile"><b>Permission Profile</b>:</label>
        <br />If you have area-specific permission profiles, you can select them here.
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
  $strHTML .= "</table>\n</form>\n";
  
  // ** SCRIPT ** //
  $strHTML .= <<<FooterScript
<script type="text/javascript">
  function SwitchDropDown(strWhich) {
    document.getElementById('optParentPrimary').style.display     = 'none';
    document.getElementById('optParentSecondary').style.display   = 'none';
    document.getElementById('optParentTertiary').style.display    = 'none';
    document.getElementById('optParent' + strWhich).style.display = 'block';
  }
  SwitchDropDown('$strNavType'); // do on startup
</script>

FooterScript;
  
  // ** DISPLAY ** //
  $CMS->AP->Display($strHTML);
?>