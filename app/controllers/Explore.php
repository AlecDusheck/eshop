<?php

namespace eShop\controllers;

use Slim\Views\Twig as View, PDO as PDO, Twig_SimpleFilter as custFunc;

class Explore extends Controller {

    public function index($request, $response)
    {
        @$this->data->page->name = "Explore";

        $getCategories = $this->db->prepare('SELECT categoryID, categoryName, isHot FROM categories');
        $getCategories->execute();
        $result = $getCategories->fetchAll();
        $categories = [];
        foreach ($result as $category) {
            $info = [
                "name" => $category['categoryName'],
                "id" => $category['categoryID'],
                "isHot" => $category['isHot']
            ];

            array_push($categories, $info);
        }
        @$this->data->categories->list = $categories;

        //Banner #1
        $getBanner1 = $this->db->prepare("SELECT content, btn_content, btn_link, enabled FROM banners WHERE name = 'explr1'");
        $getBanner1->execute();
        $row = $getBanner1->fetch(PDO::FETCH_ASSOC);
        @$this->data->banners->banner1->content = $row['content'];
        @$this->data->banners->banner1->btn_content = $row['btn_content'];
        @$this->data->banners->banner1->btn_link = $row['btn_link'];
        @$this->data->banners->banner1->enabled = $row['enabled'];

        //Banner #1
        $getBanner2 = $this->db->prepare("SELECT content, btn_content, btn_link, enabled FROM banners WHERE name = 'explr2'");
        $getBanner2->execute();
        $row = $getBanner2->fetch(PDO::FETCH_ASSOC);
        @$this->data->banners->banner2->content = $row['content'];
        @$this->data->banners->banner2->btn_content = $row['btn_content'];
        @$this->data->banners->banner2->btn_link = $row['btn_link'];
        @$this->data->banners->banner2->enabled = $row['enabled'];


        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "explore.twig");
    }
}