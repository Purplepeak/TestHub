<?php

class SMailWidget extends CWidget
{
    private $siteTitle;

    private $view = 'main';

    public $scenario;

    public $message;

    public $name;

    public $email;

    public $key;

    public $sitePath;

    public $password;

    public function init()
    {
        parent::init();
        
        $this->sitePath = Yii::app()->request->hostInfo . Yii::app()->request->baseUrl;
        $this->siteTitle = Yii::app()->name;
        
        switch ($this->scenario) {
            case 'confirm':
                $view = 'confirm';
                break;
            case 'restore':
                $view = 'restore';
                break;
            case 'passChange':
                $view = 'passchange';
                break;
            default:
                throw new Exception('Undefined scenario: ' . $this->scenario);
                break;
        }
        
        $this->message = $this->render($view, array(
            'siteTitle' => $this->siteTitle,
            'name' => $this->name,
            'email' => $this->email,
            'key' => $this->key,
            'sitePath' => $this->sitePath,
            'password' => $this->password,
        ), true);
    }

    public function run()
    {
        parent::run();
        $this->render($this->view, array(
            'title' => $this->siteTitle,
            'message' => $this->message
        ));
    }
} 