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
    if (!$CMS->RES->IsAdmin()) {
        $CMS->Err_MFail(M_ERR_UNAUTHORISED, "Admin");
    }

    $cpBindings = array();

    $cpBindings['Auth']['IsAdmin'] = $CMS->RES->IsAdmin();
    $cpBindings['Auth']['CanWriteContent'] = $CMS->RES->CanAddContent();

    // Parameters
    $getAction = empty($_GET['action']) ? "" : $_GET['action'];
    $getId = empty($_GET['id']) ? "" : (int) $_GET['id'];
    $isCreate = false;
    $isEdit = false;
    $isDelete = false;
    switch ($getAction) {
        case 'create':
            $isCreate = true;
            $pageTitle = 'Create Category';
            $themeFile = 'category/category-modify.twig';
            $formAction = '/cp/category.php?action=create';
            break;
        case 'edit':
            $isEdit = true;
            $pageTitle = 'Edit Category';
            $themeFile = 'category/category-modify.twig';
            if (!$getId) {
                throw new \Cms\Exception\Cp\PageException('Missing parameter: Id');
            }
            $formAction = '/cp/category.php?action=edit&id='.$getId;
            break;
        case 'delete':
            $isDelete = true;
            $pageTitle = 'Delete Category';
            $themeFile = 'category/category-delete.twig';
            if (!$getId) {
                throw new \Cms\Exception\Cp\PageException('Missing parameter: Id');
            }
            $formAction = '/cp/category.php?action=delete&id='.$getId;
            break;
        default:
            throw new \Cms\Exception\Cp\PageException('Missing parameter: Action');
            break;
    }

    $cpBindings['CP']['Title'] = $pageTitle;
    $cpBindings['Form']['Action'] = $formAction;

    $repoCategory = $cmsContainer->getService('Repo.Category');

    if ($getId) {
        $categoryData = $repoCategory->getById($getId);
        if (!$categoryData) {
            throw new \Cms\Exception\Data\DataException('Category not found: '.$getId);
        }
        $cpBindings['Data']['Category'] = $categoryData;
    }

    $engine = $cmsContainer->getService('Theme.EngineCPanel');
    $outputHtml = $engine->render($themeFile, $cpBindings);
    print($outputHtml);
    exit;

///////// to complete /////////////////

  $strAreaName = ""; $strAreaDesc = "";
  $intItemsPerPage = "5";
  $strSortRuleField = ""; $strSortRuleOrder = ""; 

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
    $strAreaDesc  = $CMS->PrepareTemplateForEditing($_POST['txtAreaDesc']);
  } else {
      $arrArea = $CMS->AR->GetArea($intAreaID);
      $strAreaName = $CMS->StripSlashesIFW($arrArea['name']);
      $strAreaDesc = $CMS->PrepareTemplateForEditing($arrArea['area_description']);
      $intParentID  = $arrArea['parent_id'];
      $intAreaOrder = $arrArea['area_order'];
        $intItemsPerPage = $arrArea['content_per_page'];
        $arrSortRule = explode("|", $arrArea['sort_rule']);
        $strSortRuleField = $arrSortRule[0];
        $strSortRuleOrder = $arrSortRule[1];
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

      $strSRFieldDD = $CMS->DD->SortRuleField($strSortRuleField);
      $strSROrderDD = $CMS->DD->SortRuleOrder($strSortRuleOrder);

  }
