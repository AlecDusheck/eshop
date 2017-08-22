<?php

namespace eShop\controllers;

use PDO as PDO;
use eShop\classes\User;

class Controller {
    protected $container;
    protected $db;
    protected $user;
    protected $data;
    public function __construct($container)
    {
        $this->container = $container;

        //Setup database
        $dbsettings = $container->get('settings')['db'];
        $db = new PDO("mysql:host=".$dbsettings['host'].";dbname=".$dbsettings['name'],$dbsettings['user'],$dbsettings['pass']);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->db = $db;

        $user = new User($db);
        $this->user = $user;

        //Get variables required to load every page
        $this->data = new \stdClass;
        @$this->data->site->appName = $this->container->get('settings')['misc']['appName'];
        @$this->data->login->loggedin = $user->is_logged_in();
        @$this->data->login->isAdmin = $user->isAdmin();
        if($user->is_logged_in()){
            @$this->data->login->username = $_SESSION['username'];
        }
    }
    public function __get($prop)
    {
        if ($this->container->{$prop}) {
            return $this->container->{$prop};
        }
    }

}