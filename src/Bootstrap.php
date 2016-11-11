<?php

namespace ZWorkshop;

use IPC\Silex\Provider\PDOServiceProvider;
use Rpodwika\Silex\YamlConfigServiceProvider;
use Silex\Application;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;

use ZWorkshop\Services\PasswordEncoderService;
use ZWorkshop\Services\UserProvider;

class Bootstrap
{
    static public function init(Application $app)
    {
        //register config service provider
        $app->register(new YamlConfigServiceProvider(__DIR__ . '/../config/settings.yml'));

        //register PDO service provider
        $app->register(new PDOServiceProvider(), [
            'pdo.options' => $app['config']['pdo.options'],
        ]);

        //register Twig service provider
        $app->register(new TwigServiceProvider(), [
            'twig.path' => __DIR__ . '/../src/Views',
        ]);

        //register session service provider
        $app->register(new SessionServiceProvider());

        //register security service provider
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
                return new PasswordEncoderService();
            },
        ]);

        //register routing service provider; here we can find url_generator service
        $app->register(new RoutingServiceProvider());

        //set app debug mode
        $app['debug'] = $app['config']['debug'];
    }
}
