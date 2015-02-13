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
                'allow',
                'actions' => array(
                    'myTests',
                ),
                'roles' => array(
                    'studentTests'
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
    
    /**
     * Показывает невыполненные, выполненные, проваленные (в зависимости от параметра $status) тесты относящиеся к студенту.
     */

    public function actionMyTests($status)
    {
        $model = new StudentTest();
        
        $model->unsetAttributes();
        
        if (isset($_GET['StudentTest'])) {
            $model->attributes = $_GET['StudentTest'];
            $model->testName = isset($_GET['StudentTest']['testName']) ? $_GET['StudentTest']['testName'] : '';
            $model->testTimeLimit = isset($_GET['StudentTest']['testTimeLimit']) ? $_GET['StudentTest']['testTimeLimit'] : '';
        }
        
        $model->testStatus = $status;
        
        $this->render('s_tests', array(
            'model' => $model
        ));
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