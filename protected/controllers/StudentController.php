<?php

class StudentController extends UsersController
{
    protected $model;
    
    protected $defaultModel;
    
    public function init()
    {
        $this->defaultModel = Student::model();
    
        parent::init();
    }

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
    
    public function actionList($id)
    {
        $model=new Student('search');
        $model->searchGroup = $id;
        $model->unsetAttributes();
        if(isset($_GET['Student'])) {
            $model->attributes=$_GET['Student'];
            $model->fullname = isset($_GET['Student']['fullname']) ? $_GET['Student']['fullname'] : '';
        }
        
        $this->render('list', array('model' => $model));
    }
    
    public function actionChangeAvatar()
    {
        $this->userModel = new Student('changeAvatar');
        
        parent::actionChangeAvatar();
    }

    public function actionAdmin()
    {
        $this->userModel = new Student('search');
        parent::actionAdmin();
    }
    
    public function actionStartTest()
    {
        var_dump('Hello');
    }
}
