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

if (!$accessPermission->canCreateArticle()) {
    showCpErrorPage($cmsContainer, $cpBindings, "You do not have access to do this");
}

// Parameters
$getAction = empty($_GET['action']) ? "" : $_GET['action'];
$getId = empty($_GET['id']) ? "" : (int) $_GET['id'];
$isCreate = false;
$isEdit = false;
$isDelete = false;
switch ($getAction) {
    case 'create':
        $isCreate = true;
        $pageTitle = 'Create Article';
        $themeFile = 'article/article-modify.twig';
        $formAction = '/cp/article.php?action=create';
        break;
    case 'edit':
        $isEdit = true;
        $pageTitle = 'Edit Article';
        $themeFile = 'article/article-modify.twig';
        if (!$getId) {
            $errorMsg = 'Missing parameter: Id';
            showCpErrorPage($cmsContainer, $cpBindings, $errorMsg);
        }
        $formAction = '/cp/article.php?action=edit&id='.$getId;
        break;
    case 'delete':
        $isDelete = true;
        $pageTitle = 'Delete Article';
        $themeFile = 'article/article-delete.twig';
        if (!$getId) {
            $errorMsg = 'Missing parameter: Id';
            showCpErrorPage($cmsContainer, $cpBindings, $errorMsg);
        }
        $formAction = '/cp/article.php?action=delete&id='.$getId;
        break;
    default:
        $errorMsg = 'Missing parameter: Action';
        showCpErrorPage($cmsContainer, $cpBindings, $errorMsg);
        break;
}

$cpBindings['CP']['Title'] = $pageTitle;
$cpBindings['Form']['Action'] = $formAction;

$repoCategory = $cmsContainer->getService('Repo.Category');
$repoArticle = $cmsContainer->getService('Repo.Article');
$repoUrlMapping = $cmsContainer->getService('Repo.UrlMapping');
/* @var \Cms\Data\Category\CategoryRepository $repoCategory */
/* @var \Cms\Data\Article\ArticleRepository $repoArticle */
/* @var \Cms\Data\UrlMapping\UrlMappingRepository $repoUrlMapping */

$cpBindings['Data']['CategoryList'] = $repoCategory->getTopLevel();

$formErrors = array();
$formPrefill = array();

if ($getId) {

    $articleData = $repoArticle->getById($getId);
    if (!$articleData) {
        throw new \Cms\Exception\Data\DataException('Article not found: '.$getId);
    }
    $cpBindings['Data']['Article'] = $articleData;

    /* @var \Cms\Data\Article\Article $articleData */
    $dbTitle = $articleData->getTitle();
    $dbPermalink = $articleData->getPermalink();
    $dbContent = $articleData->getContent();
    $dbAuthorId = $articleData->getAuthorId();
    $dbCategoryId = $articleData->getCategoryId();
    $dbCreateDate = $articleData->getCreateDate();
    $dbLastUpdated = $articleData->getLastUpdated();
    $dbTags = $articleData->getTags();
    $dbLinkUrl = $articleData->getLinkUrl();
    $dbStatus = $articleData->getStatus();
    $dbTagsDeleted = $articleData->getTagsDeleted();
    $dbArticleOrder = $articleData->getArticleOrder();
    $dbExcerpt = $articleData->getExcerpt();

} else {

    $articleData = null;

}

