<?php

class UsersController extends Controller
{

    public $userModel;

    public $index; // TODO: НЕ НУЖЕН ИСПРАВИТЬ

    protected $model;

    protected $defaultModel;

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
        if ($this->userData) {
            $userType = $this->userData['type'];
        } else {
            $userType = '';
        }
        
        return array(
            array(
                'allow',
                'actions' => array(
                    'view',
                    'list',
                    'registration',
                    'testss', // DELETE AFTER DEV
                ),
                'users' => array(
                    '*'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'changeAvatar',
                    'profile'
                ),
                'users' => array(
                    '@'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'startTest'
                ),
                'roles' => array(
                    'startTestUser'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'admin',
                    'delete',
                    'update'
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
        $this->userModel->unsetAttributes();
        if (isset($_GET[$this->index])) {
            $this->userModel->attributes = $_GET[$this->index];
            if (CHtml::modelName($this->userModel) === 'Teacher') {
                $this->userModel->group_id = isset($_GET['Post']['group_id']) ? $_GET['Post']['group_id'] : '';
            }
        }
        
        $this->render('admin', array(
            'model' => $this->userModel
        ));
    }

    public function actionRegistration()
    {
        $model = $this->userModel;
        
        $this->performAjaxValidation($model);
        
        if (isset($_POST[$this->index])) {
            $post = $_POST[$this->index];
            
            $model->attributes = $post;
            
            if ($model->validate()) {
                
                $model->save(false);
                
                /**
                 * Для подтверждения аккаунта отправляем пользователю активационное письмо и
                 * заносим ключ активации и e-mail в отдельную таблицу базы данных
                 */
                
                $confirmModel = new AccountInteraction();
                
                $confirmModel->saveAndSend($model, 'confirm');
                
                if (Yii::app()->session && isset(Yii::app()->session['captchaCash'])) {
                    unset(Yii::app()->session['captchaCash']);
                }
                
                if (Yii::app()->session) {
                    Yii::app()->session['regModel'] = $model;
                }
                
                // Удаление не активироанных аккаунтов
                
                Users::model()->deleteNotActivated();
                
                $this->redirect(array(
                    'accountInteraction/confirmNotification'
                ));
            }
        }
        
        $this->redirectIfLogged('//users/registration', array(
            'model' => $model
        ), array(
            'site/index'
        ));
    }

    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();
        
        if (! Yii::app()->request->isAjaxRequest) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
                'admin'
            ));
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        
        if (isset($_POST[$this->index])) {
            $model->attributes = $_POST[$this->index];
            if ($model->save())
                $this->redirect(array(
                    'view',
                    'id' => $model->id
                ));
        }
        
        $this->render('update', array(
            'model' => $model
        ));
    }

    public function actionView($id)
    {
        $model = $this->loadModel($id);
        
        $this->render('//users/view', array(
            'model' => $model,
            'externalProfile' => true
        ));
    }
    
    public function actionProfile()
    {
        $model = Users::model()->findByPk(Yii::app()->user->__userData['id']);
        
        $this->render('//users/view', array(
            'model' => $model
        ));
    }

    public function actionChangeAvatar()
    {
        $model = $this->userModel;
        $user = Users::model()->findByPk($this->userData['id']);
        $model->id = $user->id;
        
        if (isset($_POST[$this->index])) {
            $post = $_POST[$this->index];
            
            $model->avatarX = $post['avatarX'];
            $model->avatarY = $post['avatarY'];
            $model->avatarWidth = $post['avatarWidth'];
            $model->avatarHeight = $post['avatarHeight'];
            
            $model->newAvatar = CUploadedFile::getInstance($model, 'newAvatar');
            
            if ($model->validate()) {
                $model->uploadAvatar($user);
            }
        }
        
        $this->render('//users/change_avatar', array(
            'model' => $model,
            'user' => $user
        ));
    }
}
