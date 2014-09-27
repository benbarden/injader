<?php


namespace Cms\Core\Di;

use Cms\Data\User\UserRepository,
    Cms\Data\Area\AreaRepository;


class Factory
{
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
        $repoUser = new UserRepository($pdo);

        $cmsThemeEngine = new \Cms\Theme\Engine($themeCurrent, $engineCache);
        $themeEngine   = $cmsThemeEngine->getEngine();
        $themeEngineUT = $cmsThemeEngine->getEngineUnitTesting();

        $themeBinding = new \Cms\Theme\Binding();

        $serviceLocator = new ServiceLocator();
        $serviceLocator->set('Repo.Area', $repoArea);
        $serviceLocator->set('Repo.User', $repoUser);
        $serviceLocator->set('Theme.Engine', $themeEngine);
        $serviceLocator->set('Theme.EngineUT', $themeEngineUT);
        $serviceLocator->set('Theme.Binding', $themeBinding);

        return new Container($serviceLocator);
    }
} 