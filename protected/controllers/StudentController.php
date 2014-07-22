<?php

class StudentController extends UsersController
{

    public function beforeAction($action)
    {
        $this->userModel = new Student();
        $this->index = 'Student';
        return parent::beforeAction($action);
    }

    public function actionRegistration()
    {
        $this->userModel = new Student('register');
        parent::actionRegistration();
    }

    public function actionAdmin()
    {
        $this->userModel = new Student('search');
        parent::actionAdmin();
    }
}
