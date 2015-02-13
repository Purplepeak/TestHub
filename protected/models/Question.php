<?php

/**
 * This is the model class for table "question".
 *
 * The followings are the available columns in table 'question':
 * @property integer $id
 * @property string $title
 * @property string $type
 * @property integer $difficulty
 * @property integer $answer_id
 * @property string $answer_text
 * @property string $answer_number
 * @property integer $precision_percent
 *
 * The followings are the available model relations:
 * @property AnswerOptions[] $answerOptions
 */
class Question extends CActiveRecord
{

    public $updateType;

    public $modelScenario;

    public $answerOptionsArray;

    public $answerIdTextPair;
    
    public $questionNumber;

    /**
     * В массиве $optionsNumber содержатся уникальные номера для css класса
     * "answer-option-#".
     * С добавлением и удалением вариантов ответа массив будет изменяться
     * Колличество элементов массива соответствует
     * количеству вариантов ответа (по умолчанию 2).
     */
    public $optionsNumber = array(
        1,
        2
    );

    public $correctAnswers;

    const TYPE_ONE = 'select_one';

    const TYPE_MANY = 'select_many';

    const TYPE_STRING = 'string';

    const TYPE_NUMERIC = 'numeric';

    const DEFAULT_PRECISION = '0.00001';

    const NUMBER_M = 11;

    const NUMBER_D = 4;

    const PRECISION_M = 6;

    const PRECISION_D = 5;

    public function tableName()
    {
        return 'question';
    }

    public function behaviors()
    {
        return array(
            'CAdvancedArBehavior' => array(
                'class' => 'application.extensions.CAdvancedArBehavior'
            )
        );
    }

