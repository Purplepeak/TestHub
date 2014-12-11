<?php

class QuestionImagesController extends TestImagesController
{
    protected $model;
    
    protected $defaultModel;
    
    protected $imageModel;

    public function init()
    {
        $this->defaultModel = QuestionImage::model();
    
        parent::init();
    }
    
    public function actionAdmin()
    {
        $this->imageModel = new QuestionImage('search');
        parent::actionAdmin();
    }
}
