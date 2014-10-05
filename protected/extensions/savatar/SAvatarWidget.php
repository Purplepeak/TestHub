<?php

class SAvatarWidget extends CWidget
{

    private $view = 'avatar-upload-from';

    public $action;

    public $model;

    public $avatarAtt;

    public $modelAttributes;

    public $previewMaxWidth;

    public $previewMaxHeight;

    public $maxImageWidth;
    
    public $maxImageHeight;
    
    public $minImageWidth;
    
    public $minImageHeight;
    
    public $maxImageRatio;
    
    public $minImageRatio;
    
    public $maxImageSize = false;
    
    public $imgAreaSelectConfig;

    private $uploadImageConfig;

    public function init()
    {
        parent::init();
        
        $this->uploadImageConfig = array(
            'maxImageWidth' => $this->maxImageWidth,
            'maxImageHeight' => $this->maxImageHeight,
            'minImageWidth' => $this->minImageWidth,
            'minImageHeight' => $this->minImageHeight,
            'maxImageRatio' => $this->maxImageRatio,
            'minImageRatio' => $this->minImageRatio,
            'maxImageSize' => $this->maxImageSize
        );
        
        $this->imgAreaSelectConfig = array(
            'aspectRatio' => '1:1',
            'maxHeight' => '400',
            'maxWidth' => '400',
            'minHeight' => '190',
            'minWidth' => '190',
            'x1' => '0',
            'y1' => '0',
            'x2' => '190',
            'y2' => '190',
        );
    }

    public function run()
    {
        parent::run();
        $this->registerAssets();
        
        $this->render($this->view, array(
            'model' => $this->model,
            'action' => $this->action,
            'modelAttributes' => $this->modelAttributes,
            'previewMaxWidth' => $this->previewMaxWidth,
            'previewMaxHeight' => $this->previewMaxHeight,
            'uploadImageConfig' => $this->uploadImageConfig,
            'imgAreaSelectConfig' => $this->imgAreaSelectConfig
        ));
    }

    protected function registerAssets()
    {
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        
        $assets_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $url = Yii::app()->assetManager->publish($assets_path, false, - 1, YII_DEBUG);
        
        $cs->registerCssFile($url . '/css/savatar-style.css');
        $cs->registerCssFile($url . '/imgareaselect/css/imgareaselect-default.css');
        $cs->registerScriptFile($url . '/img-preview.js');
        $cs->registerScriptFile($url . '/imgareaselect/scripts/jquery.imgareaselect.pack.js');
    }
} 