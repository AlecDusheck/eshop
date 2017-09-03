<?php

//Config
$settings = [
    'db' => [
        'host' => 'localhost', /* This line is changeable. */
        'user' => 'mccode', /* This line is changeable. */
        'pass' => 'mzIXEyW1Lmsq1zj2', /* This line is changeable. */
        'name' => 'eshop' /* This line is changeable. */
    ],
    'misc' => [
        'email' => 'example@example.com', /* This line is changeable. */
        'appName' => 'eShop Demo', /* This line is changeable. */
        'shopName' => 'Demo Shop', /* This line is changeable. */
        'siteRoot' => (!empty($_SERVER['HTTPS']) ? 'https':'http').'://'.$_SERVER['HTTP_HOST']
    ],
    'paypal' => [
        'clientid' => 'CLIENTID', /* This line is changeable. */
        'secret' => 'SECRET' /* This line is changeable. */
    ],
    'email' => [
        'host' => 'smtp.gmail.com', /* This line is changeable. */
        'username' => 'a.dusheckirl@gmail.com', /* This line is changeable. */
        'password' => 'ots544fdddswgqfj', /* This line is changeable. */
        'port' => '587', /* TLS PORT - This line is changeable.*/
        'email' => 'a.dusheckirl@gmail.com' /* This line is changeable. */
    ],
    'displayErrorDetails' => true /* Error messages toggle - This line is changeable. */
];


use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Load all composer libs
include_once '../vendor/autoload.php';

//Define global site root
$site_root = (!empty($_SERVER['HTTPS']) ? 'https':'http').'://'.$_SERVER['HTTP_HOST'];
define('SITE_URL', $site_root);

//Start session
ob_start();
session_start();

$config = [
    'settings' => $settings
];

//Get our app running
$app = new \Slim\App($config);

$container = $app->getContainer();

include_once __DIR__ . '/containers/containers.php';
include_once __DIR__ . '/routes/routes.php';

include_once __DIR__ . '/middleware/gsMiddleware.php';
$app->add(new eShop\middleware\gsMiddleware($container));
