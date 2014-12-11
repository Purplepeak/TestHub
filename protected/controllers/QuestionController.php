<?php

class QuestionController extends Controller
{

    /**
     *
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    protected $model;

    protected $defaultModel;

    /**
     *
     * @return array action filters
     */
    public function init()
    {
        $this->defaultModel = Question::model();
        
        parent::init();
    }

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
                'allow',
                'actions' => array(
                    'index'
                ),
                'users' => array(
                    '*'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'view'
                ),
                'roles' => array(
                    'viewQuestion'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'optionField'
                ),
                'roles' => array(
                    'createAnswerOption'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'validateData'
                ),
                'roles' => array(
                    'validateQuestionForm'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'create'
                ),
                'roles' => array(
                    'createQuestion'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'update'
                ),
                'roles' => array(
                    'updateQuestion' => array(
                        'question' => $this->getModelByActionId('update')
                    )
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'delete'
                ),
                'roles' => array(
                    'deleteQuestion' => array(
                        'question' => $this->getModelByActionId('update')
                    )
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'admin'
                ),
                'roles' => array(
                    'adminQuestion'
                )
            ),
            array(
                'deny',
                'users' => array(
                    '*'
                )
            )
        );
    }

    public function actionOptionField($i, $key, $number)
    {
        $model = new Question();
        
        $this->renderPartial('new_answer_field', array(
            'model' => $model,
            'i' => $i,
            'number' => $number,
            'key' => $key
        ));
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
        $model = new Question();
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['Question'])) {
            $model->attributes = $_POST['Question'];
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
        $a = array();
        foreach ($model->answerOptions as $option) {
            $a[$option->option_number] = $option->option_text;
        }
        var_dump($a);
        
        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);
        
        if (isset($_POST['Question'])) {
            $model->attributes = $_POST['Question'];
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
        var_dump($this->loadModel(27)->test->teacher_id);
        $dataProvider = new CActiveDataProvider('Question');
        $this->render('index', array(
            'dataProvider' => $dataProvider
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Question('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['Question']))
            $model->attributes = $_GET['Question'];
        
        $this->render('admin', array(
            'model' => $model
        ));
    }

    /**
     * Performs the AJAX validation.
     *
     * @param Question $model
     *            the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'question-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    /**
     * Экшн validateData осуществляет ajax валидацию динамически добавляемых Question форм.
     */
    public function actionValidateData()
    {
        if (isset($_POST['scenario'], $_POST['name'], $_POST['value'])) {
            $model = new Question($_POST['scenario']);
            $model->setAttribute($_POST['name'], $_POST['value']);
            $model->validate();
            echo CHtml::error($model, $_POST['name']);
            Yii::app()->end();
        } else {
            $this->redirect(array(
                'site/index'
            ));
        }
    }
}
