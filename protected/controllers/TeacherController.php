<?php

class TeacherController extends UsersController
{
    protected $model;
    
    protected $defaultModel;
    
    public function init()
    {
        $this->defaultModel = Teacher::model();
    
        parent::init();
    }
    
    public function beforeAction($action)
    {
        $this->userModel = new Teacher();
        $this->index = 'Teacher';
        return parent::beforeAction($action);
    }

    public function actionRegistration()
    {
        $this->userModel = new Teacher('register');
        parent::actionRegistration();
    }

    public function actionAdmin()
    {
        $this->userModel = new Teacher('search');
        parent::actionAdmin();
    }

    public function actionList()
    {
        $model = new Teacher('search');
        $model->unsetAttributes();
        if (isset($_GET['Teacher'])) {
            $model->attributes = $_GET['Teacher'];
            $model->fullname = isset($_GET['Teacher']['fullname']) ? $_GET['Teacher']['fullname'] : '';
            $model->groupNumber = isset($_GET['Teacher']['groupNumber']) ? $_GET['Teacher']['groupNumber'] : '';
        }
        
        $this->render('list', array(
            'model' => $model
        ));
    }

    public function actionChangeAvatar()
    {
        $this->userModel = new Teacher('changeAvatar');
        
        parent::actionChangeAvatar();
    }

    public function actionTests()
    {
        $model = new Teacher();
        
        $this->render('tests', array('model' => $model));
    }

