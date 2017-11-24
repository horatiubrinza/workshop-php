<?php

namespace ZWorkshop;

use IPC\Silex\Provider\PDOServiceProvider;
use Rpodwika\Silex\YamlConfigServiceProvider;
use Silex\Application;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Security\Core\Encoder\PlaintextPasswordEncoder;
use ZWorkshop\Services\UserProvider;

/**
 * The application bootstrap.
 */
class Bootstrap
{
    /**
     * Initializes the application.
     *
     * @param Application $app
     */
    public static function init(Application $app): void
    {
        // Register config service provider.
        $app->register(new YamlConfigServiceProvider(__DIR__.'/../config/settings.yml'));

        // Register PDO service provider.
        $app->register(new PDOServiceProvider(), ['pdo.options' => $app['config']['pdo.options']]);

        // Register Twig service provider.
        $app->register(new TwigServiceProvider(), ['twig.path' => __DIR__ . '/../src/Views']);

        // Register session service provider.
        $app->register(new SessionServiceProvider());

        // Register security service provider.
        $app->register(new SecurityServiceProvider(), [
            'security.firewalls'       => [
                'admin' => [
                    'pattern' => '^/admin',
                    'form'    => [
                        'login_path' => '/login',
                        'check_path' => '/admin/login_check',
                        'default_target_path' => '/admin',
                    ],
                    'logout'  => [
                        'logout_path'        => '/admin/logout',
                        'invalidate_session' => true
                    ],
                    'users'   => function () use ($app) {
                        return new UserProvider($app['pdo.connection']);
                    },
                ]
            ],
            'security.default_encoder' => function () {
                return new PlaintextPasswordEncoder();
            },
        ]);

        // Register routing service provider. Here we can find url_generator service.
        $app->register(new RoutingServiceProvider());

        // Set app debug mode.
        $app['debug'] = $app['config']['debug'];
    }
}
