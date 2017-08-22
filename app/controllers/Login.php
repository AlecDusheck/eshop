<?php

namespace eShop\controllers;

use Slim\Views\Twig as View, PDO as PDO, Twig_SimpleFilter as custFunc;

class Login extends Controller
{

    public function index($request, $response)
    {
        if($this->user->is_logged_in()){
            return $response->withRedirect('/');
        }

        if(isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        if(isset($_SESSION['msg'])) {
            @$this->data->msgs = $_SESSION['msg'];
            unset($_SESSION['msg']);
        }

        @$this->data->page->name = "Login";
        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "login.twig");
    }

    public function logout($request, $response)
    {
        $this->user->logout();
        return $response->withRedirect('/login');
    }

    public function process($request, $response)
    {
        if($this->user->is_logged_in()){
            return $response->withRedirect('/');
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        if ($this->user->login($email, $password)) {
            return $response->withRedirect('/explore');
        } else {
            $error[] = "Invalid login.";
            $_SESSION['error'] = $error;
            return $response->withRedirect('/login');
        }
    }
}