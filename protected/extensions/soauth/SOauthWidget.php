<?php

class SOauthWidget extends CWidget
{

    public $component = 'soauth';

    // Доступные сервисы
    public $services = null;

    // Действие, для составленияы url, используемых в виджете 
    public $action;
    
    // login или registration. Влияет на текст социальной кнопки
    public $scenario;

    public $cssFile = true;
    
    // Для использования представления 'bootstrap-buttons', необходимо иметь установленный Bootstrap и Social Buttons for Bootstrap
    private $view = 'bootstrap-buttons';

    public function init()
    {
        parent::init();
        
        $component = Yii::app()->getComponent($this->component);
        
        if (! isset($this->services)) {
            $this->services = $component->getServices();
        }
    }

    public function run()
    {
        parent::run();
        
        $this->registerAssets();
        $this->render($this->view, array(
            'id' => $this->getId(),
            'services' => $this->services,
            'action' => $this->action,
            'scenario' => $this->scenario
        ));
    }

    protected function registerAssets()
    {
        $cs = Yii::app()->clientScript;
        $cs->registerCoreScript('jquery');
        
        $assets_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'assets';
        $url = Yii::app()->assetManager->publish($assets_path, false, - 1, YII_DEBUG);
        if ($this->cssFile) {
            $cs->registerCssFile($url . '/css/auth.css');
        }
    }
}