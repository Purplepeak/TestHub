<?php

class UsersController extends Controller
{

    public $userModel;

    public $index;

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
                'allow',
                'actions' => array(
                    'index',
                    'view',
                    'registration',
                    'login',
                    'tests',
                    'list',
                    'socialregistration',
                    'profile',
                    'changeAvatar',
                    'saveAvatar'
                ),
                'users' => array(
                    '*'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'logout'
                ),
                'users' => array(
                    '@'
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

    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider($this->index, array(
            'criteria' => array(
                'condition' => 'active=true'
            )
        ));
        $this->render('index', array(
            'dataProvider' => $dataProvider
        ));
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

    public function actionLogin()
    {
        $service = Yii::app()->request->getQuery('service');
        
        if (isset($service)) {
            
            /**
             * Так как провайдеры отдают значение гендерной принадлежности пользователя каждый по разному,
             * необходимо передать расширению значения, которые допустимы для нашей базы.
             * По умолчанию $genderArray = array('female' => 1,'male' => 2,'undefined' => 3);
             */
            
            $genderArray = array(
                'female' => 'female',
                'male' => 'male',
                'undefined' => 'undefined'
            );
            
            $serviceClass = Yii::app()->soauth->getClass($service, $genderArray);
            
            if ($serviceClass->authenticate()) {
                $socialAttributes = $serviceClass->socialAttributes();
                
                $socialModel = SocialAccounts::model();
                $socialModel->attributes = $socialAttributes;
                
                $oauthModel = $socialModel->validateSocialModel();
                
                if (! empty($oauthModel)) {
                    Yii::app()->session['oauth_model'] = $oauthModel;
                    $this->redirect(array(
                        'socialRegistration'
                    ));
                }
                
                $this->redirect(array(
                    'site/index'
                ));
            }
        }
        
        $model = new LoginForm();
        $model->scenario = 'login';
        $model->userClass = $this->userModel;
        
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login())
                $this->redirect(array(
                    'site/index'
                ));
        }
        
        $this->redirectIfLogged('//users/login', array(
            'model' => $model
        ), array(
            'site/index'
        ));
    }

    public function actionDelete()
    {
        $this->loadModel()->delete();
        
        if (! Yii::app()->request->isAjaxRequest) {
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
                'admin'
            ));
        }
    }

    public function actionUpdate()
    {
        $model = $this->loadModel();
        
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

    public function actionView()
    {
        $this->render('//users/view', array(
            'model' => $this->loadModel()
        ));
    }

    public function loadModel()
    {
        if (isset($_GET['id'])) {
            
            $model = $this->userModel->findByPk($_GET['id']);
        }
        
        if ($model === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        
        return $model;
    }

    public function actionSocialRegistration()
    {
        $userModel = $this->userModel;
        $userModel->scenario = 'oauth';
        $userModel->isNewRecord = true;
        $oauthModel = Yii::app()->session['oauth_model'];
        
        $userAttributes = json_decode($oauthModel->info);
        $userModel->attributes = array(
            'name' => $userAttributes->name,
            'surname' => $userAttributes->surname,
            'gender' => $userAttributes->gender,
            'avatar' => $userAttributes->photo,
            'active' => true
        );
        
        $this->performAjaxValidation($userModel);
        
        if (isset($_POST[$this->index])) {
            $post = $_POST[$this->index];
            $userModel->attributes = $post;
            
            if ($userModel->validate()) {
                
                $userModel->save(false);
                Users::model()->deleteNotActivated();
                
                $oauthModel->user_id = $userModel->id;
                $oauthModel->save(false);
                
                $identity = UserIdentity::forceLogin($userModel);
                Yii::app()->user->login($identity);
                unset(Yii::app()->session['oauth_model']);
                
                $this->redirect(array(
                    'site/index'
                ));
            }
        }
        
        $this->redirectIfLogged('//users/registration', array(
            'model' => $userModel
        ), array(
            'site/index'
        ));
    }

    public function actionProfile()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(array(
                'login'
            ));
        } else {
            $model = Users::model()->findByPk(Yii::app()->user->__userData['id']);
            
            $this->render('//users/view', array(
                'model' => $model
            ));
        }
    }

    public function actionChangeAvatar()
    {
        if (Yii::app()->user->isGuest) {
            $this->redirect(array(
                'login'
            ));
        }
        
        $model = $this->userModel;
        $user = Users::model()->findByPk(Yii::app()->user->__userData['id']);
        $model->id = $user->id;
        
        // var_dump($model, get_class($model));
        
        if (isset($_POST[$this->index])) {
            $post = $_POST[$this->index];
            
            $model->avatarX = $post['avatarX'];
            $model->avatarY = $post['avatarY'];
            $model->avatarWidth = $post['avatarWidth'];
            $model->avatarHeight = $post['avatarHeight'];
            
            $model->newAvatar = CUploadedFile::getInstance($model, 'newAvatar');
            
            if ($model->validate()) {
                //$model->uploadAvatar($user);
              var_dump($post, $model->newAvatar, $model->avatarX, $model->avatarY, $model->avatarWidth, $model->avatarHeight, $model->avatarWidth, $model->avatarHeight);
            }
        }
        
        $this->render('//users/change_avatar', array(
            'model' => $model,
            'user' => $user
        ));
    }

    public function actionSaveAvatar()
    {
        if (isset($_POST[$this->index])) {
            var_dump($_POST[$this->index]);
        }
    }
}
