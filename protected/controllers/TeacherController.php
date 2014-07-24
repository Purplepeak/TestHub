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

    public function actionTests()
    {
        $model1 = $this->userModel;
        
        var_dump($model1->withRelated->kkk());
        
        $this->render('//users/_register_form', array(
            'model' => $this->userModel
        ));
    }
}
