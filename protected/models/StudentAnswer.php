<?php

/**
 * This is the model class for table "student_answer".
 *
 * The followings are the available columns in table 'student_answer':
 * @property integer $question_id
 * @property integer $student_id
 * @property integer $answer_id
 * @property string $answer_text
 * @property string $answer_number
 * @property integer $exec_time
 * @property integer $result
 * @property integer $test_result
 */
class StudentAnswer extends CActiveRecord
{

    public $testId;

    public $selectedAnswers;
    
    private $resultpoints;

    /**
     *
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'student_answer';
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(
                'question_id, student_id, exec_time',
                'required'
            ),
            array(
                'question_id, student_id, answer_id, exec_time, result, test_result',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'answer_text',
                'length',
                'max' => 50
            ),
            array(
                'answer_number',
                'length',
                'max' => 9
            ),
            array(
                'selectedAnswers',
                'required',
                'message' => 'Поле не должно быть пустым',
                'on' => 'selectMany, selectOne'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'question_id, student_id, answer_id, answer_text, answer_number, exec_time, result, test_result',
                'safe',
                'on' => 'search'
            )
        );
    }

    /**
     *
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'question' => array(
                self::BELONGS_TO,
                'Question',
                'question_id'
            ),
            'testResult' => array(
                self::BELONGS_TO,
                'StudentTest',
                'test_result'
            ),
            'manyAnswers1' => array(
                self::MANY_MANY,
                'AnswerOptions',
                's_many_answers(answer_id, s_answer)'
            )
        );
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                $testResult = StudentTest::model()->find('test_id=:testId AND student_id=:studentId', array(
                    ':testId' => $this->testId,
                    ':studentId' => $this->student_id
                ));
                
                $this->test_result = $testResult->id;
            }
            
            return true;
        }
        
        return false;
    }

    protected function afterSave()
    {
        parent::afterSave();
        
        if ($this->scenario === 'selectOne' || $this->scenario === 'selectMany') {
            $criteria = new CDbCriteria();
            $criteria->addInCondition('option_number', $this->selectedAnswers);
            $criteria->addSearchCondition('question_id', $this->question_id);
            $studentAnswers = AnswerOptions::model()->findAll($criteria);
            
            if (count($studentAnswers) > 1) {
                $manyAnswersId = array();
                foreach ($studentAnswers as $studentAnswer) {
                    array_push($manyAnswersId, array(
                        'answer_id' => $this->id,
                        's_answer' => $studentAnswer->id
                    ));
                }
                
                $builder = Yii::app()->db->schema->commandBuilder;
                $insertManyAnswer = $builder->createMultipleInsertCommand('s_many_answers', $manyAnswersId);
                $insertManyAnswer->execute();
            } else {
                $singleAnswerId = $studentAnswers[0]->id;
                $this->updateByPk($this->id, array(
                    'answer_id' => $singleAnswerId
                ));
            }
        }
    }
    
    public function compareNumbers()
    {
        $userAnswerPrecision = ($this->answer_number/100) * $this->question->precision_percent;
        
        if(abs($this->question->answer_number - $this->answer_number) <= $userAnswerPrecision) {
            $this->resultpoints = $this->question->difficulty;
        } else {
            $this->resultpoints = 0;
        }
        
        $this->updateByPk($this->id, array(
            'result' => $this->resultpoints
        ));
    }
    
    public function compareTextAnswer()
    {
        $answer = array('correct' => mb_strtolower($this->question->answer_text), 'studentAnswer' => mb_strtolower($this->answer_text));
        
        $formatAnswer = str_replace(' ', '', $answer);
        
        if($formatAnswer['correct'] === $formatAnswer['studentAnswer']) {
            $this->resultpoints = $this->question->difficulty;
        } else {
            $this->resultpoints = 0;
        }
        
        $this->updateByPk($this->id, array(
            'result' => $this->resultpoints
        ));
    }
    
    public function compareManyAnswers()
    {
        $studentAnswers = $this->manyAnswers1;
    
        $correctAnswers = $this->question->correctAnswer1;
    
        $answersArray = array(
            'correct' => $correctAnswers,
            'studentAnswers' => $studentAnswers
        );
    
        $formatAnswers = function ($array)
        {
            $formattedAnswers = array();
    
            foreach ($array as $answer) {
                $formattedAnswers[$answer->option_text] = $answer->option_number;
            }
    
            return $formattedAnswers;
        };
    
        $answersArray = array_map($formatAnswers, $answersArray);
    
        if (count($answersArray['correct']) != count($answersArray['studentAnswers'])) {
            $resultPoints = 0;
        }
    
        if (empty(array_diff($answersArray['correct'], $answersArray['studentAnswers']))) {
            $resultPoints = $this->question->difficulty;
            
            StudentTest::model()->updateByPk($this->test_result, array(
            'result' => $this->testResult->result + $resultPoints
            ));
        } else {
            $resultPoints = 0;
        }
        
        $this->updateByPk($this->id, array(
            'result' => $resultPoints
        ));
    }

    public function compareSingleAnswer()
    {
        if ($this->answer_id === $this->question->answer_id) {
            
            $resultPoints = $this->question->difficulty;
            
            StudentTest::model()->updateByPk($this->test_result, array(
                'result' => $this->testResult->result + $resultPoints
            ));
        } else {
            $resultPoints = 0;
        }
        
        $this->updateByPk($this->id, array(
            'result' => $resultPoints
        ));
    }
    
    public function compareAnswer()
    {
        if($this->scenario == 'selectOne') {
            if ($this->answer_id === $this->question->answer_id) {
                $resultPoints = $this->question->difficulty;
            } else {
                $resultPoints = 0;
            }
        }
        
        if($this->scenario == 'selectMany') {
            $studentAnswers = $this->manyAnswers1;
            
            $correctAnswers = $this->question->correctAnswer1;
            
            $answersArray = array(
                'correct' => $correctAnswers,
                'studentAnswers' => $studentAnswers
            );
            
            $formatAnswers = function ($array)
            {
                $formattedAnswers = array();
            
                foreach ($array as $answer) {
                    $formattedAnswers[$answer->option_text] = $answer->option_number;
                }
            
                return $formattedAnswers;
            };
            
            $answersArray = array_map($formatAnswers, $answersArray);
            
            if (count($answersArray['correct']) != count($answersArray['studentAnswers'])) {
                $resultPoints = 0;
            }
            
            if (empty(array_diff($answersArray['correct'], $answersArray['studentAnswers']))) {
                $resultPoints = $this->question->difficulty;
            
                StudentTest::model()->updateByPk($this->test_result, array(
                'result' => $this->testResult->result + $resultPoints
                ));
            } else {
                $resultPoints = 0;
            }
        }
        
        if($this->scenario == 'number') {
            $userAnswerPrecision = ($this->answer_number/100) * $this->question->precision_percent;
            
            if(abs($this->question->answer_number - $this->answer_number) <= $userAnswerPrecision) {
                $resultPoints = $this->question->difficulty;
            } else {
                $resultPoints = 0;
            }
        }
        
        if($this->scenario == 'string') {
            if ($this->answer_id === $this->question->answer_id) {
            
                $resultPoints = $this->question->difficulty;
            
                StudentTest::model()->updateByPk($this->test_result, array(
                'result' => $this->testResult->result + $resultPoints
                ));
            } else {
                $resultPoints = 0;
            }
        }
        
        $this->updateByPk($this->id, array(
            'result' => $resultPoints
        ));
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'question_id' => 'Question',
            'student_id' => 'Student',
            'answer_id' => 'Answer',
            'answer_text' => 'Answer Text',
            'answer_number' => 'Answer Number',
            'exec_time' => 'Exec Time',
            'result' => 'Result',
            'test_result' => 'Test Result',
            'selectedAnswers' => 'Student one-many answer'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     *         based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria();
        
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('student_id', $this->student_id);
        $criteria->compare('answer_id', $this->answer_id);
        $criteria->compare('answer_text', $this->answer_text, true);
        $criteria->compare('answer_number', $this->answer_number, true);
        $criteria->compare('exec_time', $this->exec_time);
        $criteria->compare('result', $this->result);
        $criteria->compare('test_result', $this->test_result);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className
     *            active record class name.
     * @return StudentAnswer the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
