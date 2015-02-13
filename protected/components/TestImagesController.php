<?php

class TestImagesController extends Controller
{

    /**
     *
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';
    
    protected $model;
    
    protected $defaultModel;
    
    //protected $imageModel;
    
    private $tmpDir;

    /**
     *
     * @return array action filters
     */
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
                    'view',
                    'tmpUpload',
                    'bub',
                    'deleteTmpImage'
                ),
                'users' => array(
                    '*'
                )
            ),
            array(
                'allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array(
                    'create',
                    'update'
                ),
                'users' => array(
                    '@'
                )
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'admin',
                    'delete'
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
    
    public function actions()
    {
        return array(
            'tmpUpload' => array(
                'model' => $this->defaultModel,
                'attribute' => 'imageFile',
                'class' => 'ext.file-upload-action.EFileUploadAction',
                'createDirectory' => true,
                'createDirectoryMode' => 0777,
                'createDirectoryRecursive' => true,
                'filenameRule' => 'md5(date("m-d-H-i-s")).".".$file->extensionName',
                'path' => realpath(Yii::app()->basePath . '/..') . Yii::app()->params['tmpDir'] . '/',
    
                'onBeforeUpload' => function ($event)
                {
                    if(CUploadedFile::getInstanceByName($event->sender->name) === null) {
                        $result = CJSON::encode(array('error' => true, 'message' => 'Выбранный файл не является изображением'));
    
                        echo stripcslashes($result);
                        exit();
                    }
                },
    
                'onAfterUpload' => function ($event)
                {
                    if ($event->sender->hasErrors()) {
                        $result = CJSON::encode(array('error' => true, 'message' => $event->sender->getErrors()));
                    } else {
                        $file = array(
                            'filelink' => Yii::app()->baseUrl . Yii::app()->params['tmpDir']  .'/'. $event->sender->filename
                        );
    
                        $result = CJSON::encode($file);
                    }
                    echo stripcslashes($result);
                    exit();
                }
            )
        );
    }
    
    public function actionBub()
    {
        $a = 50;
        $resultImageDir = Yii::getPathOfAlias('questionImages') . "/{$a}";
        $imageDir = Yii::app()->file->set($resultImageDir, true);
        
       if($imageDir->isEmpty) {
           echo 'pusto';
       } else {
           echo 'polno';
       }
       
       var_dump($imageDir->dirname);
    }

    /**
     * Displays a particular model.
     * 
     * @param integer $id
     *            the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
            'model' => $this->loadModel($id)
        ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = $this->imageModel;
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['TestImages'])) {
            $model->attributes = $_POST['TestImages'];
            if ($model->save())
                $this->redirect(array(
                    'view',
                    'id' => $model->id
                ));
        }
        
        $this->render('create', array(
            'model' => $model
        ));
    }

    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['TestImages'])) {
            $model->attributes = $_POST['TestImages'];
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

    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (! isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
                'admin'
            ));
    }
    
    public function actionAdmin()
    {
        $model = new TestImages('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['TestImages']))
            $model->attributes = $_GET['TestImages'];
        
        $this->render('admin', array(
            'model' => $model
        ));
    }
    
    public function actionDeleteTmpImage()
    {
        if (isset($_POST['url'])) {
            $imageRelativeUrl = mb_substr($_POST['url'], mb_strlen(Yii::app()->request->hostInfo.Yii::app()->request->baseUrl));
            
            if(mb_strpos($imageRelativeUrl, Yii::app()->params['tmpDir']) !== false) {
                $file = Yii::app()->file->set(Yii::getPathOfAlias('webroot') . $imageRelativeUrl, true);
                
                $file->delete();
            }
        }
    }
}
