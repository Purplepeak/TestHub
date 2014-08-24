<?php

class SMailer extends CComponent
{
    private $server = 'smtp.gmail.com';
    
    private $port = 587;
    
    private $encryption = 'tls';
    
    private $contentType = 'text/html';
    
    private $charset = 'utf-8';
    
    private $siteTitle = 'TestHub';
    
    private $scenario;

    private $hostEmail;

    private $hostPass;

    public $root;

    private $userName;

    private $email;

    private $key;

    private $title;
    
    private $newPassword;
    
    public $templatePath;
    
    public $imagePath = 'images/';
    
    private $message;

    public function init($scenario, $userName, $email, $key, $password)
    {
        $this->userName = $userName;
        $this->email = $email;
        $this->key = $key;
        $this->newPassword = $password;
        $this->scenario = $scenario;
        
        $this->hostEmail = Yii::app()->params['siteEmail']['email'];
        $this->hostPass = Yii::app()->params['siteEmail']['password'];
        $this->templatePath = dirname(__FILE__). '/views';
    }
    
    public function formatEmail()
    {
        $widget = new CWidget;
        
        return $widget->widget('SMailWidget', array(
            'name' => $this->userName,
            'scenario' => $this->scenario,
            'email' => $this->email,
            'key' => $this->key,
            'password' => $this->newPassword,
        ), true);
    }
    
    public function sendEmail()
    {
        $bodyHtml = $this->formatEmail();
    
        $transport = Swift_SmtpTransport::newInstance($this->server, $this->port, $this->encryption);
        $transport->setUsername(Yii::app()->params['siteEmail']['email']);
        $transport->setPassword(Yii::app()->params['siteEmail']['password']);
    
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $this->message = $message;
        
        $bodyHtml = preg_replace_callback('/'.preg_quote($this->imagePath, '/').'((.+)\.(jpg|png|gif))/i', 'self::AddImage', $bodyHtml);
        
        $message->setSubject($this->siteTitle);
        $message->setFrom(array(
            Yii::app()->params['siteEmail']['email'] => 'TestHub'
        ));
        $message->setTo(array(
            $this->email => $this->userName
        ));
    
        $message->setBody($bodyHtml);
        $message->setContentType($this->contentType);
        $message->setCharset($this->charset);
        
    
        $result = $mailer->send($message);
    
        return $result;
    }
    
    private function addImage($matches) {
        $path = $this->templatePath."/".$matches[0];
        return $this->message->embed(Swift_Image::fromPath($path));
    }
}