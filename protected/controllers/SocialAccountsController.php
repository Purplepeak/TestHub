<?php

class SocialAccountsController extends Controller
{

    public $layout = '//layouts/column2';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete' // we only allow deletion via POST request
                );
    }

    public function accessRules()
    {
        return array();
    }

    public function loadModel($id)
    {
        $model = SocialAccounts::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'social-accounts-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }
}
