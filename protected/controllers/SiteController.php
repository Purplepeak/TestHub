<?php

class SiteController extends Controller
{

    /**
     * Declares class-based actions.
     */
    public $layout = '//layouts/column1';

    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete' // we only allow deletion via POST request
                );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     *
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array(
                'allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array(
                    'index',
                    'getServerTime',
                    'error',
                    'socialRegistration',
                    'login',
                    'contact',
                    'userType',
                    'install',
                    'getAvatar'
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
                'deny', // deny all users
                'users' => array(
                    '*'
                )
            )
        );
    }

    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction'
            )
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        $this->render('index');
    }

    public function actionGetServerTime()
    {
        $now = new DateTime();
        echo $now->format("M j, Y H:i:s O");
    }

    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $defaultMessage = 'Сервис временно испытывает проблемы. Приносим извинения за временные неудобства.';
                
                $layout = 'error';
                
                switch ($error['code']) {
                    case '404':
                        $layout = 'error404';
                        break;
                    case '500':
                        $error['message'] = $defaultMessage;
                        break;
                    case '400':
                        $error['message'] = 'Запрос введеный вами некорректен. Пожалуйста, убедитесь в его правильности.';
                        break;
                    case '403':
                        $error['message'] = 'У вас нет прав доступа на эту страницу.';
                        break;
                    default:
                        $error['message'] = 'Возникла непредвиденная ошибка. Приносим извинения за временные неудобства.';
                        break;
                }
                
                switch ($error['type']) {
                    case 'SOauthException':
                        $error['message'] = 'Сервис временно испытывает проблемы с входом через социальные сети, повторите попытку позже. Приносим извинения за временные неудобства.';
                        break;
                    case 'Swift_SwiftException':
                        $error['message'] = $defaultMessage;
                        break;
                    case 'SAvatarCropperException':
                        $error['message'] = $defaultMessage;
                        break;
                    case 'RegExrException':
                        $error['message'] = 'Возникла ошибка не позволяющая обработать ваш запрос. Пожалуйста, свяжитесь с администрацией воспользовавшись ссылкой ниже.';
                        break;
                }
                
                $this->render($layout, $error);
            }
        }
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
                    
                    if ($userType = Yii::app()->request->getQuery('type')) {
                        $this->redirect(array(
                            'socialRegistration',
                            'userType' => $userType
                        ));
                    } else {
                        $this->redirect(array(
                            'userType'
                        ));
                    }
                }
                
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        
        $model = new LoginForm();
        $model->scenario = 'login';
        $model->userClass = new Users();
        
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            if ($model->validate() && $model->login())
                $this->redirect(Yii::app()->user->returnUrl);
        }
        
        $this->redirectIfLogged('login', array(
            'model' => $model
        ), array(
            'index'
        ));
    }

    public function actionUserType()
    {
        $userType = Yii::app()->request->getPost('userType');
        
        if ($userType == 'student' || $userType == 'teacher') {
            $this->redirect(array(
                'socialRegistration',
                'userType' => $userType
            ));
        }
        
        $this->redirectIfLogged('user_type', array(), array(
            'site/index'
        ));
    }

    public function actionSocialRegistration($userType)
    {
        if (! $oauthModel = Yii::app()->session['oauth_model']) {
            $this->redirect(array(
                'index'
            ));
        }
        
        switch($userType) {
        	case 'student':
        	    $userModel = new Student();
        	    break;
        	case 'teacher':
        	    $userModel = new Teacher();
        	    break;
        	default:
        	    throw new CHttpException(404);
        	    break;
        }
        
        $userModel->scenario = 'oauth';
        $userModel->isNewRecord = true;
        
        $userAttributes = json_decode($oauthModel->info);
        $userModel->attributes = array(
            'name' => $userAttributes->name,
            'surname' => $userAttributes->surname,
            'gender' => $userAttributes->gender,
            'active' => true
        );
        
        $this->performAjaxValidation($userModel);
        
        if (isset($_POST[get_class($userModel)])) {
            $post = $_POST[get_class($userModel)];
            $userModel->attributes = $post;
            
            if ($userModel->validate()) {
                
                $userModel->save(false);
                Users::model()->deleteNotActivated();
                
                $oauthModel->user_id = $userModel->id;
                $oauthModel->save(false);
                
                $identity = UserIdentity::forceLogin($userModel);
                Yii::app()->user->login($identity, Yii::app()->params['rememberMeTime']);
                unset(Yii::app()->session['oauth_model']);
                unset(Yii::app()->session['userType']);
                
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

    public function actionContact()
    {
        $model = new ContactForm();
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" . "Reply-To: {$model->email}\r\n" . "MIME-Version: 1.0\r\n" . "Content-Type: text/plain; charset=UTF-8";
                
                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact', 'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array(
            'model' => $model
        ));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    public function actionGetAvatar($id, $res, $method, $img)
    {
        $user = Users::model()->findByPk($id);
        $avatarDir = sprintf("%s" . "/" . "%d" . "/", Yii::getPathOfAlias('avatarDir'), $user->id);
        $croppedAvatarDir = sprintf("%s" . "%d" . "%s" . "%d" . "/" . "%s" . "/", $avatarDir, $user->avatarWidth, 'x', $user->avatarHeight, $method);
        
        $resReg = '{(\d+)x(\d+)}';
        
        if (! preg_match($resReg, $res, $thumbRes)) {
            Yii::log("Regular expression {$resReg} for the thumbnail size does not match the link.", CLogger::LEVEL_ERROR, 'application.extensions.savatar');
            throw new SAvatarCropperException("Regular expression {$resReg} for the thumbnail size does not match the link.");
        }
        
        $thumbWidth = $thumbRes[1];
        $thumbHeight = $thumbRes[2];
        
        $cropper = new SAvatarCropper($avatarDir);
        
        $cropper->setAllowedSizes(Yii::app()->params['allowedAvatarSizes']);
        
        if (! $cropper->isAllowedSize($thumbWidth, $thumbHeight)) {
            throw new CHttpException(404);
        }
        
        $cropper->getResizedImage($croppedAvatarDir . $img, $thumbWidth, $thumbHeight, $method);
    }

    public function actionInstall()
    {
        $auth = Yii::app()->authManager;
        
        $auth->clearAll();
        
        /**
         * Операции для UsersController
         */
        // $auth->createOperation('registerUser');
        $auth->createOperation('startTestUser');
        // $auth->createOperation('updateUser');
        // $auth->createOperation('deleteUser');
        // $auth->createOperation('adminUser');
        
        /**
         * Операции для TestController
         */
        
        $auth->createOperation('viewTest');
        $auth->createOperation('createTest');
        $auth->createOperation('updateTest');
        $auth->createOperation('deleteTest');
        $auth->createOperation('viewTeacherTests');
        $auth->createOperation('adminTest');
        $auth->createOperation('beginTest');
        
        /**
         * Операции для QuestionController
         */
        
        $auth->createOperation('viewQuestion');
        $auth->createOperation('createAnswerOption');
        $auth->createOperation('validateQuestionForm');
        $auth->createOperation('createQuestion');
        $auth->createOperation('updateQuestion');
        $auth->createOperation('deleteQuestion');
        $auth->createOperation('adminQuestion');
        
        /**
         * Операции для StudentTestController
         */
        
        $auth->createOperation('studentTests');
        
        $bizRuleTest = 'return Yii::app()->user->id==$params["test"]->teacher_id;';
        $bizRuleQuestion = 'return Yii::app()->user->id==$params["question"]->test->teacher_id;';
        $bizRuleBeginTest = 'return $params["beginAccess"]==true;';
        // $bizRuleUsers = 'return Yii::app()->user->id==$params["question"]->id;';
        
        /**
         * Задачи для TestController
         */
        
        $task = $auth->createTask('updateOwnTest', '', $bizRuleTest);
        $task->addChild('updateTest');
        
        $task = $auth->createTask('deleteOwnTest', '', $bizRuleTest);
        $task->addChild('deleteTest');
        
        $task = $auth->createTask('beginTestForMe', '', $bizRuleBeginTest);
        $task->addChild('beginTest');
        
        /**
         * Задачи для QuestionController
         */
        
        $task = $auth->createTask('updateOwnQuestion', '', $bizRuleQuestion);
        $task->addChild('updateQuestion');
        
        $task = $auth->createTask('deleteOwnQuestion', '', $bizRuleQuestion);
        $task->addChild('deleteQuestion');
        
        /**
         * GUEST
         */
        
        $role = $auth->createRole('guest');
        
        // Права GUEST для контроллера TEST
        
        $role->addChild('viewTest');
        
        /**
         * STUDENT
         */
        
        $role = $auth->createRole('student');
        
        // Права STUDENT для контроллера TEST
        
        $role->addChild('guest');
        
        // Права STUDENT для контроллера USERS
        
        $role->addChild('startTestUser');
        
        // Права STUDENT для контроллера StudentTests
        
        $role->addChild('studentTests');
        $role->addChild('beginTestForMe');
        
        /**
         * TEACHER
         */
        
        $role = $auth->createRole('teacher');
        
        // Права TEACHER для контроллера TEST
        
        $role->addChild('guest');
        $role->addChild('createTest');
        $role->addChild('updateOwnTest');
        $role->addChild('deleteOwnTest');
        $role->addChild('viewTeacherTests');
        
        // Права TEACHER для контроллера QUESTION
        
        $role->addChild('viewQuestion');
        $role->addChild('createAnswerOption');
        $role->addChild('validateQuestionForm');
        $role->addChild('createQuestion');
        $role->addChild('updateOwnQuestion');
        $role->addChild('deleteOwnQuestion');
        
        /**
         * ADMIN
         */
        
        $role = $auth->createRole('admin');
        
        // Права ADMIN для контроллера TEST
        
        $role->addChild('viewTest');
        $role->addChild('createTest');
        $role->addChild('updateTest');
        $role->addChild('deleteTest');
        $role->addChild('viewTeacherTests');
        $role->addChild('adminTest');
        
        // Права ADMIN для контроллера QUESTION
        
        $role->addChild('viewQuestion');
        $role->addChild('createAnswerOption');
        $role->addChild('validateQuestionForm');
        $role->addChild('createQuestion');
        $role->addChild('updateQuestion');
        $role->addChild('deleteQuestion');
        $role->addChild('adminQuestion');
        
        // Права ADMIN для контроллера USERS
        
        // $role->addChild('updateUser');
        // $role->addChild('deleteUser');
        // $role->addChild('adminUser');
        
        $auth->save();
    }
}