    public function actionTestss()
    {
        /*
         * $testModel = new Test(); $attributes = array( 'name' => 'Проверка', 'foreword' => 'Настоящий тест', 'minimum_score' => '80', 'time_limit' => '120000', 'attempts' => '3', 'deadline' => date('Y-m-d G:i:s'), 'teacher_id' => '646', 'testGroups' => '3017-3018' ); $testModel->attributes = $attributes; //$testModel->isTeacherGroupExist(); if($testModel->validate()) { $testModel->save(false); } else { var_dump($testModel->errors); } //$criteria = new CDbCriteria(); //$criteria->addInCondition('group_id', array(204, 205)); //$groups = Student::model()->findAll($criteria); //var_dump('rr');
         */
        /*
        $answerOptionsArray = array(
            '1' => 'Букусима-2',
            '2' => 'Быльская АЭС',
            '3' => 'Формула-1',
            '4' => 'Абыр-быр-бе'
        );
        
        $questionModel = new Question();
        
        $aswersModel = new AnswerOptions();
        
        if (1 > 2) {
            $attributes = array(
                'title' => 'Какая ЧАЭС является причиной крупнейшей экологической катастрофы?',
                'type' => 'select_one',
                'difficulty' => '2'
            );
            $questionModel->attributes = $attributes;
            
            if ($questionModel->validate()) {
                $questionModel->save(false);
            } else {
                var_dump($questionModel->errors);
            }
            
            $optionsArray = array();
            
            foreach ($answerOptionsArray as $number => $option) {
                array_push($optionsArray, array(
                    'question_id' => 3,
                    'option_text' => $option,
                    'option_number' => $number
                ));
            }
            
            $builder = Yii::app()->db->schema->commandBuilder;
            $command = $builder->createMultipleInsertCommand('answer_options', $optionsArray);
            $command->execute();
        } elseif (1 > 2) {
            $questionModel->scenario = 'selectMany';
            $attributes = array(
                'title' => 'Такая ХУЕС является причиной крупнейшей экологической катастрофы?',
                'type' => 'select_many',
                'difficulty' => '10',
                'test_id' => 2,
                'answerOptions' => array(
                    '1' => 'Букусима-2',
                    '2' => 'Быльская АЭС',
                    '3' => 'Формула-1',
                    '4' => 'Абыр-быр-бе'
                ),
                'correctAnswers' => array(
                    4,
                    1
                )
            );
            
            $questionModel->attributes = $attributes;
            
            if ($questionModel->validate()) {
                $questionModel->save(false);
            } else {
                var_dump($questionModel->errors);
            }
        }
        
        if (1 > 2) {
            $questionModel->scenario = 'numeric';
            $attributes = array(
                'title' => 'Напишите чему равно число Пи с точность до четырех знаков после запятой.',
                'type' => 'numeric',
                'difficulty' => '3',
                'answer_number' => 3.1415,
                'precision_percent' => 0.1,
                'test_id' => 2
            );
            
            $questionModel->attributes = $attributes;
            
            if ($questionModel->validate()) {
                $questionModel->save(false);
            } else {
                var_dump($questionModel->errors);
            }
        }
        
        if (1 > 3) {
            $questionModel->scenario = 'string';
            $attributes = array(
                'title' => 'Название самого глубокого озера в мире.',
                'type' => 'string',
                'difficulty' => '2',
                'answer_text' => 'Байкал',
                'test_id' => 2
            );
            
            $questionModel->attributes = $attributes;
            
            if ($questionModel->validate()) {
                $questionModel->save(false);
            } else {
                var_dump($questionModel->errors);
            }
        }
        
        if (1 > 2) {
            $questionModel->scenario = 'selectOne';
            $attributes = array(
                'title' => 'Самая высокая гора в мире?',
                'type' => 'select_one',
                'difficulty' => '3',
                'test_id' => 2,
                'answerOptions' => array(
                    '1' => 'Лхоцзе',
                    '2' => 'Канченджанга',
                    '3' => 'Макалу',
                    '4' => 'Джомолунгма'
                ),
                'correctAnswers' => array(
                    4
                )
            );
            
            $questionModel->attributes = $attributes;
            
            if ($questionModel->validate()) {
                $questionModel->save(false);
            } else {
                var_dump($questionModel->errors);
            }
        }
        
        if (1 > 3) {
            $answer = new StudentAnswer();
            $answer->testId = 2;
            $answer->attributes = array(
                'question_id' => 3,
                'student_id' => 1963,
                'answer_text' => 'байкал',
                'exec_time' => 5
            );
            
            if ($answer->validate()) {
                $answer->save(false);
            } else {
                var_dump($answer->errors);
            }
        }
        
        if (1 > 3) {
            $answer = new StudentAnswer();
            $answer->testId = 2;
            $answer->attributes = array(
                'question_id' => 3,
                'student_id' => 1962,
                'answer_text' => 'байкал',
                'exec_time' => 5
            );
            
            if ($answer->validate()) {
                $answer->save(false);
            } else {
                var_dump($answer->errors);
            }
        }
        
        if (1 > 3) {
            $answer = new StudentAnswer();
            $answer->testId = 2;
            $answer->attributes = array(
                'question_id' => 2,
                'student_id' => 1962,
                'answer_number' => 3.1447,
                'exec_time' => 2
            );
            
            if ($answer->validate()) {
                $answer->save(false);
            } else {
                var_dump($answer->errors);
            }
        }
        
        if (1 > 3) {
            $answer = new StudentAnswer();
            $answer->scenario = 'selectMany';
            $answer->testId = 2;
            $answer->attributes = array(
                'question_id' => 1,
                'student_id' => 1962,
                'exec_time' => 5,
                'selectedAnswers' => array(
                    4,
                    1
                )
            );
            
            if ($answer->validate()) {
                $answer->save(false);
            } else {
                var_dump($answer->errors);
            }
        }
        
        if (2 > 3) {
            $answer = new StudentAnswer();
            $answer->scenario = 'selectOne';
            $answer->testId = 2;
            $answer->attributes = array(
                'question_id' => 4,
                'student_id' => 1950,
                'exec_time' => 3,
                'selectedAnswers' => array(
                    4
                )
            );
            
            if ($answer->validate()) {
                $answer->save(false);
            } else {
                var_dump($answer->errors);
            }
        }
        */
        // $model = $questionModel->findByPk(1);
        // $correctAnswer = $aswersModel->findAll('question_id=2 AND option_number=2');
        // $criteria = new CDbCriteria();
        // $criteria->addCondition('question_id=:question_id');
        // $criteria->addInCondition('option_number', array(2, 1));
        // $criteria->addSearchCondition('question_id', 3);
        // $bbr = AnswerOptions::model()->findAll($criteria);
        
        // $questionModel->updateByPk(1, array('answer_id' => $correctAnswer->id));
        // $model = $questionModel->findByPk(1);
        // $aModel = $aswersModel->find('question_id=1');
        // $a = $questionModel->findByPk(24);
        // $b = $aswersModel->find;
        /*
         * $br = $a->correctAnswer1; $data = array(); foreach($br as $ca) { array_push($data, array($ca->option_text,$ca-> option_number)); }
         */
        
        //$gg = $questionModel->selectCorrectNumber(2, 3.14465);
        // $t = Test::model()->findByPk(2);
        
        // $h = Question::model()->find('test_id=:testId', array(':testId' => 2));
        
        // $g = StudentAnswer::model()->find('question_id=1 AND student_id=1963');
        
        // var_dump($g->question->answer_text, $g->answer_text, $g->testResult);
        
        //$l = StudentAnswer::model()->findByPk(18);
        //$l->scenario = 'selectOne';
        // $l->compareSingleAnswer();
        // $k = new StudentAnswer;
        // $k->id = 6;
        // $l->compareManyAnswers();
        // $l->compareNumbers();
        // $l->compareTextAnswer();
        // $l->compareAnswer();
        
        $p = new Teacher;
        $u = $p->findAll();
        $m = Teacher::model()->findAll();
        
        $criteria = new CDbCriteria();
        
        $criteria->with = array(
            'teacher' => array(
                'select' => array(
                    'teacher.id',
                    'teacher.name',
                    'teacher.surname',
                    'teacher.type'
                ),
                'alias' => 'teacher',
                'together' => true,
                'condition' => 'teacher.active=true'
            ),
            'student' => array(
                'select' => array(
                    'student.id',
                    'student.name',
                    'student.surname',
                    'student.type'
                ),
                'alias' => 'student',
                'together' => true,
                'condition' => 'student.active=true'
            )
        );
        
        var_dump(Group::model()->findAll($criteria));
    }
}
