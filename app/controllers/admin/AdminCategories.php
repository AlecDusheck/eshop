<?php

namespace eShop\controllers\admin;

use eShop\classes\Mobile_Detect;
use eShop\controllers\Controller as Controller;
use PDO as PDO;

class AdminCategories extends Controller
{

    public function index($request, $response)
    {

        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            return $response->withRedirect('/');
        }

        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        @$this->data->page->name = "Categories";

        $getCategories = $this->db->prepare('SELECT categoryID, categoryName FROM categories');
        $getCategories->execute();
        $result = $getCategories->fetchAll();
        $categories = [];
        foreach ($result as $category) {
            $info = [
                "name" => $category['categoryName'],
                "id" => $category['categoryID']
            ];

            array_push($categories, $info);
        }
        @$this->data->categories->list = $categories;

        if (isset($_SESSION['msg'])) {
            @$this->data->msgs = $_SESSION['msg'];
            unset($_SESSION['msg']);
        }

        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "admin/categories/categories.twig");
    }

    public function add($request, $response)
    {
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            return $response->withRedirect('/');
        }

        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        if (isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        @$this->data->page->name = "Add Category";

        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "admin/categories/add.twig");
    }

    public function addprocess($request, $response)
    {
        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        $categoryName = $_POST['name'];
        $categoryDesc = $_POST['desc'];

        if (empty($categoryName) || empty($categoryDesc)) {
            $error[] = "Please fill out all the fields.";
        } else if (str_replace($categoryName >= 255) || str_replace($categoryDesc >= 255)) {
            $error[] = "You issued a request that was too large. Please shorten the length of your fields.";
        }

        if (!isset($error)) {

            if (isset($_POST['isHot']) && $_POST['isHot'] == 'Yes') {
                $isHot = 'true';
            } else {
                $isHot = 'false';
            }

            //insert into database
            $saveCategory = $this->db->prepare('INSERT INTO categories (categoryName,categoryDesc, isHot) VALUES (:name, :desc, :isHot)');
            $saveCategory->execute(array(
                ':name' => $categoryName,
                ':desc' => $categoryDesc,
                ':isHot' => $isHot
            ));
            $msg[] = "Category added.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/admin/categories');
        }
        $_SESSION['error'] = $error;
        return $response->withRedirect('/admin/categories/add');
    }

    public function remove($request, $response)
    {
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            return $response->withRedirect('/');
        }

        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        if (isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        $getCategories = $this->db->prepare('SELECT categoryID, categoryName FROM categories');
        $getCategories->execute();
        $result = $getCategories->fetchAll();
        $categories = [];
        foreach ($result as $category) {
            $info = [
                "name" => $category['categoryName'],
                "id" => $category['categoryID']
            ];

            array_push($categories, $info);
        }
        @$this->data->categories->list = $categories;

        @$this->data->page->name = "Remove Category";

        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "admin/categories/remove.twig");
    }

    public function removeprocess($request, $response)
    {
        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        $categoryID = $_POST['category'];

        if (empty($categoryID)) {
            $error[] = "Please select a category.";
        } else {
            //Ensure category exists
            $checkIfCategoryExists = $this->db->prepare('SELECT categoryID FROM categories WHERE categoryID = :id');
            $checkIfCategoryExists->execute(array(':id' => $categoryID));
            $row = $checkIfCategoryExists->fetch(PDO::FETCH_ASSOC);
            if (empty($row['categoryID'])) {
                $error[] = "Category dose not exist.";
            }
        }

        if (!isset($error)) {
            //Remove
            $delete = $this->db->prepare('DELETE FROM categories WHERE categoryID = :id');
            $delete->execute(array(':id' => $categoryID));

            //TODO remove items (i havent even done that yet.
            $msg[] = "Category removed.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/admin/categories');
        }
        $_SESSION['error'] = $error;
        return $response->withRedirect('/admin/categories/remove');
    }

    public function edit($request, $response)
    {
        $detect = new Mobile_Detect();
        if ($detect->isMobile()) {
            return $response->withRedirect('/');
        }

        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        if (isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        $id = $_GET['id'];

        if(empty($id)){
            return $response->withRedirect('/admin/categories');
        }

        //Ensure category exists
        $checkIfCategoryExists = $this->db->prepare('SELECT categoryID FROM categories WHERE categoryID = :id');
        $checkIfCategoryExists->execute(array(':id' => $id));
        $row = $checkIfCategoryExists->fetch(PDO::FETCH_ASSOC);
        if (empty($row['categoryID'])) {
            return $response->withRedirect('/admin/categories');
        }

        //Query and define data
        $getData = $this->db->prepare('SELECT categoryName, categoryDesc, isHot, categoryID FROM categories WHERE categoryID = :id');
        $getData->execute(array(':id' => $id));
        $row = $getData->fetch(PDO::FETCH_ASSOC);
        @$this->data->category->name = $row['categoryName'];
        @$this->data->category->desc = $row['categoryDesc'];
        @$this->data->category->isHot = $row['isHot'];

        @$this->data->page->name = "Edit category '".$row['categoryName']."''";

        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "admin/categories/edit.twig");
    }

    public function editprocess($request, $response)
    {
        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        $id = $_GET['id'];

        if(empty($id)){
            return $response->withRedirect('/admin/categories');
        }

        //Ensure category exists
        $checkIfCategoryExists = $this->db->prepare('SELECT categoryID FROM categories WHERE categoryID = :id');
        $checkIfCategoryExists->execute(array(':id' => $id));
        $row = $checkIfCategoryExists->fetch(PDO::FETCH_ASSOC);
        if (empty($row['categoryID'])) {
            return $response->withRedirect('/admin/categories');
        }

        $categoryName = $_POST['name'];
        $categoryDesc = $_POST['desc'];

        if (empty($categoryName) || empty($categoryDesc)) {
            $error[] = "Please fill out all the fields.";
        } else if (str_replace($categoryName >= 255) || str_replace($categoryDesc >= 255)) {
            $error[] = "You issued a request that was too large. Please shorten the length of your fields.";
        }
        if (!isset($error)) {

            if (isset($_POST['isHot']) && $_POST['isHot'] == 'Yes') {
                $isHot = 'true';
            } else {
                $isHot = 'false';
            }

            //update database
            $saveCategory = $this->db->prepare('UPDATE categories SET categoryName=:name,categoryDesc=:desc, isHot=:isHot where categoryID=:id');
            $saveCategory->execute(array(
                ':name' => $categoryName,
                ':desc' => $categoryDesc,
                ':isHot' => $isHot,
                ':id' => $id
            ));
            $msg[] = "Category edited.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/admin/categories');
        }
        $_SESSION['error'] = $error;
        return $response->withRedirect('/admin/categories/edit');
    }
}