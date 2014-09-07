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
                    'changeNotification',
                    'resend',
                    'sendNewConfirmation',
                    'avatar'
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

    /**
     * Страница предупреждающая пользователя о необходимости активировать аккаунт.
     *
     * Присутствует функция повторной отправки сообщения.
     */
    public function actionConfirmNotification()
    {
        if (isset(Yii::app()->session) && isset(Yii::app()->session['regModel'])) {
            $userModel = Yii::app()->session['regModel'];
            unset(Yii::app()->session['regModel']);
            $sendMessage = Yii::app()->request->getQuery('sendMessage');
            
            $this->render('notification', array(
                'model' => $userModel
            ));
        } else {
            $this->redirect(array(
                'site/index'
            ));
        }
    }

    public function actionResend($id, $name, $email)
    {
        $userModel = Users::model();
        $userModel->id = $id;
        $userModel->name = $name;
        $userModel->email = $email;
        
        $confirmModel = new AccountInteraction();
        
        $confirmModel->saveAndSend($userModel, 'confirm');
        
        Yii::app()->end();
    }

    public function actionSendNewConfirmation()
    {
        $model = new AccountInteractionForm;
        $model->scenario = 'newConfirm';
        $model->userClass = Users::model();
        
        if (isset($_POST['AccountInteractionForm'])) {
            $model->attributes = $_POST['AccountInteractionForm'];
            
            if ($model->validate()) {
                
                $confirmModel = new AccountInteraction();
                
                $userModel = Users::model();
                
                $user = $userModel->find('email = :email', array(
                    ':email' => $model->email
                ));
                $confirmModel->saveAndSend($user, 'confirm');
                
                Yii::app()->session['regModel'] = $user;
                
                $this->redirect(array(
                    'confirmNotification'
                ));
            }
        }
        
        $this->redirectIfLogged('new_confirmation', array(
            'model' => $model
        ), array(
            'site/index'
        ));
    }

    /**
     * Предупреждает пользователя, что его пароль был изменен.
     */
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

    /**
     * Метод предназначен для автоматической активации аккаунта при
     * переходе по ссылке с корректными параметрами.
     */
    public function actionConfirm()
    {
        $email = Yii::app()->request->getQuery('email');
        $key = Yii::app()->request->getQuery('key');
        
        if ($email && $key) {
            $confirmModel = AccountInteraction::model();
            $scenario = 'confirm';
            $account = $confirmModel->findByEmailKey($email, $key, $scenario);
            
            if ($account === null) {
                $this->render('failed_confirm');
            } else {
                $confirmedAccountId = $account->user_id;
                $account->deleteByPk($account->id);
                
                if (Yii::app()->session && isset(Yii::app()->session['regModel'])) {
                    unset(Yii::app()->session['regModel']);
                }
                
                $confirmedUser = Users::model()->findByPk($confirmedAccountId);
                Users::model()->updateByPk($confirmedUser->id, array(
                    'active' => true
                ));
                
                $identity = UserIdentity::forceLogin($confirmedUser);
                Yii::app()->user->login($identity, Yii::app()->params['rememberMeTime']);
                
                Yii::app()->user->setFlash('success', "Ваш аккаунт был успешно активирован.");
                
                $this->redirect(array(
                    'site/index'
                ));
            }
        } else {
            $this->render('failed_confirm');
        }
    }

    /**
     * Восстановление пароля пользователя.
     * Юзер вбивает в форму свою почту, на
     * которую мы высылаем ссылку на действие changePass().
     */
    public function actionPassRestore()
    {
        $model = new AccountInteractionForm();
        $model->scenario = 'passRestore';
        
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
        
        $this->redirectIfLogged('pass_restore', array(
            'model' => $model
        ), array(
            'site/index'
        ));
    }

    /**
     * Метод меняет старый пароль на новый, который пользователь
     * выбирает самостоятельно.
     * По итогам смены, юзера перенаправляет
     * на страницу users/login с предупреждением, что пароль был изменен.
     */
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
                    
                    $account->sendEmail('passChange');
                    
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
            $this->render('change_error');
        }
    }
    
    public function actionAvatar()
    {
        
    }
}
