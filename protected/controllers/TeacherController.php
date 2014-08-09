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
        $model=new Teacher('search');
        $model->unsetAttributes();
        if(isset($_GET['Teacher'])) {
            $model->attributes=$_GET['Teacher'];
            $model->fullname = isset($_GET['Teacher']['fullname']) ? $_GET['Teacher']['fullname'] : '';
        }
        
        $this->render('list', array('model' => $model));
    }

    public function actionTests()
    {
        ini_set('xdebug.var_display_max_depth', 5);
        ini_set('xdebug.var_display_max_children', 256);
        ini_set('xdebug.var_display_max_data', 1024);
        
        $key = 'лукашенко';
        $criteria=new CDbCriteria;
        $criteria->addSearchCondition('surname', $key);
        $criteria->addSearchCondition('name', $key, true, 'OR');
        
        $model = $this->userModel;
        $u = $model->findAll($criteria);
        
        $y = Group::model()->with(array('teacher'=>array('condition'=>'active=3')))->findByPk('1');
        
        
        var_dump($y);
        $this->render('//accountInteraction/change_error');
    }
}
