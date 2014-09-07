<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{

    public $userModel;

    public $layout = '//layouts/column1';
    
    public $menu = array();

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && ($_POST['ajax'] === 'register-form' || $_POST['ajax'] === 'change-pass-form' || $_POST['ajax'] === 'group-form' || $_POST['ajax'] === 'avatar-form')) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
    
    
    /**
     * Рендерит представление, если юзер не залогинен
     */
    public function redirectIfLogged($renderView, $data, $redirectAction)
    {
        if(Yii::app()->user->isGuest) {
            $this->render($renderView, $data);
        } else {
            $this->redirect($redirectAction);
        }
    }
}