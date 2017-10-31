<?php

declare(strict_types=1);

/*
 * This file is part of Cyberschall.
 *
 * (c) Max Stockner <mail@bluemaex.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Bluemaex\Cyberschall\Environment;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';

\Symfony\Component\Debug\ErrorHandler::register();
\Symfony\Component\HttpFoundation\Request::setTrustedProxies(['10.0.0.0/8']);
error_reporting(error_reporting() & ~E_USER_DEPRECATED);
date_default_timezone_set('UTC');

$environment = Environment::getenv('APP_ENV');

// Create the Application
$app = new \Silex\Application(
    [
        'app.name' => 'cyberschall-api',
        'app.environment' => $environment,
        'app.root_dir' => dirname(__DIR__),
        'app.version' => '0.0.7-tripod',
        'debug' => 'TESTING' === $environment || 'DEVELOPMENT' === $environment,
    ]
);
$app->register(new \Silex\Provider\ServiceControllerServiceProvider());
$app->register(new \Silex\Provider\ValidatorServiceProvider());

// Logging + ErrorHandler
$app->register(new \Bluemaex\Cyberschall\Monolog\Provider(), [
    'monolog.name' => Environment::getenv('LOG_NAME', $app['app.name']),
    'monolog.level' => Environment::getenv('LOG_LEVEL', 'DEBUG'),
    'monolog.threshold' => Environment::getenv('LOG_THRESHOLD', 'DEBUG'),
]);
$app->register(new \Bluemaex\Cyberschall\Monolog\ErrorHandlerProvider());

// Database and orm
$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'dbs.options' => [
        'cyberschall' => [
            'url' => Environment::getenv('DATABASE_DSN'),
        ],
    ],
]);
$app->register(new \Bluemaex\Cyberschall\Doctrine\Provider(), [
    'orm.ems.default' => 'cyberschall',
    'orm.ems.options' => [
        'cyberschall' => [
            'debug' => $app['debug'],
            'proxy_namespace' => 'Proxy\\Cyberschall\\',
            'proxy_dir' => dirname(__DIR__).'/../var/Doctrine/Cyberschall/',
            'doctrine.paths' => [dirname(__DIR__).'/src/Entity'],
            'listeners' => [],
        ],
    ],
]);

// security firewall
$app->register(new \Bluemaex\Cyberschall\Security\Jwt\Provider(), [
    'security.jwt.secret' => Environment::getenv('JWT_SECRET'),
]);
$app->register(new \Bluemaex\Cyberschall\Security\Provider());

// Cors + HAL
$app->register(new JDesrosiers\Silex\Provider\CorsServiceProvider());
$app['cors-enabled']($app);

// Register Application Services
$app->register(new \Bluemaex\Cyberschall\Auth\Provider());
$app->register(new \Bluemaex\Cyberschall\Index\Provider());

// json middleware:
// if it is a JSON request, parse the JSON
$app->before(function (Request $request): void {
    if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : []);
    }
});

return $app;
