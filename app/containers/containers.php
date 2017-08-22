<?php

// Twig view dependency
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig("../app/views", [
        "cache" => false,
    ]);

    $view->addExtension(new \Slim\Views\TwigExtension(
        $container->router,
        $container->request->getUri()
    ));
    $view->addExtension(new Twig_Extension_Debug());
    $twig = $view->getEnvironment();
    return $view;
};

//Page - no auth

$container['home'] = function ($container) {
    return new \eShop\controllers\Home($container);
};

$container['login'] = function ($container) {
    return new \eShop\controllers\Login($container);
};

$container['register'] = function ($container) {
    return new \eShop\controllers\Register($container);
};

$container['explore'] = function ($container) {
    return new \eShop\controllers\Explore($container);
};

$container['reset'] = function ($container) {
    return new \eShop\controllers\PasswordReset($container);
};

$container['notFoundHandler'] = function ($c) {
    return new \eShop\handlers\NotFound($c->get('view'), function ($request, $response) use ($c) {
        return $c['response']
            ->withStatus(404);
    });
};

//Page - auth
$container['adminhome'] = function ($container) {
    return new \eShop\controllers\admin\AdminHome($container);
};

$container['admincategories'] = function ($container) {
    return new \eShop\controllers\admin\AdminCategories($container);
};

$container['admincustomization'] = function ($container) {
    return new \eShop\controllers\admin\AdminCustomization($container);
};