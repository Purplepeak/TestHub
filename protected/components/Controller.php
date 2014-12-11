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
    
    public $userData;
    
    protected $model;
    
    protected $defaultModel;
    
    public function init()
    {
        if(!Yii::app()->user->isGuest && !empty(Yii::app()->user->__userData)) {
            $this->userData = Yii::app()->user->__userData;
        }
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && ($_POST['ajax'] === 'register-form' || $_POST['ajax'] === 'change-pass-form' || $_POST['ajax'] === 'group-form' || $_POST['ajax'] === 'avatar-form' || $_POST['ajax']==='test-form')) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Рендерит представление, если юзер не залогинен
     */
    public function redirectIfLogged($renderView, $data, $redirectAction)
    {
        if (Yii::app()->user->isGuest) {
            $this->render($renderView, $data);
        } else {
            $this->redirect($redirectAction);
        }
    }
    
    public function loadModel($id)
    {
        if ($this->model === null) {
            $this->model = $this->defaultModel->findByPk($id);
        }
    
        if ($this->model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
    
        return $this->model;
    }
    
    public function getModelByActionId($actionId)
    {
        if ($this->action->id === $actionId && ! empty($this->actionParams) && $this->actionParams['id']) {
            return $this->loadModel($this->actionParams['id']);
        }
    }
}