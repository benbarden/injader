<?php


namespace Cms\Theme;

use Cms\Ia\Link\AreaLink,
    Cms\Ia\Link\ArticleLink,
    Cms\Ia\Link\UserLink,
    Cms\Data\Area\AreaRepository,
    Cms\Data\Article\ArticleRepository,
    Cms\Data\User\UserRepository,
    Cms\Data\User\User,
    Cms\Exception\Theme\EngineException;


class Engine
{
    /**
     * @var array
     */
    private $pathsArray;

    /**
     * @var array
     */
    private $cPanelPathsArray;

    /**
     * @var array
     */
    private $envArray;

    /**
     * @var string
     */
    private $publicThemePath;

    /**
     * @var AreaLink
     */
    private $iaLinkArea;

    /**
     * @var ArticleLink
     */
    private $iaLinkArticle;

    /**
     * @var UserLink
     */
    private $iaLinkUser;

    /**
     * @var ArticleRepository
     */
    private $repoArticle;

    /**
     * @var AreaRepository
     */
    private $repoArea;

    /**
     * @var UserRepository
     */
    private $repoUser;

    /**
     * @var User
     */
    private $loggedInUser;

    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @param string $current
     * @param integer $cache
     * @throws \Exception
     */
    public function __construct($current = "", $cache = 1)
    {
        // Validate theme path
        if (!$current) {
            throw new EngineException('Current theme not defined!');
        }
        $userThemePath = sprintf('%sthemes/user/%s', ABS_ROOT, $current);
        if (!is_dir($userThemePath)) {
            throw new EngineException(sprintf('Cannot find theme: %s', $userThemePath));
        }
        $this->pathsArray = array(
            $userThemePath,
            ABS_ROOT.'themes/system',
        );
        $this->cPanelPathsArray = array(
            ABS_ROOT.'themes/cpanel/injader',
        );

        // Set up caching
        if ($cache == 1) {
            $this->envArray = array('cache' => ABS_ROOT.'data/cache');
        } else {
            $this->envArray = array();
        }

        // Save the theme path
        $this->publicThemePath = sprintf('%sthemes/user/%s/', URL_ROOT, $current);

        // Instantiate Twig
        require_once ABS_ROOT.'/lib/Twig/Autoloader.php';
        \Twig_Autoloader::register();
    }

    public function setIALinkArea(AreaLink $iaLinkArea)
    {
        $this->iaLinkArea = $iaLinkArea;
    }

    public function setIALinkArticle(ArticleLink $iaLinkArticle)
    {
        $this->iaLinkArticle = $iaLinkArticle;
    }

    public function setIALinkUser(UserLink $iaLinkUser)
    {
        $this->iaLinkUser = $iaLinkUser;
    }

    public function setRepoArea(AreaRepository $repoArea)
    {
        $this->repoArea = $repoArea;
    }

    public function setRepoArticle(ArticleRepository $repoArticle)
    {
        $this->repoArticle = $repoArticle;
    }

    public function setRepoUser(UserRepository $repoUser)
    {
        $this->repoUser = $repoUser;
    }

    public function setLoggedInUser(User $user)
    {
        $this->loggedInUser = $user;
    }

    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * @return string
     */
    public function getPublicThemePath()
    {
        return $this->publicThemePath;
    }

