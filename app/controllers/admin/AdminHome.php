<?php

namespace eShop\controllers\admin;

use Slim\Views\Twig as View, PDO as PDO, Twig_SimpleFilter as custFunc, eShop\controllers\Controller as Controller;
use eShop\classes\Mobile_Detect;

class AdminHome extends Controller {

    public function index($request, $response)
    {

        $detect = new Mobile_Detect();
        if($detect->isMobile()){
            return $response->withRedirect('/');
        }

        if(!$this->user->isAdmin()){
            return $response->withRedirect('/');
        }

        @$this->data->page->name = "Admin Home";
        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "admin/home.twig");
    }
}