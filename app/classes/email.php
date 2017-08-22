<?php

namespace eShop\classes;

class Email{
    private $phpmailer;
    private $container;

    function __construct($container){
        $this->phpmailer = new \PHPMailer();
        $this->container = $container;

        $this->phpmailer->isSMTP();
        $this->phpmailer->Host = $container->get('settings')['email']['host'];
        $this->phpmailer->SMTPAuth = true;
        $this->phpmailer->Username = $container->get('settings')['email']['username'];
        $this->phpmailer->Password = $container->get('settings')['email']['password'];
        $this->phpmailer->SMTPSecure = 'tls';
        $this->phpmailer->Port = $container->get('settings')['email']['port'];
        $this->phpmailer->From = $container->get('settings')['email']['email'];
        $this->phpmailer->FromName = $container->get('settings')['misc']['appName'];
    }

    public function sendWelcomeMessage($emailTo, $name){
            $this->phpmailer->clearAddresses();
            $this->phpmailer->clearAllRecipients();

            $this->phpmailer->addAddress($emailTo, $name);
            $this->phpmailer->WordWrap = 50;
            $this->phpmailer->isHTML(true);

            $this->phpmailer->Subject = 'Welcome!';

            $msg = file_get_contents(__DIR__ . '/../email/templates/registered.html');
            $msg = str_replace('{appName}', $this->container->get('settings')['misc']['appName'], $msg);
            $msg = str_replace('{username}', $name, $msg);
            $msg = str_replace('{time}', date("Y/m/d"), $msg);
            $msg = str_replace('{url}', $this->container->get('settings')['misc']['siteRoot'], $msg);
            $msg = str_replace('{email}', $emailTo, $msg);

            $this->phpmailer->Body = $msg;
            $this->phpmailer->AltBody = "Thank you for registering at " . $this->container->get('settings')['misc']['appName'] . ".";

            $this->phpmailer->send();
    }

    public function sendRecoveryMessage($emailTo, $name, $link){
        $this->phpmailer->clearAddresses();
        $this->phpmailer->clearAllRecipients();

        $this->phpmailer->addAddress($emailTo, $name);
        $this->phpmailer->WordWrap = 50;
        $this->phpmailer->isHTML(true);

        $this->phpmailer->Subject = 'Password Recovery';

        $msg = file_get_contents(__DIR__ . '/../email/templates/recovery.html');
        $msg = str_replace('{appName}', $this->container->get('settings')['misc']['appName'], $msg);
        $msg = str_replace('{username}', $name, $msg);
        $msg = str_replace('{time}', date("Y/m/d"), $msg);
        $msg = str_replace('{url}', $this->container->get('settings')['misc']['siteRoot'], $msg);
        $msg = str_replace('{email}', $emailTo, $msg);
        $msg = str_replace('{link}', $link, $msg);

        $this->phpmailer->Body = $msg;
        $this->phpmailer->AltBody = "You've recently asked to reset the password for this ".$this->container->get('settings')['misc']['siteRoot']." account. Go to '".$link."'' to reset it.";

        $this->phpmailer->send();
    }
}