    private function setupFunctions($twig)
    {
        // cmsBlock
        $funcBlock = new \Twig_SimpleFunction('cmsBlock',
            array($this, 'cmsBlock'),
            array('is_safe' => array('html'),
            'needs_environment' => true,
            'needs_context' => true
        ));
        $twig->addFunction($funcBlock);
        // cmsData
        $funcCmsDataContentRecent = new \Twig_SimpleFunction('cmsDataContentRecent',
            array($this, 'cmsDataContentRecent'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcCmsDataContentRecent);
        // cmsDomainFull
        $funcDomainFull = new \Twig_SimpleFunction('cmsDomainFull',
            array($this, 'cmsDomainFull'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcDomainFull);
        // cmsFormatDate
        $funcFormatDate = new \Twig_SimpleFunction('cmsFormatDate',
            array($this, 'cmsFormatDate'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcFormatDate);
        // cmsLink
        $funcLinkArticle = new \Twig_SimpleFunction('cmsLinkArticle',
            array($this, 'cmsLinkArticle'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcLinkArticle);
        $funcLinkArea = new \Twig_SimpleFunction('cmsLinkArea',
            array($this, 'cmsLinkArea'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcLinkArea);
        $funcLinkUser = new \Twig_SimpleFunction('cmsLinkUser',
            array($this, 'cmsLinkUser'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcLinkUser);
        $funcLinkPage = new \Twig_SimpleFunction('cmsLinkPage',
            array($this, 'cmsLinkPage'),
            array('is_safe' => array('html')
        ));
        $twig->addFunction($funcLinkPage);
        return $twig;
    }

    private function setupCPanelFunctions($twig)
    {
        // cpLink
        $funcLink = new \Twig_SimpleFunction('cpLink',
            array($this, 'cpLink'),
            array('is_safe' => array('html'))
        );
        $twig->addFunction($funcLink);
        return $twig;
    }

    public function cmsBlock(\Twig_Environment $twig, $context, $blockFile)
    {
        $urlThemeRoot = $context['URL']['ThemeRoot'];
        $absSysBlockPath  = sprintf(ABS_ROOT.'themes/system/blocks/%s.twig', $blockFile);
        $absUserBlockPath = sprintf(ABS_ROOT.$urlThemeRoot.'blocks/%s.twig', $blockFile);
        $relBlockPath = sprintf('blocks/%s.twig', $blockFile);
        if (file_exists($absSysBlockPath) || file_exists($absUserBlockPath)) {
            return $twig->render($relBlockPath, $context);
        } else {
            return sprintf('<p><strong>MISSING: %s</strong></p>', $blockFile);
        }
    }

    public function cmsDataContentRecent($limit)
    {
        $contentArray = $this->repoArticle->getRecentPublic($limit);
        return $contentArray;
    }

    public function cmsDomainFull()
    {
        return 'http://'.$_SERVER['HTTP_HOST'];
    }

    public function cmsFormatDate($date, $format = '')
    {
        if ($format) {
            return date($format, strtotime($date));
        } else {
            return date($this->dateFormat, strtotime($date));
        }
    }

    public function cmsLinkArticle($itemId)
    {
        $article = $this->repoArticle->getById($itemId);
        $areaId = $article->getContentAreaId();
        $area = $this->repoArea->getById($areaId);
        $this->iaLinkArticle->setArea($area);
        $this->iaLinkArticle->setArticle($article);
        $outputHtml = $this->iaLinkArticle->generate();
        return $outputHtml;
    }

    public function cmsLinkArea($itemId)
    {
        $area = $this->repoArea->getById($itemId);
        $this->iaLinkArea->setArea($area);
        $outputHtml = $this->iaLinkArea->generate();
        return $outputHtml;
    }

    public function cmsLinkUser($itemId)
    {
        $user = $this->repoUser->getById($itemId);
        $this->iaLinkUser->setUser($user);
        $outputHtml = $this->iaLinkUser->generate();
        return $outputHtml;
    }

    public function cmsLinkPage($link, $title = '', $tagOpen = 'li', $tagClose = 'li')
    {
        $outputHtml = ''; $url = '';

        $supportedLinkTypes = array('archives', 'register', 'login', 'logout', 'tagmap', 'cp');

        if (!in_array($link, $supportedLinkTypes)) {
            return sprintf('Unknown link: %s', $link);
        }

        switch ($link) {
            case 'archives':
                if (!$title) {
                    $title = 'Archives';
                }
                $url = URL_ROOT.'cms/archives';
                break;
            case 'register':
                if (!$title) {
                    $title = 'Register';
                }
                if (!$this->loggedInUser) {
                    $url = URL_ROOT.'register.php';
                }
                break;
            case 'login':
                if (!$title) {
                    $title = 'Login';
                }
                if (!$this->loggedInUser) {
                    $url = URL_ROOT.'login.php';
                }
                break;
            case 'logout':
                if (!$title) {
                    $title = 'Logout';
                }
                if ($this->loggedInUser) {
                    $url = URL_ROOT.'logout.php';
                }
                break;
            case 'tagmap':
                if (!$title) {
                    $title = 'Tag Map';
                }
                $url = URL_ROOT.'tagmap.php';
                break;
            case 'cp':
                if (!$title) {
                    $title = 'Control Panel';
                }
                if ($this->loggedInUser) {
                    $url = URL_ROOT.'cp/index.php';
                }
                break;
        }

        if ($url) {
            $outputHtml = sprintf('<%s><a href="%s">%s</a></%s>', $tagOpen, $url, $title, $tagClose);
        }
        return $outputHtml;
    }

    public function cpLink($link, $params = array())
    {
        $url = '';

        if (array_key_exists('action', $params)) {
            $action = $params['action'];
        } else {
            $action = "";
        }
        if (array_key_exists('id', $params)) {
            $id = $params['id'];
        } else {
            $id = "";
        }
        if (array_key_exists('type', $params)) {
            $type = $params['type'];
        } else {
            $type = "";
        }

        switch ($link) {
            case 'index':
                $url = URL_ROOT.'cp/index.php';
                break;
            case 'write':
                $url = sprintf(URL_ROOT.'cp/write.php?action=%s&id=%s', $action, $id);
                break;
            case 'users':
                $url = sprintf(URL_ROOT.'cp/users.php?action=%s', $action);
                break;
            case 'content_manage':
                $url = URL_ROOT.'cp/content_manage.php?area=0&status=0&user=';
                break;
            case 'edit_profile':
                $url = URL_ROOT.'cp/edit_profile.php';
                break;
            case 'change_password':
                $url = URL_ROOT.'cp/change_password.php';
                break;
            case 'manage_avatars':
                $url = URL_ROOT.'cp/manage_avatars.php';
                break;
            case 'categories':
                $url = URL_ROOT.'cp/categories.php';
                break;
            case 'category':
                $url = sprintf(URL_ROOT.'cp/category.php?action=%s&id=%s', $action, $id);
                break;
            case 'files':
                $url = sprintf(URL_ROOT.'cp/files.php?type=%s', $type);
                break;
            case 'file_add':
                $url = URL_ROOT.'cp/files_site_upload.php?action=create';
                break;
            // Settings
            case 'settings_general':
                $url = URL_ROOT.'cp/general_settings.php';
                break;
            case 'settings_content':
                $url = URL_ROOT.'cp/content_settings.php';
                break;
            case 'settings_file':
                $url = URL_ROOT.'cp/files_settings.php';
                break;
            case 'settings_url':
                $url = URL_ROOT.'cp/url_settings.php';
                break;
            case 'themes':
                $url = URL_ROOT.'cp/themes.php';
                break;
            case 'permissions':
                $url = URL_ROOT.'cp/permission.php?action=edit&id=1';
                break;
            case 'user_roles':
                $url = URL_ROOT.'cp/user_roles.php';
                break;
            // Tools
            case 'tools_user_sessions':
                $url = URL_ROOT.'cp/tools_user_sessions.php';
                break;
            case 'tools_access_log':
                $url = URL_ROOT.'cp/access_log.php';
                break;
            case 'tools_error_log':
                $url = URL_ROOT.'cp/error_log.php';
                break;
            // Catchall
            default:
                $url = 'ERROR::'.$link;
                break;
        }

        return $url;
    }

    /**
     * @return \Twig_Environment
     */
    public function getEngine()
    {
        $loader = new \Twig_Loader_Filesystem($this->pathsArray);
        $twig = new \Twig_Environment($loader, $this->envArray);
        $twig = $this->setupFunctions($twig);
        return $twig;
    }

    /**
     * @return \Twig_Environment
     */
    public function getEngineCPanel()
    {
        $loader = new \Twig_Loader_Filesystem($this->cPanelPathsArray);
        $twig = new \Twig_Environment($loader, $this->envArray);
        $twig = $this->setupFunctions($twig);
        $twig = $this->setupCPanelFunctions($twig);
        return $twig;
    }

    /**
     * @return \Twig_Environment
     */
    public function getEngineUnitTesting()
    {
        $loader = new \Twig_Loader_Array(array(
            'index.html' => '<p>Hello {{ Name }}!</p>',
        ));
        return new \Twig_Environment($loader);
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        unset($this->engine);
        unset($this->iaLinkArea);
        unset($this->iaLinkArticle);
        unset($this->iaLinkUser);
        unset($this->loggedInUser);
        unset($this->repoArea);
        unset($this->repoArticle);
        unset($this->repoUser);
    }
} 