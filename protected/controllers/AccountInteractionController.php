<?php

class AccountInteractionController extends Controller
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
        return array(
            array(
                'allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array(
                    'confirm',
                    'confirmNotification',
                    'passRestore',
                    'changePass',
                    'restoreNotification',
                    'changeNotification'
                ),
                'users' => array(
                    '*'
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

    public function actionConfirmNotification()
    {
        if (isset(Yii::app()->session) && isset(Yii::app()->session['regModel'])) {
            $userModel = Yii::app()->session['regModel'];
            unset(Yii::app()->session['regModel']);
            $sendMessage = Yii::app()->request->getQuery('sendMessage');
            
            if ($sendMessage == 1) {
                $confirmModel = new AccountInteraction();
                
                $confirmModel->confirmation($userModel);
            }
            
            $this->render('notification', array(
                'model' => $userModel
            ));
        } else {
            $this->redirect(array(
                'site/index'
            ));
        }
    }

    public function actionRestoreNotification()
    {
        if (isset(Yii::app()->session) && isset(Yii::app()->session['restoreModel'])) {
            $userModel = Yii::app()->session['restoreModel'];
            unset(Yii::app()->session['restoreModel']);
            $this->render('restore_notification', array(
                'model' => $userModel
            ));
        } else {
            $this->redirect(array(
                'passRestore'
            ));
        }
    }

    public function actionChangeNotification()
    {
        $this->render('change_notification');
    }

    public function actionConfirm()
    {
        $email = Yii::app()->request->getQuery('email');
        $key = Yii::app()->request->getQuery('key');
        
        if ($email && $key) {
            $confirmModel = AccountInteraction::model();
            $scenario = 'confirm';
            $account = $confirmModel->findByEmailKey($email, $key, $scenario);
            
            if ($account === null) {
                $this->redirect(array(
                    'site/index'
                ));
            } else {
                $confirmedAccountId = $account->user_id;
                $account->deleteByPk($account->id);
                
                if (Yii::app()->session && isset(Yii::app()->session['regModel'])) {
                    unset(Yii::app()->session['regModel']);
                }
                
                $confirmedUser = Users::model()->findByPk($confirmedAccountId);
                Users::model()->updateByPk($confirmedUser->id, array(
                    'active' => 1
                ));
                
                $identity = UserIdentity::forceLogin($confirmedUser);
                Yii::app()->user->login($identity);
                
                $this->redirect(array(
                    'site/index'
                ));
            }
        } else {
            $this->redirect(array(
                'site/index'
            ));
        }
    }

    public function actionPassRestore()
    {
        $model = new AccountInteractionForm();
        $model->scenario = 'passRestore';
        $userType = Yii::app()->request->getQuery('user');
        
        $model->userClass = Users::model();
        
        $tempModel = new AccountInteraction();
        
        $this->performAjaxValidation($model);
        
        if (isset($_POST['AccountInteractionForm'])) {
            $model->attributes = $_POST['AccountInteractionForm'];
            if ($model->validate()) {
                $tempModel->saveAndSend($model->user, 'restore');
                Yii::app()->session['restoreModel'] = $model->user;
                
                $this->redirect('restoreNotification');
            }
        }
        
        $this->render('pass_restore', array(
            'model' => $model
        ));
    }

    public function actionChangePass()
    {
        $email = Yii::app()->request->getQuery('email');
        $key = Yii::app()->request->getQuery('key');
        
        if ($email && $key) {
            $changeModel = new AccountInteraction();
            $scenario = 'restore';
            $account = $changeModel->findByEmailKey($email, $key, $scenario);
            
            if ($account === null) {
                $this->render('change_error');
            } else {
                $userId = $account->user_id;
                $model = Users::model();
                $model->scenario = 'changePass';
                $this->performAjaxValidation($model);
                if (isset($_POST['Users'])) {
                    $newPassword = $model->hashPassword($_POST['Users']['passwordText']);
                    
                    $user = $model->findByPk($account->user_id);
                    $model->updateByPk($account->user_id, array(
                        'password' => $newPassword
                    ));
                    
                    $account->newPassword = $_POST['Users']['passwordText'];
                    $account->sendEmail('Смена пароля', '/passchange_template.');
                    $changeModel->deleteByPk($account->id);
                    
                    $this->redirect(array(
                        $user->type . '/login',
                        'newPassword' => 1
                    ));
                }
                
                $this->render('change_pass', array(
                    'model' => $model
                ));
            }
        } else {
            $this->redirect(array(
                'passRestore'
            ));
        }
    }
}
