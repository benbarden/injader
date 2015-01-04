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
    
    // Check if installed
    if (file_exists("data/secure/db_vars.php") == false) {
        header("Location: installer/");
    }
    
    require 'sys/header.php';

    $pageType = null;

    // If there's a subfolder in the URL, don't treat this as part of the URL array
    $currentUrl = $_SERVER['REQUEST_URI'];
    $posFolder = strpos($currentUrl, URL_ROOT);
    if ($posFolder !== false) {
        $currentUrl = substr($currentUrl, $posFolder + strlen(URL_ROOT));
    }

    // Load special CMS pages
    $currentUrlArray = explode("/", $currentUrl);
    $urlBit1 = array_key_exists(0, $currentUrlArray) ? $currentUrlArray[0] : "";
    $urlBit2 = array_key_exists(1, $currentUrlArray) ? $currentUrlArray[1] : "";
    $urlBit3 = array_key_exists(2, $currentUrlArray) ? $currentUrlArray[2] : "";
    $urlBit4 = array_key_exists(3, $currentUrlArray) ? $currentUrlArray[3] : "";
    if (($urlBit1 == 'cms') && ($urlBit2 == 'archives')) {
        $pageType = 'archives';
    }

    // Homepage
    $homeUrlArray = array('', 'index.php');
    $isHomePage = in_array($currentUrl, $homeUrlArray);

    if ($isHomePage) {

        $pageType = 'area';
        $itemId = $CMS->AR->GetDefaultAreaID();

    } elseif (!$pageType) {

        // ** Find this page in the database ** //
        $arrPageObject = $CMS->UM->getByUrl($_SERVER['REQUEST_URI']);

        // ** Does it exist? ** //
        if (!is_array($arrPageObject)) {
            $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "Not found: ".$_SERVER['REQUEST_URI']);
        }

        // ** Verify whether this is a valid URL ** //
        if ($arrPageObject[0]['is_active'] == "N") {

            // Nope, not active. Do we have any others?
            if (!empty($arrPageObject[0]['article_id'])) {
                $strErrText       = "Article ".$arrPageObject[0]['article_id'];
                $arrNewPageObject = $CMS->UM->getActiveArticle($arrPageObject[0]['article_id']);
                $blnRedirected    = true;
            } elseif (!empty($arrPageObject[0]['area_id'])) {
                $strErrText       = "Area ".$arrPageObject[0]['area_id'];
                $arrNewPageObject = $CMS->UM->getActiveArea($arrPageObject[0]['area_id']);
                $blnRedirected    = true;
            } else {
                $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "No article or area id for this url!");
            }
            if ($blnRedirected) {
                if (!is_array($arrNewPageObject)) {
                    $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "No active entry for item: $strErrText");
                }
                httpRedirectPerm($arrNewPageObject[0]['relative_url']);
            }

        }

        // We have an active URL, so off we go!
        if (!empty($arrPageObject[0]['article_id'])) {
            $pageType = "article";
            $itemId = $arrPageObject[0]['article_id'];
        } elseif (!empty($arrPageObject[0]['area_id'])) {
            $pageType = "area";
            $itemId = $arrPageObject[0]['area_id'];
        } else {
            $CMS->Err_MFail(M_ERR_NO_ROWS_RETURNED, "No article or area id for this url!");
        }

    }
    
    // Theme renderer
    $themeRenderer = new \Cms\Theme\Renderer($cmsContainer);
    switch ($pageType) {
        case "area":
        case "category":
            // Pagination
            $pageNo = null;
            if (isset($_GET['page'])) {
                $pageNo = (int) $_GET['page'];
            }
            if (!$pageNo) {
                $pageNo = 1;
            }
            $themeRenderer->setPageNo($pageNo);
            // Must set page number before setting object
            $themeRenderer->setItemId($itemId);
            $themeRenderer->setObjectCategory();
            break;
        case "article":
            $themeRenderer->setItemId($itemId);
            $themeRenderer->setObjectArticle();
            break;
        case "file":
            $themeRenderer->setItemId($itemId);
            $themeRenderer->setObjectFile();
            break;
        case "user":
            $themeRenderer->setItemId($itemId);
            $themeRenderer->setObjectUser();
            break;
        case 'archives':
            $themeRenderer->setObjectArchives();
            if ($urlBit3) {
                $themeRenderer->setRendererParam(1, $urlBit3);
            }
            if ($urlBit4) {
                $themeRenderer->setRendererParam(2, $urlBit4);
            }
            break;
        default:
            $CMS->Err_MFail(M_ERR_INVALID_VIEW_PARAM, $pageType);
            break;
    }
    $themeRenderer->render();

