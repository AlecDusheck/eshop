<?php

//Normal Routes --------------------------------------------
//index route
$app->get("/", "home:index");

//login routes
$app->get("/login", "login:index");
$app->post("/login", "login:process");
$app->get("/logout", "login:logout");

//register routes
$app->get("/register", "register:index");
$app->post("/register", "register:process");

//shop routes
$app->get("/explore", "explore:index");

//password reset routes
$app->get('/reset', "reset:index");
$app->post('/reset', "reset:process");
$app->get('/reset/token', "reset:resetpassword");
$app->post('/reset/token', "reset:resetpasswordprocess");

//Admin Routes --------------------------------------------
//dashboard route
$app->get("/admin/home", "adminhome:index");

//categories routes
$app->get("/admin/categories", "admincategories:index");
$app->get("/admin/categories/add", "admincategories:add");
$app->post("/admin/categories/add", "admincategories:addprocess");
$app->get("/admin/categories/remove", "admincategories:remove");
$app->post("/admin/categories/remove", "admincategories:removeprocess");
$app->get("/admin/categories/edit", "admincategories:edit");
$app->post("/admin/categories/edit", "admincategories:editprocess");

//customization routes
$app->get("/admin/customization", "admincustomization:index");
$app->post("/admin/customization", "admincustomization:process");