<?php

namespace eShop\controllers;

use Slim\Views\Twig as View, PDO as PDO, Twig_SimpleFilter as custFunc;

class Register extends Controller
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

        @$this->data->page->name = "Register";
        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "register.twig");
    }

    public function process($request, $response)
    {
        if($this->user->is_logged_in()){
            return $response->withRedirect('/');
        }

        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        if(empty($name) ||
            empty($email) ||
            empty($password) ||
            empty($password2)){
            $error[] = "Please fill out all the fields.";
        }else {
            if(strlen($name) >= 255 ||
                strlen($email) >= 255 ||
                strlen($password) >= 255 ||
                strlen($password2) >= 255){
                $error[] = "You issued a request that was too large. Please shorten the length of your fields.";
            }else {
                //Basic Validation
                if (strlen($password) < 6) {
                    $error[] = "Your password must be greater then 6 characters.";
                }

                if ($password != $password2) {
                    $error[] = "Your passwords did not match.";
                }

                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error[] = "Please enter a valid email.";
                } else {
                    //Its a valid email so lets make sure it isn't in use.
                    $checkEmail = $this->db->prepare('SELECT email FROM members WHERE email = :email');
                    $checkEmail->execute(array(':email' => $_POST['email']));
                    $row = $checkEmail->fetch(PDO::FETCH_ASSOC);
                    if (!empty($row['email'])) {
                        $error[] = 'Email provided is already in use.';
                    }
                }
            }
        }

        if(!isset($error)){
            //Hash the users password
            $hashedPwd = $this->user->password_hash($password, PASSWORD_BCRYPT);

            //insert into database
            $saveUser = $this->db->prepare('INSERT INTO members (username,password,email) VALUES (:username, :password, :email)');
            $saveUser->execute(array(
                ':username' => $name,
                ':password' => $hashedPwd,
                ':email' => $email
            ));

            $emailSend = new \eShop\classes\Email($this->container);
            $emailSend->sendWelcomeMessage($email, $name);

            $msg[] = "You are now registered. Please login.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/login');
        }
        $_SESSION['error'] = $error;
        return $response->withRedirect('/register');

    }
}