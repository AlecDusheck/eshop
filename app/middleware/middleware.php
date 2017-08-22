<?php

namespace eShop\middleware;

class Middleware{
    protected $container;
    public function __construct($container) {
        $this->container = $container;
    }
}