<?php

class AdminController extends UsersController
{
    public function beforeAction($action)
	{
		$this->userModel = Admin::model();
		$this->index = 'Admin';
		return parent::beforeAction($action);
	}
	
    public function accessRules()
    {
        return array(
            array(
                'allow',
                'actions' => array(
                    'login'
                ),
                'users' => array(
                    '*'
                )
            ),
            
            array(
                'allow',
                'actions' => array(
                    'admin',
                    'delete',
                    'create',
                    'update',
                    'index',
                    'view'
                ),
                'users' => array(
                    'admin'
                )
            ),
            array(
                'deny', // deny all users
                'users' => array(
                    '*'
                )
            )
        );
    }
    
    public function actionAdmin()
    {
        $this->userModel = new Admin('search');
        parent::actionAdmin();
    }
}
