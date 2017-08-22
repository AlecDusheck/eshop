<?php

namespace eShop\classes;

use PDO as PDO;

include('password.php');

class User extends Password
{
    private $_db;

    function __construct($db)
    {
        parent::__construct();
        $this->_db = $db;
    }

    private function get_user_hash($email)
    {
        $stmt = $this->_db->prepare('SELECT password, username, memberID, email FROM members WHERE email = :email');
        $stmt->execute(array(':email' => $email));
        return $stmt->fetch();
    }

    public function login($email, $password)
    {
        $row = $this->get_user_hash($email);
        if ($this->password_verify($password, $row['password']) == 1) {
            $_SESSION['loggedin'] = true;
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['memberID'] = $row['memberID'];
            return true;
        }
    }

    public function isAdmin(){
        if(!$this->is_logged_in()){
            return false;
        }

        $stmt = $this->_db->prepare('SELECT isAdmin FROM members WHERE memberID = :id');
        $stmt->execute(array(':id' => $_SESSION['memberID']));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if($row['isAdmin'] == 'true'){
            return true;
        }
        return false;
    }

    public function logout()
    {
        session_destroy();
    }

    public function is_logged_in()
    {
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
            return true;
        }
    }
}
