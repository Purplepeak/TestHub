<?php

class GroupController extends Controller
{

    /**
     *
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

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
                    'list'
                ),
                'users' => array(
                    '*'
                )
            ),
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'admin',
                    'delete',
                    'create',
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

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Group();
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['Group'])) {
            $model->attributes = $_POST['Group'];
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

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * 
     * @param integer $id
     *            the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['Group'])) {
            $model->attributes = $_POST['Group'];
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

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * 
     * @param integer $id
     *            the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        $this->loadModel($id)->delete();
        
        // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
        if (! isset($_GET['ajax']))
            $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
                'admin'
            ));
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Group');
        $this->render('index', array(
            'dataProvider' => $dataProvider
        ));
    }

    public function actionList()
    {
        $model = new Group('search');
        
        if (isset($_GET['f']) && $_GET['f'] === 'mygroups') {
            $model->groupFilter = $_GET['f'];
        }
        
        $model->unsetAttributes();
        
        if (isset($_GET['Group'])) {
            $model->attributes = $_GET['Group'];
            $model->fullname = isset($_GET['Group']['fullname']) ? $_GET['Group']['fullname'] : '';
            $model->numberOfStudents = isset($_GET['Group']['numberOfStudents']) ? $_GET['Group']['numberOfStudents'] : '';
        }
        
        $this->render('list', array(
            'model' => $model
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Group('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['Group']))
            $model->attributes = $_GET['Group'];
        
        $this->render('admin', array(
            'model' => $model
        ));
    }

    public function loadModel($id)
    {
        $model = Group::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }
}
