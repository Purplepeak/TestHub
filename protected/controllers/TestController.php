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

    protected $testTimeOut = false;

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
            'accessControl',
            'postOnly + delete + postQuestion'
                );
    }

    public function actions()
    {
        return array(
            'getRowForm' => array(
                'class' => 'application.components.DynamicTabularForm.actions.GetRowForm',
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
                'allow',
                'actions' => array(
                    'view',
                    'postQuestion',
                    'result'
                ),
                'roles' => array(
                    'viewTest'
                )
            ),
            array(
                'allow',
                'actions' => array(
                    'process',
                    'init'
                ),
                'roles' => array(
                    'beginTest' => array(
                        'beginAccess' => $this->beginTestAccessRule(array(
                            'process',
                            'init'
                        ))
                    )
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
                'allow',
                'actions' => array(
                    'admin'
                ),
                'roles' => array(
                    'adminTest'
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

    /**
     * Отображает информацию о тесте. У студента есть возможность начать тест, у преподавателя изменить.
     */
    public function actionView($id)
    {
        $model = $this->loadModel($id);
        
        $studentTest = StudentTest::model()->find('test_id=:testId AND student_id=:studentId', array(
            'testId' => $id,
            'studentId' => Yii::app()->user->id
        ));
        
        $testInProgress = false;
        
        if ($studentTest) {
            $model->attempts = $studentTest->attempts;
            $model->deadline = Yii::app()->params["dataHandler"]->handleDataTimezone($studentTest->deadline . '[Y-m-d H:i]');
            $testInProgress = $studentTest->checkTestInProgress();
        }
        
        $isTeacher = false;
        
        if ($model->teacher->id == Yii::app()->user->id) {
            $isTeacher = true;
        }
        
        $this->render('view', array(
            'model' => $model,
            'studentTestModel' => $studentTest,
            'testInProgress' => $testInProgress,
            'isTeacher' => $isTeacher
        ));
    }

    /**
     * Действие выполняется при начале теста студентом.
     */
    public function actionInit($id)
    {
        $studentTest = StudentTest::model()->find('test_id=:testId AND student_id=:studentId', array(
            'testId' => $id,
            'studentId' => Yii::app()->user->id
        ));
        
        $studentTest->attempts = $studentTest->attempts - 1;
        $studentTest->start_time = date('Y-m-d H:i:s');
        $studentTest->result = null;
        $studentTest->end_time = null;
        $studentTest->update();
        
        StudentAnswer::model()->deleteAll('test_result=:testResult', array(
            ':testResult' => $studentTest->id
        ));
        
        $this->redirect(array(
            'process',
            'id' => $id
        ));
    }

    /**
     * Выполнение теста.
     */
    public function actionProcess($id)
    {
        if (isset($_POST['StudentAnswer'])) {
            
            /**
             * Перед тем как принять на проверку ответ, проверяем не вышло ли время на выполнение теста.
             * Если время вышло, редиректим на страницу с результатом.
             */
            
            if (! Test::model()->checkTestTimeLimit($_POST['testStartTime'], $_POST['testTimeLimit'])) {
                Yii::app()->user->setState('endTest', true);
                if (Yii::app()->request->isAjaxRequest) {
                    echo json_encode(array(
                        'redirect' => $this->createUrl('test/result', array(
                            'id' => $_POST['testId']
                        ))
                    ));
                    
                    Yii::app()->end();
                } else {
                    $this->redirect(array(
                        'result',
                        'id' => $_POST['testId']
                    ));
                }
            }
            
            /**
             * Назначаем и сохраняем ответ.
             */
            
            $studentAnswer = StudentAnswer::model()->getAnswerModel($_POST['StudentAnswer']['question_id'], $_POST['StudentAnswer']['scenario'], $_POST['testId']);
            
            $studentAnswer->attributes = $_POST['StudentAnswer'];
            
            if (isset($_POST['StudentAnswer']['selectedAnswers'])) {
                $studentAnswer->selectedAnswers = $_POST['StudentAnswer']['selectedAnswers'];
            }
            
            if ($studentAnswer->validate()) {
                $studentAnswer->save(false);
                echo CJSON::encode(array(
                    'validateStatus' => 'success'
                ));
            } else {
                $error = CActiveForm::validate($studentAnswer);
                if ($error != '[]')
                    echo $error;
            }
            
            Yii::app()->end();
        }
        
        /**
         * Ниже извлекаем информацию о тесте и вопросах к нему относящихся. Если через GET передан
         * номер вопроса, рендерим его, иначе рендерим первый вопрос теста.
         */
        
        $test = $this->loadModel($id);
        
        $directQuestionNumber = 1;
        
        if ($directQuestionNumber = Yii::app()->request->getQuery('q')) {
            if ($directQuestionNumber === 'end') {
                $this->redirect(array(
                    'process',
                    'id' => $test->id
                ));
            }
        } else {
            $directQuestionNumber = 1;
        }
        
        if ($directQuestionNumber > count($test->question)) {
            throw new CHttpException(404);
        }
        
        $studentTest = StudentTest::model()->find('test_id=:testId AND student_id=:studentId', array(
            'testId' => $test->id,
            'studentId' => Yii::app()->user->id
        ));
        
        if (!isset($studentTest->start_time) || isset($studentTest->end_time)) {
            $this->redirect(array(
                'view',
                'id' => $test->id
            ));
        }
        
        $questionDataArray = array(); // Массив содержащий необходимую информацию  о всех вопросах
        $questionNumberIdPair = array(); // Массив номер вопроса => ID вопроса
        
        foreach ($test->question as $key => $question) {
            $questionDataArray[$key + 1] = array(
                'id' => $question->id,
                'title' => $question->title,
                'type' => $question->type,
                'answerIdTextPair' => $question->answerIdTextPair  // Массив содержит пару ID варианта ответа => Текст варианта ответа
            );
            $questionNumberIdPair[$key + 1] = $question->id;
        }
        
        $answerModel = StudentAnswer::model()->getAnswerModel($questionDataArray[$directQuestionNumber]['id'], $questionDataArray[$directQuestionNumber]['type'], $test->id);
        
        $this->render('test_starter', array(
            'test' => $test,
            'directQuestionNumber' => $directQuestionNumber,
            'answerModel' => $answerModel,
            'questionDataArray' => $questionDataArray,
            'questionNumberIdPair' => $questionNumberIdPair,
            'testTimeLimit' => $test->time_limit * 60,
            'testStartTime' => strtotime($studentTest->start_time)
        ));
    }
    
    /**
     * Если тест еще не выполнялся или выполняется повторно, действие подсчитывает 
     * результат теста и приводит запись в таблице Student_test к соответствующему виду 
     * (присваиваются значения столбцам result и end_time). Иначе просто выводим результат 
     * выполнения теста.
     */

    public function actionResult($id)
    {
        $studentTest = StudentTest::model()->find('test_id=:testId AND student_id=:studentId', array(
            ':testId' => $id,
            ':studentId' => Yii::app()->user->id
        ));
        
        if ($studentTest) {
            $studentTotalScore = $studentTest->result;
            $timeOutMessage = '';
            
            if (isset($_POST['endTest']) || Yii::app()->user->getState('endTest') === true) {
                
                $studentTotalScore = 0;
                if ($studentTest->studentAnswers) {
                    foreach ($studentTest->studentAnswers as $answer) {
                        $studentTotalScore = $studentTotalScore + $answer->result;
                    }
                }
                
                $studentTest->result = $studentTotalScore;
                $studentTest->end_time = date('Y-m-d H:i');
                
                $timeLimit = strtotime($studentTest->start_time) + $studentTest->test->time_limit * 60;
                
                if (strtotime($studentTest->end_time) > $timeLimit) {
                    $studentTest->end_time = date('Y-m-d H:i', $timeLimit);
                }
                
                $studentTest->update();
                
                if (Yii::app()->user->getState('endTest')) {
                    $timeOutMessage = 'Время, отведенное на выполнение теста, вышло.';
                }
                
                Yii::app()->user->setState('endTest', null);
                
                $this->redirect(array(
                    'result',
                    'id' => $id
                ));
            }
            
            if (is_null($studentTotalScore)) {
                $this->redirect(array(
                    'view',
                    'id' => $id
                ));
            }
            
            $message = 'Тест был успешно сдан.';
            $testPassed = true;
            
            if ($studentTotalScore < $studentTest->test->minimum_score) {
                $message = 'Вы набрали недостаточно баллов для прохождения теста';
                $testPassed = false;
            }
            
            $this->render('test_result', array(
                'totalScore' => $studentTotalScore,
                'studentTest' => $studentTest,
                'message' => $message,
                'timeOutMessage' => $timeOutMessage,
                'testPassed' => $testPassed
            ));
        } else {
            $this->redirect(array(
                'view',
                'id' => $id
            ));
        }
    }
    
    /**
     * Действие допускает только POST запросы и предназначено для динамического отображения запрашиваемого вопроса.
     */

    public function actionPostQuestion()
    {
        if (isset($_POST) && isset($_POST['questionNumber']) && isset($_POST['questionNumberIdPair'])) {
            $questionNumber = $_POST['questionNumber'];
            $numberOfQuestions = count($_POST['questionNumberIdPair']);
            
            $questionDataArray = array();
            $studentAnswersQuestionId = array();
            $answerModel = null;
            
            if (Yii::app()->request->getPost('questionDataArray')) {
                $questionDataArray = $_POST['questionDataArray'];
                $answerModel = StudentAnswer::model()->getAnswerModel($questionDataArray['id'], $questionDataArray['type'], $_POST['testID']);
            }
            
            $studentAnswersQuestionId = Test::model()->getStudentAnswersByQuestionsId($_POST['questionNumberIdPair']);
            
            $questionAlert = '';
            
            if ($questionNumber == 'end') {
                $questionAlert = 'Завершить тест?';
                
                if (count($studentAnswersQuestionId) < $numberOfQuestions) {
                    $questionAlert = 'Вы ответили не на все вопросы. ' . $questionAlert;
                }
            }
            
            $this->renderPartial('test_question', array(
                'answerModel' => $answerModel,
                'testID' => $_POST['testID'],
                'questionNumber' => $questionNumber,
                'questionNumberIdPair' => $_POST['questionNumberIdPair'],
                'questionDataArray' => $questionDataArray,
                'questionAlert' => $questionAlert,
                'numberOfQuestions' => $numberOfQuestions,
                'studentAnswersQuestionId' => $studentAnswersQuestionId,
                'testTimeLimit' => $_POST['testTimeLimit'],
                'testStartTime' => $_POST['testStartTime']
            ), false, true);
        }
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
                
                $this->redirect(array(
                    'test/view',
                    'id' => $test->id
                ));
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
                        $numberOfOptions = range(1, count($value['answerOptionsArray']));
                        $question->answerOptionsArray = array_combine($numberOfOptions, $value['answerOptionsArray']);
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
                    'test/view',
                    'id' => $test->id
                ));
            }
        }
        
        $this->render('create', array(
            'test' => $test,
            'questions' => $questions
        ));
    }
    
    /**
     * Выводит тесты относящиеся к преподавателю
     */

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

    private function beginTestAccessRule($actionsArray)
    {
        if (in_array($this->action->id, $actionsArray) && $this->actionParams && $this->actionParams['id']) {
            $studentTest = StudentTest::model()->find('test_id=:testId AND student_id=:studentId AND attempts >= 1 AND deadline > NOW()', array(
                'testId' => $this->actionParams['id'],
                'studentId' => Yii::app()->user->id
            ));
            
            if (is_null($studentTest)) {
                return false;
            } else {
                return true;
            }
        }
    }
}
