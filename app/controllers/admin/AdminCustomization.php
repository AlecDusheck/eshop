<?php

namespace eShop\controllers\admin;

use eShop\classes\Mobile_Detect;
use eShop\controllers\Controller as Controller;
use PDO as PDO;

class AdminCustomization extends Controller
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

        //Query and define data (banner 1)
        $getData = $this->db->prepare("SELECT content, btn_content, btn_link, enabled FROM banners WHERE name='explr1'");
        $getData->execute();
        $row = $getData->fetch(PDO::FETCH_ASSOC);
        @$this->data->banners->explr1->content = $row['content'];
        @$this->data->banners->explr1->btn_content = $row['btn_content'];
        @$this->data->banners->explr1->btn_link = $row['btn_link'];
        @$this->data->banners->explr1->enabled = $row['enabled'];

        //Query and define data (banner 2)
        $getData = $this->db->prepare("SELECT content, btn_content, btn_link, enabled FROM banners WHERE name='explr2'");
        $getData->execute();
        $row = $getData->fetch(PDO::FETCH_ASSOC);
        @$this->data->banners->explr2->content = $row['content'];
        @$this->data->banners->explr2->btn_content = $row['btn_content'];
        @$this->data->banners->explr2->btn_link = $row['btn_link'];
        @$this->data->banners->explr2->enabled = $row['enabled'];

        @$this->data->page->name = "Customization";

        if (isset($_SESSION['msg'])) {
            @$this->data->msgs = $_SESSION['msg'];
            unset($_SESSION['msg']);
        }

        if (isset($_SESSION['error'])) {
            @$this->data->errors = $_SESSION['error'];
            unset($_SESSION['error']);
        }

        $this->container->view->getEnvironment()->addGlobal('data', $this->data);
        return $this->view->render($response, "admin/customization.twig");
    }

    public function process($request, $response)
    {
        if (!$this->user->isAdmin()) {
            return $response->withRedirect('/');
        }

        $expban1_text = $_POST['expban1_txt'];
        $expban1_btn = $_POST['expban1_btn'];
        $expban1_link = $_POST['expban1_link'];
        if (isset($_POST['expban1_enabled']) && $_POST['expban1_enabled'] == 'Yes') {
            $expban1_enabled = 'true';
        } else {
            $expban1_enabled = 'false';
        }

        $expban2_text = $_POST['expban2_txt'];
        $expban2_btn = $_POST['expban2_btn'];
        $expban2_link = $_POST['expban2_link'];
        if (isset($_POST['expban2_enabled']) && $_POST['expban2_enabled'] == 'Yes') {
            $expban2_enabled = 'true';
        } else {
            $expban2_enabled = 'false';
        }

        if (empty($expban1_text) ||
            empty($expban1_btn) ||
            empty($expban1_link) ||
            empty($expban2_text) ||
            empty($expban2_btn) ||
            empty($expban2_link)) {
            $error[] = "Please fill out all the fields.";
        } else if (strlen($expban1_text) >= 255 ||
            strlen($expban1_btn) >= 255 ||
            strlen($expban1_link) >= 255 ||
            strlen($expban2_text) >= 255 ||
            strlen($expban2_btn) >= 255 ||
            strlen($expban2_link) >= 255) {
            $error[] = "You issued a request that was too large. Please shorten the length of your fields.";
        }

        if (!isset($error)) {
            //Process and save (banner 1)
            $checkIfb1Exists = $this->db->prepare("SELECT `name` FROM banners WHERE `name` = 'explr1'");
            $checkIfb1Exists->execute();
            $row = $checkIfb1Exists->fetch(PDO::FETCH_ASSOC);
            if (empty($row['name'])) {
                $saveb1 = $this->db->prepare("INSERT INTO banners (name,btn_content,btn_link,content,enabled) VALUES ('explr1', :btn_content, :btn_link, :content, :enabled)");
                $saveb1->execute(array(
                    ':btn_content' => $expban1_btn,
                    ':btn_link' => $expban1_link,
                    ':content' => $expban1_text,
                    ':btn_content' => $expban1_btn,
                    ':enabled' => $expban1_enabled
                ));
            } else {
                $saveb1 = $this->db->prepare("UPDATE banners SET btn_content=:btn_content,btn_link=:btn_link,content=:content,enabled=:enabled WHERE name='explr1'");
                $saveb1->execute(array(
                    ':btn_content' => $expban1_btn,
                    ':btn_link' => $expban1_link,
                    ':content' => $expban1_text,
                    ':btn_content' => $expban1_btn,
                    ':enabled' => $expban1_enabled
                ));
            }
            //Process and save (banner 2)
            $checkIfb2Exists = $this->db->prepare("SELECT `name` FROM banners WHERE `name` = 'explr2'");
            $checkIfb2Exists->execute();
            $row = $checkIfb2Exists->fetch(PDO::FETCH_ASSOC);
            if (empty($row['name'])) {
                $saveb2 = $this->db->prepare("INSERT INTO banners (name,btn_content,btn_link,content,enabled) VALUES ('explr2', :btn_content, :btn_link, :content, :enabled)");
                $saveb2->execute(array(
                    ':btn_content' => $expban2_btn,
                    ':btn_link' => $expban2_link,
                    ':content' => $expban2_text,
                    ':btn_content' => $expban2_btn,
                    ':enabled' => $expban2_enabled
                ));
            } else {
                $saveb2 = $this->db->prepare("UPDATE banners SET btn_content=:btn_content,btn_link=:btn_link,content=:content,enabled=:enabled WHERE name='explr2'");
                $saveb2->execute(array(
                    ':btn_content' => $expban2_btn,
                    ':btn_link' => $expban2_link,
                    ':content' => $expban2_text,
                    ':btn_content' => $expban2_btn,
                    ':enabled' => $expban2_enabled
                ));
            }
            $msg[] = "Changes Updated.";
            $_SESSION['msg'] = $msg;
            return $response->withRedirect('/admin/customization');
        }
        $_SESSION['error'] = $error;
        return $response->withRedirect('/admin/customization');
    }
}