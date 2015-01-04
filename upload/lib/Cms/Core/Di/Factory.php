<?php


namespace Cms\Core\Di;

use Cms\Data\AccessLog\AccessLogRepository,
    Cms\Data\Area\AreaRepository,
    Cms\Data\Article\ArticleRepository,
    Cms\Data\Setting\SettingRepository,
    Cms\Data\User\UserRepository,
    Cms\Data\UserSession\UserSessionRepository;
use Cms\Data\Category\CategoryRepository;


class Factory
{
    /**
     * @var \Cms\Data\User\User
     */
    private $loggedInUser;

    /**
     * @param UserSessionRepository $repoUserSession
     * @param UserRepository $repoUser
     */
    private function setupAuthLayer(
        UserSessionRepository $repoUserSession,
        UserRepository $repoUser)
    {
        $accessLogin = new \Cms\Access\Login();
        $cookie = $accessLogin->getCookie();
        $userId = $repoUserSession->getValidUserId($cookie);
        if ($userId) {
            $user = $repoUser->getById($userId);
            if ($user) {
                $this->loggedInUser = $user;
            }
        }
    }

    public function buildContainer(Config $config)
    {
        $dsn  = $config->getByKey('Database.DSN');
        $user = $config->getByKey('Database.User');
        $pw   = $config->getByKey('Database.Pass');

        $themeCurrent = $config->getByKey('Theme.Current');
        $themeCache   = $config->getByKey('Theme.Cache');
        $engineCache  = ($themeCache == 'On') ? 1 : 0;

        $pdo = new \PDO($dsn, $user, $pw);

        $repoAccessLog = new AccessLogRepository($pdo);
        $repoArea = new AreaRepository($pdo);
        $repoArticle = new ArticleRepository($pdo);
        $repoCategory = new CategoryRepository($pdo);
        $repoSetting = new SettingRepository($pdo);
        $repoUser = new UserRepository($pdo);
        $repoUserSession = new UserSessionRepository($pdo);

        $this->setupAuthLayer($repoUserSession, $repoUser);

        $dateFormat = $repoSetting->getDateFormat();
        $linkStyle  = $repoSetting->getSettingLinkStyle();

        $iaOptimiser   = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLinkArticle = new \Cms\Ia\Link\ArticleLink($linkStyle, $iaOptimiser);
        $iaLinkArea    = new \Cms\Ia\Link\AreaLink($linkStyle, $iaOptimiser);
        $iaLinkUser    = new \Cms\Ia\Link\UserLink($linkStyle, $iaOptimiser);

        $themeBinding = new \Cms\Theme\Binding();

        $cmsThemeEngine = new \Cms\Theme\Engine($themeCurrent, $engineCache);
        $cmsThemeEngine->setIALinkArea($iaLinkArea);
        $cmsThemeEngine->setIALinkArticle($iaLinkArticle);
        $cmsThemeEngine->setIALinkUser($iaLinkUser);
        $cmsThemeEngine->setRepoArea($repoArea);
        $cmsThemeEngine->setRepoArticle($repoArticle);
        $cmsThemeEngine->setRepoUser($repoUser);
        if ($this->loggedInUser) {
            $cmsThemeEngine->setLoggedInUser($this->loggedInUser);
        }
        $cmsThemeEngine->setDateFormat($dateFormat);
        $themeEngine       = $cmsThemeEngine->getEngine();
        $themeEngineCPanel = $cmsThemeEngine->getEngineCPanel();
        $themeEngineUT     = $cmsThemeEngine->getEngineUnitTesting();

        $serviceLocator = new ServiceLocator();
        if ($this->loggedInUser) {
            $serviceLocator->set('Auth.CurrentUser', $this->loggedInUser);
        }
        $serviceLocator->set('Cms.Config', $config);
        $serviceLocator->set('Cms.ThemeEngine', $cmsThemeEngine);
        $serviceLocator->set('IA.LinkArea', $iaLinkArea);
        $serviceLocator->set('IA.LinkArticle', $iaLinkArticle);
        $serviceLocator->set('Repo.AccessLog', $repoAccessLog);
        $serviceLocator->set('Repo.Area', $repoArea);
        $serviceLocator->set('Repo.Article', $repoArticle);
        $serviceLocator->set('Repo.Category', $repoCategory);
        $serviceLocator->set('Repo.Setting', $repoSetting);
        $serviceLocator->set('Repo.User', $repoUser);
        $serviceLocator->set('Repo.UserSession', $repoUserSession);
        $serviceLocator->set('Theme.Engine', $themeEngine);
        $serviceLocator->set('Theme.EngineCPanel', $themeEngineCPanel);
        $serviceLocator->set('Theme.EngineUT', $themeEngineUT);
        $serviceLocator->set('Theme.Binding', $themeBinding);

        $container = new Container($serviceLocator);

        // Save some settings
        $container->saveSetting('DateFormat', $dateFormat);
        $container->saveSetting('LinkStyle', $linkStyle);

        return $container;
    }
} 