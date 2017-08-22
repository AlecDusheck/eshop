<?php

namespace eShop\controllers;

use PDO as PDO;

class PasswordReset extends Controller
{

    public function index($request, $response)
    {
        if ($this->user->is_logged_in()) {
            return $response->withRedirect('/');
        }

        if (isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        if (isset($_SESSION['msg'])) {
            @$this->data->msgs = $_SESSION['msg'];
            unset($_SESSION['msg']);
        }

        @$this->data->page->name = "Password Reset";
        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "reset.twig");
    }

    public function process($request, $response)
    {
        if ($this->user->is_logged_in()) {
            return $response->withRedirect('/');
        }

        $email = $_POST['email'];

        if (empty($email)) {
            $error[] = "Please fill out all the fields.";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error[] = "Please enter a valid email.";
        }

        if (!isset($error)) {
            //We don't tell the user if an email is attached to the account.

            $checkEmail = $this->db->prepare('SELECT * FROM members WHERE email=:email');
            $checkEmail->execute(array(':email' => $email));
            $row = $checkEmail->fetch(PDO::FETCH_ASSOC);
            $expireDate = date('Y-m-d H:i:s', strtotime("+1 day"));
            if (!empty($row['email'])) {
                $token = md5(uniqid(rand(), true));
                $setToken = $this->db->prepare("UPDATE members SET resetToken = :token, resetExpire = :expireDate, resetComplete='No' WHERE email = :email");
                $setToken->execute(array(
                    ':email' => $email,
                    ':token' => $token,
                    ':expireDate' => $expireDate
                ));

                $getName = $this->db->prepare('SELECT username FROM members WHERE email=:email');
                $getName->execute(array(':email' => $email));
                $row = $getName->fetch(PDO::FETCH_ASSOC);

                $link = $this->container->get('settings')['misc']['siteRoot'] . "/reset/token?token=" . $token;
                $emailSend = new \eShop\classes\Email($this->container);
                $emailSend->sendRecoveryMessage($email, $row['name'], $link);

            }
            $msg[] = "If an account is attached to the email, you were emailed a recovery link.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/reset');
        }
        $_SESSION['error'] = $error;
        return $response->withRedirect('/reset');
    }

    public function resetpassword($request, $response)
    {
        if ($this->user->is_logged_in()) {
            return $response->withRedirect('/');
        }

        if (isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        $checkToken = $this->db->prepare('SELECT resetToken, resetComplete FROM members WHERE resetToken = :token');
        $checkToken->execute(array(':token' => $_GET['token']));
        $row = $checkToken->fetch(PDO::FETCH_ASSOC);

        $today = date('Y-m-d H:i:s');
        $expire = $row['resetExpire'];
        $today_time = strtotime($today);
        $expire_time = strtotime($expire);


        if (empty($row['resetToken'])) {
            $error[] = 'Invalid token provided, please use the link provided in the reset email.';
        } elseif ($row['resetComplete'] == 'Yes') {
            $error[] = 'Your password has already been changed!';
        } elseif ($expire_time < $today_time){
            $error[] = 'Your reset has expired. Please try again.';
        }

        if (!isset($error)) {
            @$this->data->page->name = "Password Reset";
            $this->container->view->getEnvironment()->addGlobal('data', $this->data);
            return $this->view->render($response, "reset2.twig");
        }

        $_SESSION['error'] = $error;
        return $response->withRedirect('/reset');
    }

    public function resetpasswordprocess($request, $response)
    {
        if ($this->user->is_logged_in()) {
            return $response->withRedirect('/');
        }

        $token = $_GET['token'];

        $checkToken = $this->db->prepare('SELECT resetToken, resetComplete FROM members WHERE resetToken = :token');
        $checkToken->execute(array(':token' => $token));
        $row = $checkToken->fetch(PDO::FETCH_ASSOC);

        $today = date('Y-m-d H:i:s');
        $expire = $row['resetExpire'];
        $today_time = strtotime($today);
        $expire_time = strtotime($expire);

        if (empty($row['resetToken'])) {
            return $response->withRedirect('/');
        } elseif ($row['resetComplete'] == 'Yes') {
            return $response->withRedirect('/');
        } elseif ($expire_time < $today_time){
            return $response->withRedirect('/');
        }

        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        if ($password > 6) {
            $error[] = "Your password must be greater then 6 characters.";
        }

        if ($password != $password2) {
            $error[] = "Your passwords did not match.";
        }

        if (!isset($error)) {
            $hashedpassword = $this->user->password_hash($password, PASSWORD_BCRYPT);
            $updatePassword = $this->db->prepare("UPDATE members SET password = :hashedpassword, resetComplete = 'Yes'  WHERE resetToken = :token");
            $updatePassword->execute(array(
                ':hashedpassword' => $hashedpassword,
                ':token' => $token
            ));

            $msg[] = "Your password has been updated.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/login');
        }

        $_SESSION['error'] = $error;
        return $response->withRedirect('/reset/token?token=' . $token);
    }
}