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

    public function actionChangeAvatar()
    {
        $this->userModel = new Teacher('changeAvatar');
        
        parent::actionChangeAvatar();
    }

    public function actionTests()
    {
        //$d = scandir(Yii::getPathOfAlias('avatarFolder') . '/308');
        //SHelper::deleteFolder(Yii::getPathOfAlias('avatarFolder') . '/308/', true);
        //var_dump(Yii::app()->request->hostInfo);
        //var_dump(getimagesize('http://local.testhub.com/test.me/avatars/303/1402484508001.png'));
    }
}
