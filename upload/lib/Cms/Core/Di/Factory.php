<?php


namespace Cms\Core\Di;

use Cms\Data\User\UserRepository;


class Factory
{
    public function buildContainer(Config $config)
    {
        $dsn  = $config->getByKey('Database.DSN');
        $user = $config->getByKey('Database.User');
        $pw   = $config->getByKey('Database.Pass');

        $pdo = new \PDO($dsn, $user, $pw);

        $serviceLocator = new ServiceLocator();

        $repoUser = new UserRepository($pdo);
        $serviceLocator->set('Repo.User', $repoUser);

        $themeBinding = new \Cms\Theme\Binding();
        $serviceLocator->set('Theme.Binding', $themeBinding);

        return new Container($serviceLocator);
    }
} 