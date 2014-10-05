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
                    'view',
                    'PopulateUsers'
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
    
    public function actionPopulateUsers()
    {
        $attributes = array(
            'name'=>'Тестер',
            'surname' => 'Тестеров',
            'email'=>'tester@example.com',
            'gender' => 'male',
            'active' => 0,
            'groups' => '302 301',
            'type' => 'teacher',
            'passwordText' => '$2a$13$hcBaAd16nNnSfyQnvquezeNrMU4Hop./4sOvDyGPW9/BOx0AFZ5F.'
        );
        
        $student = new Teacher('register');
        $student->attributes = $attributes;
        
        $student->isGroupExist();
        
        if($student->save(false)) {
            echo 'basas';
        } else {
            var_dump($student->errors);
        }
        

        //var_dump();
    }
}
