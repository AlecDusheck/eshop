<?php

namespace eShop\controllers;

use Slim\Views\Twig as View, PDO as PDO, Twig_SimpleFilter as custFunc;

class Home extends Controller {

    public function index($request, $response)
    {
        @$this->data->page->name = "Home";
        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "home.twig");
    }
}