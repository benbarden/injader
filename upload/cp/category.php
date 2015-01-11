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
    /* @var \Cms\Data\Category\CategoryRepository $repoCategory */

    $formErrors = array();
    $formPrefill = array();

    if ($getId) {

        $categoryData = $repoCategory->getById($getId);
        if (!$categoryData) {
            throw new \Cms\Exception\Data\DataException('Category not found: '.$getId);
        }
        $cpBindings['Data']['Category'] = $categoryData;

        /* @var \Cms\Data\Category\Category $categoryData */
        $dbName = $categoryData->getName();
        $dbPermalink = $categoryData->getPermalink();
        $dbDescription = $categoryData->getDescription();
        $dbPerPage = $categoryData->getItemsPerPage();
        $dbParentId = $categoryData->getParentId();
        if (!$dbParentId) {
            $dbParentId = 0;
        }
        $dbSortRule = $categoryData->getSortRule();

    } else {

        $categoryData = null;

    }

    if ($_POST) {
        if ($isDelete) {
            $postId = $_POST['category-id'];
            if ($postId != $getId) {
                $formErrors[] = array('Message' => 'Category id mismatch!');
            }
        } else {
            $postName        = $_POST['name'];
            $postPermalink   = $_POST['permalink'];
            $postDescription = $_POST['description'];
            $postPerPage     = (int) $_POST['items-per-page'];
            $postParentId    = (int) $_POST['parent-id'];
            $postSortRule    = $_POST['sort-rule'];
            if (!$postName) {
                $formErrors[] = array('Field' => 'name', 'Message' => 'Missing category name');
            }
            if (!$postPermalink) {
                $formErrors[] = array('Field' => 'permalink', 'Message' => 'Missing permalink');
            }
            if (!$postPerPage) {
                $formErrors[] = array('Field' => 'items-per-page', 'Message' => 'Missing items per page');
            }
            if (!$postSortRule) {
                $formErrors[] = array('Field' => 'sort-rule', 'Message' => 'Missing sort rule');
            }
        }
        if (!$formErrors) {
            $dbData = array();
            $dbData['id'] = $getId;
            if (!$isDelete) {
                $dbData['name'] = $postName;
                $dbData['permalink'] = $postPermalink;
                $dbData['description'] = $postDescription;
                if ($postParentId) {
                    $dbData['parent_id'] = $postParentId;
                }
                $dbData['items_per_page'] = $postPerPage;
                $dbData['sort_rule'] = $postSortRule;
            }
            $modelCategory = new \Cms\Data\Category\Category($dbData);
            try {
                if ($isDelete) {
                    $repoCategory->delete($modelCategory);
                } else {
                    $repoCategory->save($modelCategory);
                }
                if ($isCreate) {
                    $resultMsg = '?msg=addsuccess';
                } elseif ($isEdit) {
                    $resultMsg = '?msg=editsuccess';
                } elseif ($isDelete) {
                    $resultMsg = '?msg=deletesuccess';
                }
                $resultsUrl = URL_ROOT.'cp/categories.php'.$resultMsg;
                header('Location: '.$resultsUrl);
            } catch (\Cms\Exception\Data\DataException $e) {
                $formErrors[] = array('Message' => 'DataException: '.$e->getMessage());
            }
        }
    }

    // Prefill form
    if ($_POST) {
        $formPrefill[] = array('Field' => 'name', 'Value' => $postName);
        $formPrefill[] = array('Field' => 'permalink', 'Value' => $postPermalink);
        $formPrefill[] = array('Field' => 'description', 'Value' => $postDescription);
        if ($postPerPage != '0') {
            $formPrefill[] = array('Field' => 'items-per-page', 'Value' => $postPerPage);
        }
        $formPrefill[] = array('Field' => 'parent-id', 'Value' => $postParentId);
        $formPrefill[] = array('Field' => 'sort-rule', 'Value' => $postSortRule);
    } elseif ($categoryData && $isEdit) {
        $formPrefill[] = array('Field' => 'name', 'Value' => $dbName);
        $formPrefill[] = array('Field' => 'permalink', 'Value' => $dbPermalink);
        $formPrefill[] = array('Field' => 'description', 'Value' => $dbDescription);
        if ($dbPerPage != '0') {
            $formPrefill[] = array('Field' => 'items-per-page', 'Value' => $dbPerPage);
        }
        $formPrefill[] = array('Field' => 'parent-id', 'Value' => $dbParentId);
        $formPrefill[] = array('Field' => 'sort-rule', 'Value' => $dbSortRule);
    }

    $cpBindings['Form']['Errors'] = $formErrors;
    $cpBindings['Form']['Prefill'] = $formPrefill;

    $engine = $cmsContainer->getService('Theme.EngineCPanel');
    $outputHtml = $engine->render($themeFile, $cpBindings);
    print($outputHtml);
    exit;
