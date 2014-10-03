<?php


namespace Cms\Core\Di;

use Cms\Data\Area\AreaRepository,
    Cms\Data\Setting\SettingRepository,
    Cms\Data\User\UserRepository,
    Cms\Data\UserSession\UserSessionRepository;


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

        $repoArea = new AreaRepository($pdo);
        $repoSetting = new SettingRepository($pdo);
        $repoUser = new UserRepository($pdo);
        $repoUserSession = new UserSessionRepository($pdo);

        $this->setupAuthLayer($repoUserSession, $repoUser);

        $cmsThemeEngine = new \Cms\Theme\Engine($themeCurrent, $engineCache);
        $themeEngine   = $cmsThemeEngine->getEngine();
        $themeEngineUT = $cmsThemeEngine->getEngineUnitTesting();

        $linkStyle = $repoSetting->getSettingLinkStyle();
        $iaOptimiser = new \Cms\Ia\Tools\OptimiseUrl();
        $iaLinkArticle = new \Cms\Ia\Link\ArticleLink($linkStyle, $iaOptimiser);

        $themeBinding = new \Cms\Theme\Binding();

        $serviceLocator = new ServiceLocator();
        if ($this->loggedInUser) {
            $serviceLocator->set('Auth.CurrentUser', $this->loggedInUser);
        }
        $serviceLocator->set('Cms.ThemeEngine', $cmsThemeEngine);
        $serviceLocator->set('IA.LinkArticle', $iaLinkArticle);
        $serviceLocator->set('Repo.Area', $repoArea);
        $serviceLocator->set('Repo.Setting', $repoSetting);
        $serviceLocator->set('Repo.User', $repoUser);
        $serviceLocator->set('Repo.UserSession', $repoUserSession);
        $serviceLocator->set('Theme.Engine', $themeEngine);
        $serviceLocator->set('Theme.EngineUT', $themeEngineUT);
        $serviceLocator->set('Theme.Binding', $themeBinding);

        return new Container($serviceLocator);
    }
} 