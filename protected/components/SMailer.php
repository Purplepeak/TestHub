<?php

class SMailer extends CApplicationComponent
{

    private $siteTitle = 'TestHub';

    private $hostEmail;

    private $hostPass;

    public $root;

    private $userName;

    private $email;

    private $key;

    private $title;

    public function init($userName, $email, $key, $title)
    {
        $this->userName = $userName;
        $this->email = $email;
        $this->key = $key;
        $this->root = $_SERVER['DOCUMENT_ROOT'] . Yii::app()->request->baseUrl . '/templates';
        $this->hostEmail = Yii::app()->params['siteEmail']['email'];
        $this->hostPass = Yii::app()->params['siteEmail']['password'];
    }

    private function formatEmail($format)
    {
        $template = file_get_contents($this->root . '/signup_template.' . $format);
        
        $patterns = array(
            '/{EMAIL}/',
            '/{KEY}/',
            '/{SITEPATH}/',
            '/{NAME}/',
            '/{BASE_URL}/'
        );
        $replacements = array(
            $this->email,
            $this->key,
            Yii::app()->request->hostInfo,
            $this->user_name,
            Yii::app()->request->baseUrl
        );
        
        $template = preg_replace($patterns, $replacements, $template);
        
        return $template;
    }

    public static function sendEmail()
    {
        $bodyHtml = $this->formatEmail('html');
        $bodyTxt = $this->formatEmail('txt');
        
        // setup the mailer
        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
        $transport->setUsername($this->hostEmail);
        $transport->setPassword($this->hostPass);
        
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $message->setSubject($this->title);
        $message->setFrom(array(
            Yii::app()->params['siteEmail']['email'] => $this->siteTitle
        ));
        $message->setTo(array(
            $this->email => $this->user_name
        ));
        
        $message->setBody($bodyTxt);
        $message->addPart($bodyHtml, 'text/html');
        
        $result = $mailer->send($message);
        
        return $result;
    }
}