    public function rules()
    {
        return array(
            array(
                'title, difficulty',
                'required',
                'message' => 'Поле не должно быть пустым.'
            ),
            array(
                'id, updateType, modelScenario',
                'safe',
            ),
            array(
                'answer_number',
                'required',
                'message' => 'Поле не должно быть пустым.',
                'on' => 'numeric'
            ),
            array(
                'answer_text',
                'required',
                'message' => 'Поле не должно быть пустым.',
                'on' => 'string'
            ),
            array(
                'answer_number',
                'numerical',
                'numberPattern' => '/^\d{1,' . self::NUMBER_M . '}\.?\d{0,' . self::NUMBER_D . '}$/',
                'message' => 'Число должно быть либо целым, либо с плавающей точкой. Максимум знаков относительно запятой: ' . self::NUMBER_M . ' — перед запятой, ' . self::NUMBER_D . ' — после.'
            ),
            array(
                'precision_percent',
                'numerical',
                'numberPattern' => '/^\d{1,' . self::PRECISION_M . '}\.?\d{0,' . self::PRECISION_D . '}$/',
                'message' => 'Непредусмотренная процентная точность. Максимум знаков относительно запятой: ' . self::PRECISION_M . ' — перед запятой, ' . self::PRECISION_D . ' — после.'
            ),
            array(
                'correctAnswers, answerOptionsArray',
                'required',
                'message' => 'Поле не должно быть пустым.',
                'on' => 'select'
            ),
            array(
                'answerOptionsArray',
                'validateAnswerOptions',
                'on' => 'select'
            ),
            array(
                'correctAnswers',
                'match',
                'pattern' => '/^[\d,\s]+$/',
                'message' => 'Необходимо указать номера правильных ответов из вышепреведенных вариантов. Если ответов несколько, разделите их запятой: 1, 3.',
                'on' => 'select'
            ),
            array(
                'correctAnswers',
                'formatCorrectAnswers',
                'on' => 'select'
            ),
            array(
                'precision_percent',
                'default',
                //'setOnEmpty' => true,
                'value' => self::DEFAULT_PRECISION,
                'on' => 'numeric'
            ),
            array(
                'answer_id, test_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'difficulty',
                'numerical',
                'integerOnly' => true,
                'message' => 'Баллы должны быть в виде числа.'
            ),
            array(
                'type',
                'in',
                'range' => array(
                    self::TYPE_ONE,
                    self::TYPE_MANY,
                    self::TYPE_STRING,
                    self::TYPE_NUMERIC
                ),
                'message' => 'Указаный тип ответа не поддерживается.'
            ),
            array(
                'answer_text',
                'length',
                'max' => 50,
                'tooLong' => 'Ответ в виде текста должен содержать не более 50 символов.'
            ),
            array(
                'picture',
                'length',
                'max' => 200
            ),
            array(
                'answer_number',
                'length',
                'max' => 9,
                'tooLong' => 'Поле может содержать не более 9 символов.'
            ),
            array(
                'id, title, type, difficulty, answer_id, answer_text, answer_number, precision_percent, picture, test_id',
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
            'correctAnswer1' => array(
                self::MANY_MANY,
                'AnswerOptions',
                'correct_answers(question_id, c_answer)'
            ),
            'answerOptions' => array(
                self::HAS_MANY,
                'AnswerOptions',
                'question_id'
            ),
            'test' => array(
                self::BELONGS_TO,
                'Test',
                'test_id'
            ),
            'studentAnswer' => array(
                self::MANY_MANY,
                'StudentTest',
                'student_answer(question_id, test_result)'
            ),
            'question_images' => array(
                self::HAS_MANY,
                'QuestionImage',
                'question_id'
            )
        );
    }

    public function beforeDelete()
    {
        $file = Yii::app()->file->set(Yii::getPathOfAlias('questionImages') . '/' . $this->id, true);
        
        if ($file->exists) {
            $file->delete();
        }
        
        return parent::beforeDelete();
    }

    /**
     * Метод выполняет валидацию каждого отдельного
     */
    public function validateAnswerOptions()
    {
        if (is_array($this->answerOptionsArray)) {
            foreach ($this->answerOptionsArray as $optionNumber => $optionText) {
                if ($optionText === '') {
                    $this->addError("answerOptionsArray[{$optionNumber}]", 'Поле не должно быть пустым');
                }
            }
        } else {
            if ($this->answerOptionsArray === '') {
                $this->addError("answerOptionsArray", 'Поле не должно быть пустым');
            }
        }
    }

    public function formatCorrectAnswers()
    {
        if (! preg_match_all('/\d+/', $this->correctAnswers, $correctAnswersArray)) {
            $this->addError('correctAnswers', 'Необходимо указать номера правильных ответов из вышепреведенных вариантов. Если ответов несколько, разделите их запятой: 1, 3');
        }
        
        if (is_array($this->answerOptionsArray)) {
            foreach ($correctAnswersArray[0] as $correctNumber) {
                if ($correctNumber == 0 || $correctNumber > count($this->answerOptionsArray)) {
                    $this->addError('correctAnswers', 'Вариант с номером "' . $correctNumber . '" не найден.');
                }
            }
        }
        
        $this->correctAnswers = implode(', ', $correctAnswersArray[0]);
    }
    /*
     * protected function beforeValidate() { if ($this->isNewRecord) { switch ($this->scenario) { case 'selectOne': $this->type = self::TYPE_ONE; break; case 'selectMany': $this->type = self::TYPE_MANY; break; case 'string': $this->type = self::TYPE_STRING; break; case 'numeric': $this->type = self::TYPE_NUMERIC; break; default: $type = ''; break; } } if($this->scenario === 'numeric' && isset($this->precision_percent) == false) { $this->precision_percent = self::DEFAULT_PRECISION; } return parent::beforeValidate(); }
     */
    protected function afterFind()
    {
        parent::afterFind();
        
        switch ($this->type) {
            case self::TYPE_ONE:
                $this->scenario = 'select';
                break;
            case self::TYPE_MANY:
                $this->scenario = 'select';
                break;
            case self::TYPE_STRING:
                $this->scenario = 'string';
                break;
            case self::TYPE_NUMERIC:
                $this->scenario = 'numeric';
                break;
            default:
                $this->scenario = '';
                break;
        }
        
        $this->optionsNumber = array();
        
        foreach ($this->answerOptions as $answerOption) {
            $this->answerOptionsArray[$answerOption->option_number] = $answerOption->option_text;
            $this->optionsNumber[] = $answerOption->option_number;
            $this->answerIdTextPair[$answerOption->id] = $answerOption->option_text;
            
            if ($this->type === 'select_one' && $answerOption->id == $this->answer_id) {
                $this->correctAnswers = $answerOption->option_number;
            }
        }
        
        if ($this->type === 'select_many') {
            $correctAnswersArray = array();
            
            foreach ($this->correctAnswer1 as $correctAnswer) {
                $correctAnswersArray[] = $correctAnswer->option_number;
            }
            
            $this->correctAnswers = implode(', ', $correctAnswersArray);
        }
        
        if ($this->type === 'numeric') {
            $this->answer_number = floatval($this->answer_number);
            $this->precision_percent = sprintf('%.'.self::PRECISION_D.'f', floatval($this->precision_percent));
        }
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            $this->correctAnswers = explode(', ', $this->correctAnswers);
            
            switch ($this->scenario) {
                case 'string':
                    $this->type = self::TYPE_STRING;
                    break;
                case 'numeric':
                    $this->type = self::TYPE_NUMERIC;
                    break;
                default:
                    $type = '';
                    break;
            }
            
            if ($this->scenario === 'select') {
                if (count($this->correctAnswers) > 1) {
                    $this->type = self::TYPE_MANY;
                } else {
                    $this->type = self::TYPE_ONE;
                }
            }
            /*
            if ($this->scenario === 'numeric' && isset($this->precision_percent) == false) {
                $this->precision_percent = self::DEFAULT_PRECISION;
            }
            */
            $this->title = SHelper::purifyHtml($this->title);
            
            return true;
        }
        
        return false;
    }

