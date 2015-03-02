<?php

class SMailer extends CApplicationComponent
{

    private $server = 'smtp.gmail.com';

    private $port = 587;

    private $encryption = 'tls';

    private $contentType = 'text/html';

    private $charset = 'utf-8';

    private $scenario;

    private $hostEmail;

    private $hostPass;

    private $username;

    private $email;

    private $key;

    private $title;

    private $newPassword;

    public $templatePath;

    private $imagePath = 'images/';

    private $message;

    private $sitePath;

    private $siteTitle;

    private $mainTemplate = 'main.php';

    private $password;
    
    private $mailSubject;

    const EMAIL_CONFIRM = 1;

    const EMAIL_RESTORE = 2;

    const PASS_CHANGE = 3;

    public function init()
    {
        parent::init();
        
        $this->sitePath = Yii::app()->request->hostInfo . Yii::app()->request->baseUrl;
        $this->siteTitle = Yii::app()->name;
        
        $this->hostEmail = Yii::app()->params['siteEmail']['email'];
        $this->hostPass = Yii::app()->params['siteEmail']['password'];
        $this->templatePath = dirname(__FILE__) . '/views';
    }

    private function formatEmail()
    {
        switch ($this->scenario) {
            case self::EMAIL_CONFIRM:
                $view = 'confirm.php';
                $this->mailSubject = "Регистрация на {$this->siteTitle}";
                break;
            case self::EMAIL_RESTORE:
                $view = 'restore.php';
                $this->mailSubject = "Восстановление пароля на {$this->siteTitle}";
                break;
            case self::PASS_CHANGE:
                $view = 'passchange.php';
                $this->mailSubject = "Изменение пароля на {$this->siteTitle}";
                break;
            default:
                throw new Exception('Undefined scenario: ' . $this->scenario);
                break;
        }
        
        $this->message = Yii::app()->controller->renderFile($this->templatePath . '/' . $view, array(
            'siteTitle' => $this->siteTitle,
            'username' => $this->username,
            'email' => $this->email,
            'key' => $this->key,
            'sitePath' => $this->sitePath,
            'password' => $this->newPassword
        ), true);
        
        return Yii::app()->controller->renderFile($this->templatePath . '/' . $this->mainTemplate, array(
            'title' => $this->siteTitle,
            'message' => $this->message
        ), true);
    }

    public function send($scenario, array $userData)
    {
        $this->scenario = $scenario;
        $this->username = $userData['username'];
        $this->email = $userData['email'];
        $this->key = $userData['key'];
        $this->newPassword = $userData['password'];
        
        $bodyHtml = $this->formatEmail();
        
        $transport = Swift_SmtpTransport::newInstance($this->server, $this->port, $this->encryption);
        $transport->setUsername(Yii::app()->params['siteEmail']['email']);
        $transport->setPassword(Yii::app()->params['siteEmail']['password']);
        
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $this->message = $message;
        
        $bodyHtml = preg_replace_callback('/' . preg_quote($this->imagePath, '/') . '((.+)\.(jpg|png|gif))/i', 'self::AddImage', $bodyHtml);
        
        $message->setSubject($this->mailSubject);
        $message->setFrom(array(
            Yii::app()->params['siteEmail']['email'] => 'TestHub'
        ));
        $message->setTo(array(
            $this->email => $this->username
        ));
        
        $message->setBody($bodyHtml);
        $message->setContentType($this->contentType);
        $message->setCharset($this->charset);
        
        $result = $mailer->send($message);
        
        return $result;
    }

    private function addImage($matches)
    {
        $path = $this->templatePath . "/" . $matches[0];
        return $this->message->embed(Swift_Image::fromPath($path));
    }
}