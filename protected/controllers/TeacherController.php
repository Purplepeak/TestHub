<?php

class TeacherController extends UsersController
{

    public function beforeAction($action)
    {
        $this->userModel = new Teacher();
        $this->index = 'Teacher';
        return parent::beforeAction($action);
    }

    public function actionRegistration()
    {
        $this->userModel = new Teacher('register');
        parent::actionRegistration();
    }

    public function actionAdmin()
    {
        $this->userModel = new Teacher('search');
        parent::actionAdmin();
    }

    public function actionList()
    {
        $model = new Teacher('search');
        $model->unsetAttributes();
        if (isset($_GET['Teacher'])) {
            $model->attributes = $_GET['Teacher'];
            $model->fullname = isset($_GET['Teacher']['fullname']) ? $_GET['Teacher']['fullname'] : '';
            $model->groupNumber = isset($_GET['Teacher']['groupNumber']) ? $_GET['Teacher']['groupNumber'] : '';
        }
        
        $this->render('list', array(
            'model' => $model
        ));
    }

    public function actionTests()
    {
        $a = new SMailer;
        
        $a->init('confirm', 'Татьяна', 'shrpeak@yandex.ru', 'asfasfas423sdhsdh', null);
        $a->sendEmail();
        //$a->renderWidget();
        
        var_dump($a->formatEmail());
        // $this->render('//accountInteraction/change_error');
    }
}
