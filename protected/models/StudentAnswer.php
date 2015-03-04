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

    public $questionNumber;
    
    const NUMBER_M = 11;
    
    const NUMBER_D = 4;

    private $requiredMessage = 'Вы не ответили на вопрос';

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
                'answer_text',
                'required',
                'on' => 'string',
                'message' => $this->requiredMessage
            ),
            array(
                'answer_number',
                'required',
                'on' => 'numeric',
                'message' => $this->requiredMessage
            ),
            array(
                'answer_id',
                'required',
                'on' => 'select_one',
                'message' => $this->requiredMessage
            ),
            array(
                'selectedAnswers',
                'required',
                'on' => 'select_many',
                'message' => $this->requiredMessage
            ),
            array(
                'question_id, student_id, answer_id, exec_time, result, test_result',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'answer_number',
                'numerical',
                'integerOnly' => false,
                'message' => 'Ответ должен быть в виде цифры'
            ),
            array(
                'answer_text',
                'length',
                'max' => 200,
                'message' => 'Максимальное количество символов: 200'
            ),
            array(
                'answer_number',
                'numerical',
                'numberPattern' => '/^-?\d{1,' . self::NUMBER_M . '}\.?\d{0,' . self::NUMBER_D . '}$/',
                'message' => 'Число должно быть либо целым, либо с плавающей точкой. Максимум знаков относительно запятой: ' . self::NUMBER_M . ' — перед запятой, ' . self::NUMBER_D . ' — после.'
            ),
            array(
                'questionNumber, scenario',
                'safe'
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
    
    /**
     * Проверяет ответ число на допустимые значения
     */
    
    public function checkNumericAnswer()
    {
        if(!preg_match('/^(\d)+[\.,]?(\d)*$/', $this->answer_number, $matches)) {
            $this->addError('answer_number', 'Неверный формат числа');
            
            return false;
        }
        
        if(mb_strlen(strval($matches[1])) > self::NUMBER_M) {
            $this->addError('answer_number', 'Число превышает значение максимально допустимого');
            return false;
        }
        
        if(mb_strlen(strval($matches[2])) > self::NUMBER_D) {
            $this->addError('answer_number', 'Максимально количество знаков после запятой — четыре');
            return false;
        }
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
            
            $this->compareAnswer();
            
            return true;
        }
        
        return false;
    }

    protected function afterFind()
    {
        parent::afterFind();
        
        if ($this->manyAnswers1) {
            foreach ($this->manyAnswers1 as $selectedOption) {
                $this->selectedAnswers[] = $selectedOption->id;
            }
        }
        
        if ($this->answer_number) {
            $this->answer_number = floatval($this->answer_number);
        }
    }

    protected function afterSave()
    {
        parent::afterSave();
        
        if ($this->scenario === 'select_many') {
            
            if (! $this->isNewRecord) {
                SManyAnswers::model()->deleteAll('answer_id=:answerId', array(
                    ':answerId' => $this->id
                ));
            }
            if ($this->selectedAnswers) {
                $selectedAnswersId = array();
                foreach ($this->selectedAnswers as $selectedAnswer) {
                    array_push($selectedAnswersId, array(
                        'answer_id' => $this->id,
                        's_answer' => $selectedAnswer
                    ));
                }
                
                $builder = Yii::app()->db->schema->commandBuilder;
                $insertManyAnswer = $builder->createMultipleInsertCommand('s_many_answers', $selectedAnswersId);
                $insertManyAnswer->execute();
            }
        }
    }

    public function compareAnswer()
    {
        $this->result = 0;
        
        if ($this->scenario == 'select_one') {
            if ($this->answer_id === $this->question->answer_id) {
                $this->result = $this->question->difficulty;
            }
        }
        
        if ($this->scenario == 'select_many') {
            $correctOptionsId = array();
            
            foreach ($this->question->correctAnswer1 as $optionObject) {
                $correctOptionsId[] = $optionObject->id;
            }
            
            if (count($this->selectedAnswers) == count($correctOptionsId) && array_diff($correctOptionsId, $this->selectedAnswers) == false) {
                $this->result = $this->question->difficulty;
            }
        }
        
        if ($this->scenario == 'numeric') {
            $userAnswerPrecision = ($this->answer_number / 100) * $this->question->precision_percent;
            
            if (abs($this->question->answer_number - $this->answer_number) <= $userAnswerPrecision) {
                $this->result = $this->question->difficulty;
            }
        }
        
        if ($this->scenario == 'string') {
            $answer = array(
                'correct' => mb_strtolower($this->question->answer_text),
                'studentAnswer' => mb_strtolower($this->answer_text)
            );
            
            $formatAnswer = str_replace(' ', '', $answer);
            
            if ($formatAnswer['correct'] === $formatAnswer['studentAnswer']) {
                $this->result = $this->question->difficulty;
            }
        }
    }
    
    /**
     * В заваисимости от того, существует ли ответ на вопрос с ID $questionId, возвращаем модель ответа.
     */

    public function getAnswerModel($questionId, $questionType, $testId)
    {
        $studentCurrentAnswer = $this->find('question_id=:questionId AND student_id=:studentId', array(
            ':questionId' => $questionId,
            ':studentId' => Yii::app()->user->id
        ));
        
        if ($studentCurrentAnswer) {
            $answerModel = $studentCurrentAnswer;
            $answerModel->scenario = $questionType;
        } else {
            $answerModel = new self($questionType);
            $answerModel->testId = $testId;
            $answerModel->student_id = Yii::app()->user->id;
        }
        
        return $answerModel;
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
