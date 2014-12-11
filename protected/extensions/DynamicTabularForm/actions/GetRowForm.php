<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of GetRowForm
 *
 * @author Web Developer
 */
class GetRowForm extends CAction{
    //put your code here
    public $view;
    public $modelClass;
    public $processOutput = true;
    
    public function run() {
        $controller = $this->getController();
        
        $model = new $this->modelClass;
        $model->scenario = $_GET['scenario'];
        
        
        $form = new DynamicTabularForm();
        
        $controller->renderPartial($this->view,array('key'=>$_GET['key'], 'questionNumber' => $_GET['questionNumber'], 'model'=>$model,'form'=>$form),false, $this->processOutput);
    }
}

?>
