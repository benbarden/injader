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

    $cpBindings['CP']['Title'] = "Categories";

    $cpBindings['Auth']['IsAdmin'] = $CMS->RES->IsAdmin();
    $cpBindings['Auth']['CanWriteContent'] = $CMS->RES->CanAddContent();

    $repoCategory = $cmsContainer->getService('Repo.Category');
    $categoryTopLevel = $repoCategory->getTopLevel();

    $categoryList = array();

    if ($categoryTopLevel) {

        foreach ($categoryTopLevel as $topItem) {

            $categoryList[] = $topItem;

            // @todo make this support multiple levels
            $topItemId = $topItem['id'];
            $categoryChildren = $repoCategory->getByParent($topItemId);
            if ($categoryChildren) {
                foreach ($categoryChildren as $childItem) {
                    $categoryList[] = $childItem;
                }
            }

        }

    }

    $cpBindings['Page']['CategoryList'] = $categoryList;

    if (isset($_GET['msg'])) {
        $getMsg = $_GET['msg'];
        $cpBindings['CP']['Msg'] = $getMsg;
    }

    $engine = $cmsContainer->getService('Theme.EngineCPanel');
    $outputHtml = $engine->render('category/categories.twig', $cpBindings);
    print($outputHtml);
    exit;
