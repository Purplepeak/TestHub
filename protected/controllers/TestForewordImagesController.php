<?php

class TestForewordImagesController extends TestImagesController
{
    protected $model;
    
    protected $defaultModel;
    
    protected $imageModel;
    
    public function init()
    {
        $this->defaultModel = TestForewordImage::model();
    
        parent::init();
    }
    
    public function actionAdmin()
    {
        $this->imageModel = new TestForewordImage('search');
        parent::actionAdmin();
    }
}
