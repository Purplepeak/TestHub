<?php

class TestController extends Controller
{

    /**
     *
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     *      using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/column2';

    protected $model;
    
    protected $defaultModel;
    
    public function init()
    {
        $this->defaultModel = Test::model();
    
        parent::init();
    }

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

    public function actions()
    {
        return array(
            'getRowForm' => array(
                'class' => 'ext.DynamicTabularForm.actions.GetRowForm',
                'view' => 'question_form',
                'modelClass' => 'Question'
            )
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
                    'view'
                ),
                'roles' => array(
                    'viewTest'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'update'
                ),
                'roles' => array(
                    'updateTest' => array(
                        'test' => $this->getModelByActionId('update')
                    )
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'delete'
                ),
                'roles' => array(
                    'deleteTest' => array(
                        'test' => $this->getModelByActionId('delete')
                    )
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'teacher'
                ),
                'roles' => array(
                    'viewTeacherTests'
                )
            ),
            
            array(
                'allow',
                'actions' => array(
                    'create',
                    'getRowForm'
                ),
                'roles' => array(
                    'createTest'
                )
            ),
            
            array(
                'allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array(
                    'admin',
                ),
                'roles' => array(
                    'adminTest'
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

    public function actionCreate()
    {
        /**
         * Модель для теста и массив для вопросов, которые будет содержать тест
         */
        $test = new Test();
        $test->teacher_id = Yii::app()->user->id;
        $questions = array();
        
        /**
         * Ajax валидация будет будет доступна только для модели Test, за валидацию полей формы
         * вопросов будет отвечать экшн question/validateData
         */
        $this->performAjaxValidation($test);
        
        if (isset($_POST['Test'])) {
            $test->attributes = $_POST['Test'];
            
            if (isset($_POST['Question'])) {
                $questions = array();
                
                /**
                 * Каждый элемент массива $_POST['Question'] является набором атрибутов
                 * для каждого отдельного вопроса.
                 * Соответственно мы создаем для каждого элемента
                 * новый экземпляр модели "Question" и помещаем его в массив $questions.
                 */
                
                foreach ($_POST['Question'] as $key => $value) {
                    $question = new Question();
                    $question->attributes = $value;
                    $question->scenario = $value['modelScenario'];
                    
                    /**
                     * Если мы преподаватель создает вопрос в котором необходимо выбрать правильный ответ
                     * из ряда предложенных, срабатывает сценарий "select".
                     * Каждый вариант ответа будет
                     * расположен в массиве "answerOptionsArray".
                     */
                    
                    if ($question->scenario === 'select') {
                        $question->answerOptionsArray = $value['answerOptionsArray'];
                        $question->correctAnswers = $value['correctAnswers'];
                        $answerOptionsId = array();
                        foreach ($value['answerOptionsArray'] as $id => $option) {
                            $answerOptionsId[] = $id;
                        }
                        
                        /**
                         * Переменная $question->optionsNumber применяется для того, чтобы при ошибке
                         * валидции, css класс динамически добавляемого варианта ответа "answer-option-#"
                         * принимал правильное значение.
                         */
                        
                        $question->optionsNumber = $answerOptionsId;
                    }
                    
                    $questions[] = $question;
                }
            }
            
            /**
             * Ниже выполняется валидация и сохранение обеих моделей
             */
            
            $valid = $test->validate();
            foreach ($questions as $question) {
                $valid = $question->validate() && $valid;
            }
            
            if ($valid) {
                $transaction = $test->getDbConnection()->beginTransaction();
                try {
                    $test->save();
                    $test->refresh();
                    
                    foreach ($questions as $question) {
                        $question->test_id = $test->id;
                        $question->save();
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                }
                
                // $this->redirect(array('site/index'));
            }
        }
        $this->render('create', array(
            'test' => $test,
            'questions' => $questions
        ));
    }

    public function actionUpdate($id)
    {
        $test = $this->loadModel($id);
        
        $questions = $test->question;
        
        $this->performAjaxValidation($test);
        
        if (isset($_POST['Test'])) {
            $test->attributes = $_POST['Test'];
            
            if (isset($_POST['Question'])) {
                $questions = array();
                foreach ($_POST['Question'] as $key => $value) {
                    
                    if ($value['updateType'] == DynamicTabularForm::UPDATE_TYPE_CREATE) {
                        $question = new Question();
                    } else {
                        if ($value['updateType'] == DynamicTabularForm::UPDATE_TYPE_UPDATE) {
                            $question = Question::model()->findByPk($value['id']);
                        } else {
                            if ($value['updateType'] == DynamicTabularForm::UPDATE_TYPE_DELETE) {
                                $delete = Question::model()->findByPk($value['id']);
                                if ($delete->delete()) {
                                    unset($question);
                                    continue;
                                }
                            }
                        }
                    }
                    
                    $question->attributes = $value;
                    $question->scenario = $value['modelScenario'];
                    
                    if ($question->scenario === 'select') {
                        $question->answerOptionsArray = $value['answerOptionsArray'];
                        $question->correctAnswers = $value['correctAnswers'];
                        $answerOptionsId = array();
                        
                        foreach ($value['answerOptionsArray'] as $id => $option) {
                            $answerOptionsId[] = $id;
                        }
                        
                        $question->optionsNumber = $answerOptionsId;
                    }
                    
                    $questions[] = $question;
                }
            }
            
            $valid = $test->validate();
            foreach ($questions as $question) {
                $valid = $question->validate() & $valid;
            }
            
            if ($valid) {
                $transaction = $test->getDbConnection()->beginTransaction();
                try {
                    $test->save();
                    $test->refresh();
                    
                    foreach ($questions as $question) {
                        $question->test_id = $test->id;
                        $question->save();
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                }
                
                $this->redirect(array(
                    'test/update',
                    'id' => $test->id
                ));
            }
        }
        
        $this->render('create', array(
            'test' => $test,
            'questions' => $questions
        ));
    }

    public function actionTeacher()
    {
        $model = new Test('search');
        $model->unsetAttributes();
        
        if (isset($_GET['Test'])) {
            $model->attributes = $_GET['Test'];
            // $model->testGroups = isset($_GET['Test']['testGroups']) ? $_GET['Test']['testGroups'] : '';
        }
        
        $this->render('tests', array(
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
        $dataProvider = new CActiveDataProvider('Test');
        $this->render('index', array(
            'dataProvider' => $dataProvider
        ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Test('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['Test']))
            $model->attributes = $_GET['Test'];
        
        $this->render('admin', array(
            'model' => $model
        ));
    }
}