    protected function afterSave()
    {
        parent::afterSave();
        
        if ($this->scenario === 'select') {
            if (! $this->isNewRecord) {
                AnswerOptions::model()->deleteAll('question_id=:questionId', array(
                    ':questionId' => $this->id
                ));
            }
            
            $aswersModel = new AnswerOptions();
            
            $optionsArray = array();
            
            foreach ($this->answerOptionsArray as $number => $optionText) {
                array_push($optionsArray, array(
                    'question_id' => $this->id,
                    'option_text' => $optionText,
                    'option_number' => $number
                ));
            }
            
            $builder = Yii::app()->db->schema->commandBuilder;
            $command = $builder->createMultipleInsertCommand('answer_options', $optionsArray);
            $command->execute();
            
            $criteria = new CDbCriteria();
            $criteria->addInCondition('option_number', $this->correctAnswers);
            $criteria->addSearchCondition('question_id', $this->id);
            $correctAnswers = AnswerOptions::model()->findAll($criteria);
            
            if (count($correctAnswers) > 1) {
                $manyAnswersId = array();
                foreach ($correctAnswers as $correctAnswer) {
                    array_push($manyAnswersId, array(
                        'question_id' => $this->id,
                        'c_answer' => $correctAnswer->id
                    ));
                }
                $insertCorrectAnswer = $builder->createMultipleInsertCommand('correct_answers', $manyAnswersId);
                $insertCorrectAnswer->execute();
            } else {
                $singleAnswerId = $correctAnswers[0]->id;
                $this->updateByPk($this->id, array(
                    'answer_id' => $singleAnswerId
                ));
            }
        }
        
        $questionImages = new QuestionImage('saveRecord');
        $questionImages->saveTestImages($this, 'title', $this->question_images);
    }

    public function selectCorrectNumber($answerId, $userAnswer)
    {
        /*
         * $answer = $this->find('answer_number BETWEEN :userAnswer - :userAnswer/100*precision_percent AND :userAnswer + :userAnswer/100*precision_percent AND id=:id', array( ':userAnswer' => $userAnswer, ':id' => $answerId ));
         */
        $question = $this->find('id=:id', array(
            ':id' => $answerId
        ));
        
        $userAnswerPrecision = ($userAnswer / 100) * $question->precision_percent;
        
        if (abs($question->answer_number - $userAnswer) <= $userAnswerPrecision) {
            return 'Correct';
        } else {
            return 'Incorrect';
        }
    }

    public function compareTextAnswer($answerId, $userAnswer)
    {
        $question = $this->find('id=:id', array(
            ':id' => $answerId
        ));
        
        $answer = array(
            'correct' => mb_strtolower($question->answer_text),
            'userAnswer' => mb_strtolower($userAnswer)
        );
        
        $formatAnswer = str_replace(' ', '', $answer);
        
        if ($formatAnswer['correct'] === $formatAnswer['userAnswer']) {
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'title' => 'Текст вопроса',
            'type' => 'Type',
            'difficulty' => 'Баллы за правильный ответ',
            'answer_id' => 'AnswerId',
            'answer_text' => 'Правильный ответ',
            'answer_number' => 'Правильный ответ',
            'precision_percent' => 'Precision Percent',
            'answerOptionsArray' => 'Варианты ответов',
            'correctAnswers' => 'Номера правильных ответов'
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
        
        $criteria->compare('id', $this->id);
        $criteria->compare('title', $this->title, true);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('difficulty', $this->difficulty);
        $criteria->compare('answer_id', $this->answer_id);
        $criteria->compare('answer_text', $this->answer_text, true);
        $criteria->compare('answer_number', $this->answer_number, true);
        $criteria->compare('precision_percent', $this->precision_percent);
        
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
     * @return Question the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