if ($_POST) {
    $modelUrlMapping = null;
    if ($isDelete) {
        $postId = $_POST['article-id'];
        if ($postId != $getId) {
            $formErrors[] = array('Message' => 'Article id mismatch!');
        }
    } else {
        $postTitle = $_POST['title'];
        $postPermalink = $_POST['permalink'];
        $postBody = $_POST['content-body'];
        $postCategoryId = (int) $_POST['category-id'];
        $postCreateDate = sprintf('%s %s', $_POST['create-date-day'], $_POST['create-date-time']);
        $postLinkUrl = $_POST['link-url'];
        $postExcerpt = $_POST['excerpt'];

        if (!$postTitle) {
            $formErrors[] = array('Field' => 'title', 'Message' => 'Missing title');
        }
        if (!$postPermalink) {
            $formErrors[] = array('Field' => 'permalink', 'Message' => 'Missing permalink');
        }
        if (!$postBody) {
            $formErrors[] = array('Field' => 'content-body', 'Message' => 'Missing body');
        }
        // Validate permalink
        if (!$isDelete) {
            $modelUrlMapping = $repoUrlMapping->getByUrl($postPermalink);
            if ($modelUrlMapping) {
                if ($isCreate) {
                    $formErrors[] = array('Field' => 'permalink', 'Message' => 'Permalink is already in use [1]');
                } elseif ($isEdit) {
                    if ($modelUrlMapping->getArticleId() != $getId) {
                        $formErrors[] = array('Message' => 'Permalink is already in use. Please choose another.');
                    } elseif ($modelUrlMapping->getCategoryId()) {
                        $formErrors[] = array('Message' => 'Permalink is already in use. Please choose another.');
                    }
                }
            }
        }
    }
    if (!$formErrors) {
        $dbData = array();
        $dbData['id'] = $getId;
        if (!$isDelete) {
            $dbData['title'] = $postTitle;
            $dbData['permalink'] = $postPermalink;
            $dbData['content'] = $postBody;
            if ($postCategoryId) {
                $dbData['category_id'] = $postCategoryId;
            }
            $dbData['create_date'] = $postCreateDate;
            $dbData['link_url'] = $postLinkUrl;
            $dbData['article_excerpt'] = $postExcerpt;
            if ($isCreate) {
                $dbData['author_id'] = $currentUser->getUserId();
            } elseif ($articleData) {
                $dbData['author_id'] = $articleData->getAuthorId();
            }
            $dbData['last_updated'] = date('Y-m-d H:i:s');
            $dbData['content_status'] = C_CONT_PUBLISHED;
            $dbData['tags'] = '';
            $dbData['tags_deleted'] = '';
            $dbData['article_order'] = 0;
        }
        $modelArticle = new \Cms\Data\Article\Article($dbData);

        $addUrlMapping = false;
        if ($isCreate) {
            $addUrlMapping = true;
        } elseif ($isEdit) {
            if ($modelUrlMapping) {
                $urlRowId = $modelUrlMapping->getId();
            } else {
                $addUrlMapping = true;
            }
        }

        if ($addUrlMapping) {
            // ok to set up the new model
            $modelUrlMapping = new \Cms\Data\UrlMapping\UrlMapping();
            $modelUrlMapping->setRelativeUrl($postPermalink);
            if ($getId) {
                $modelUrlMapping->setArticleId($getId);
            }
            $modelUrlMapping->setCategoryId(0);
            $modelUrlMapping->setIsActive('Y');
        }

        if (!$formErrors) {
            try {
                if ($isCreate) {
                    $newArticleId = $repoArticle->save($modelArticle);
                    $modelUrlMapping->setArticleId($newArticleId);
                    $repoUrlMapping->create($modelUrlMapping);
                } elseif ($isEdit) {
                    $repoArticle->save($modelArticle);
                    if ($addUrlMapping) {
                        $urlRowId = $repoUrlMapping->create($modelUrlMapping);
                    } elseif ($modelUrlMapping) {
                        $repoUrlMapping->activateById($urlRowId);
                    }
                    $repoUrlMapping->deactivateByArticle($getId, $urlRowId);
                } elseif ($isDelete) {
                    $repoArticle->delete($modelArticle);
                    $repoUrlMapping->deleteAllByArticle($getId);
                }
                if ($isCreate) {
                    $resultMsg = '?msg=addsuccess';
                } elseif ($isEdit) {
                    $resultMsg = '?msg=editsuccess';
                } elseif ($isDelete) {
                    $resultMsg = '?msg=deletesuccess';
                }
                $resultsUrl = URL_ROOT.'cp/articles.php'.$resultMsg;
                header('Location: '.$resultsUrl);
            } catch (\Cms\Exception\Data\DataException $e) {
                $formErrors[] = array('Message' => 'DataException: '.$e->getMessage());
            }
        }
    }
}

// Prefill form
if ($_POST) {
    $formPrefill[] = array('Field' => 'title', 'Value' => $postTitle);
    $formPrefill[] = array('Field' => 'permalink', 'Value' => $postPermalink);
    if ($postCategoryId != '0') {
        $formPrefill[] = array('Field' => 'category-id', 'Value' => $postCategoryId);
    }
    $formPrefill[] = array('Field' => 'excerpt', 'Value' => $postExcerpt);
    $formPrefill[] = array('Field' => 'link-url', 'Value' => $postLinkUrl);
    $createDateArray = explode(' ', $postCreateDate);
    $formPrefill[] = array('Field' => 'create-date-day', 'Value' => $createDateArray[0]);
    $formPrefill[] = array('Field' => 'create-date-time', 'Value' => $createDateArray[1]);
    // CKEditor prefill needs to be done differently
    $cpBindings['Form']['CKEditor']['Body'] = $postBody;
} elseif ($articleData && $isEdit) {
    $formPrefill[] = array('Field' => 'title', 'Value' => $dbTitle);
    $formPrefill[] = array('Field' => 'permalink', 'Value' => $dbPermalink);
    if ($dbCategoryId != '0') {
        $formPrefill[] = array('Field' => 'category-id', 'Value' => $dbCategoryId);
    }
    $formPrefill[] = array('Field' => 'excerpt', 'Value' => $dbExcerpt);
    $formPrefill[] = array('Field' => 'link-url', 'Value' => $dbLinkUrl);
    $createDateArray = explode(' ', $dbCreateDate);
    $formPrefill[] = array('Field' => 'create-date-day', 'Value' => $createDateArray[0]);
    $formPrefill[] = array('Field' => 'create-date-time', 'Value' => $createDateArray[1]);
    // CKEditor prefill needs to be done differently
    $cpBindings['Form']['CKEditor']['Body'] = $dbContent;
}

$cpBindings['Form']['Errors'] = $formErrors;
$cpBindings['Form']['Prefill'] = $formPrefill;

$engine = $cmsContainer->getService('Theme.EngineCPanel');
$outputHtml = $engine->render($themeFile, $cpBindings);
print($outputHtml);
exit;
