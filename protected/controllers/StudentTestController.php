<?php

class StudentTestController extends Controller
{

    public $layout = '//layouts/column2';
    
    public function filters()
    {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete' // we only allow deletion via POST request
        );
    }

    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('StudentTest');
        $this->render('index', array(
            'dataProvider' => $dataProvider
        ));
    }

    public function accessRules()
    {
        return array(
            array(
                'allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array(
                    'index',
                    'view'
                ),
                'users' => array(
                    '*'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'myTests'
                ),
                'expression' => "UsersController::userAccess('student')"
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
    
    public function actionMyTests()
    {
        $model = new StudentTest();
   
        $model->unsetAttributes();
    
        if (isset($_GET['StudentTest'])) {
            $model->attributes = $_GET['StudentTest'];
            $model->testName = isset($_GET['StudentTest']['testName']) ? $_GET['StudentTest']['testName'] : '';
            $model->testTimeLimit = isset($_GET['StudentTest']['testTimeLimit']) ? $_GET['StudentTest']['testTimeLimit'] : '';
        }
    
        $this->render('s_tests', array(
            'model' => $model
        ));
    }

    public function actionCreate()
    {
        $model = new StudentTest();
        
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'client-account-create-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
        
        if (isset($_POST['StudentTest'])) {
            $model->attributes = $_POST['StudentTest'];
            if ($model->validate()) {
                $this->saveModel($model);
                $this->redirect(array(
                    'view',
                    'student_id' => $model->student_id,
                    'test_id' => $model->test_id
                ));
            }
        }
        $this->render('create', array(
            'model' => $model
        ));
    }

    public function actionDelete($student_id, $test_id)
    {
        if (Yii::app()->request->isPostRequest) {
            try {
                // we only allow deletion via POST request
                $this->loadModel($student_id, $test_id)->delete();
            } catch (Exception $e) {
                $this->showError($e);
            }
            
            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (! isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array(
                    'index'
                ));
        } else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    public function actionUpdate($student_id, $test_id)
    {
        $model = $this->loadModel($student_id, $test_id);
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['StudentTest'])) {
            $model->attributes = $_POST['StudentTest'];
            $this->saveModel($model);
            $this->redirect(array(
                'view',
                'student_id' => $model->student_id,
                'test_id' => $model->test_id
            ));
        }
        
        $this->render('update', array(
            'model' => $model
        ));
    }

    public function actionAdmin()
    {
        $model = new StudentTest('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['StudentTest']))
            $model->attributes = $_GET['StudentTest'];
        
        $this->render('admin', array(
            'model' => $model
        ));
    }

    public function actionView($student_id, $test_id)
    {
        $model = $this->loadModel($student_id, $test_id);
        $this->render('view', array(
            'model' => $model
        ));
    }

    public function loadModel($student_id, $test_id)
    {
        $model = StudentTest::model()->findByPk(array(
            'student_id' => $student_id,
            'test_id' => $test_id
        ));
        if ($model == null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function saveModel($model)
    {
        try {
            $model->save();
        } catch (Exception $e) {
            $this->showError($e);
        }
    }

    function showError(Exception $e)
    {
        if ($e->getCode() == 23000)
            $message = "This operation is not permitted due to an existing foreign key reference.";
        else
            $message = "Invalid operation.";
        throw new CHttpException($e->getCode(), $message);
    }
}