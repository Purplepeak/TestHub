<?php

/**
 * This is the model class for table "student_test".
 *
 * The followings are the available columns in table 'student_test':
 * @property integer $id
 * @property integer $attempts
 * @property string $deadline
 * @property integer $result
 * @property integer $test_id
 * @property integer $student_id
 *
 * The followings are the available model relations:
 * @property Question[] $questions
 * @property Test $test
 * @property Users $student
 */
class StudentTest extends CActiveRecord
{

    public $testName;

    public $testTimeLimit;

    public $testStatus;
    
    public function tableName()
    {
        return 'student_test';
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'test_id, student_id',
                'required'
            ),
            array(
                'attempts, result, test_id, student_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'deadline',
                'safe'
            ),
            array(
                'id, attempts, deadline, result, test_id, student_id',
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
            'questions' => array(
                self::MANY_MANY,
                'Question',
                'student_answer(test_result, question_id)'
            ),
            'test' => array(
                self::BELONGS_TO,
                'Test',
                'test_id'
            ),
            'student' => array(
                self::BELONGS_TO,
                'Users',
                'student_id'
            ),
            'studentAnswers' => array(
                self::HAS_MANY,
                'StudentAnswer',
                'test_result'
            )
        );
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'attempts' => 'Попыток для сдачи',
            'deadline' => 'Крайний срок сдачи',
            'result' => 'Результат',
            'test_id' => 'Test',
            'student_id' => 'Student'
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
        $criteria->compare('attempts', $this->attempts);
        $criteria->compare('deadline', $this->deadline, true);
        $criteria->compare('result', $this->result);
        $criteria->compare('test_id', $this->test_id);
        $criteria->compare('student_id', $this->student_id);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    public function searchMyTests()
    {
        $criteria = new CDbCriteria();
        
        $criteria->addCondition($this->getTableAlias() . '.student_id=:studentId AND ' . $this->getTableAlias() . '.deadline > NOW()');
        $criteria->params = array(
            ':studentId' => Yii::app()->user->id
        );
        
        $criteria->with = array(
            'test' => array(
                'select' => array(
                    'test.id',
                    'test.name',
                    'test.time_limit',
                    'test.minimum_score'
                ),
                'together' => true
            )
        );
        
        switch($this->testStatus) {
        	case 'notpassed':
        	    $condition = $this->getTableAlias() . '.result IS NULL OR ' . $this->getTableAlias() . '.result < test.minimum_score AND ' .$this->getTableAlias().'.attempts > 0';
        	    break;
        	case 'passed':
        	    $condition = $this->getTableAlias() . '.result > test.minimum_score';
        	    break;
        	case 'failed':
        	    $condition = $this->getTableAlias() . '.result < test.minimum_score AND ' .$this->getTableAlias().'.attempts = 0';
        	    break;
        	default:
        	    throw new CHttpException(404);
        	    break;  
        }
        
        $criteria->addCondition($condition);
        $criteria->compare($this->getTableAlias() . '.deadline', $this->deadline, true);
        $criteria->compare($this->getTableAlias() . '.attempts', $this->attempts, true);
        $criteria->compare($this->getTableAlias() . '.attempts', $this->attempts, true);
        $criteria->compare('test.time_limit', $this->testTimeLimit, true);
        $criteria->compare('test.name', $this->testName, true);
        
        $count = $this->count($criteria);
        
        $pages = new CPagination($count);
        $pages->pageSize = 15;
        $pages->applyLimit($criteria);
        
        $sort = new CSort();
        $sort->defaultOrder = array(
            'deadline' => CSort::SORT_ASC
        );
        $sort->attributes = array(
            'deadline' => array(
                'asc' => $this->getTableAlias() . '.deadline',
                'desc' => $this->getTableAlias() . '.deadline DESC'
            ),
            'attempts' => array(
                'asc' => $this->getTableAlias() . '.attempts',
                'desc' => $this->getTableAlias() . '.attempts DESC'
            ),
            'testName' => array(
                'asc' => 'test.name',
                'desc' => 'test.name DESC'
            ),
            'testTimeLimit' => array(
                'asc' => 'test.time_limit',
                'desc' => 'test.time_limit DESC'
            )
        );
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pages,
            'sort' => $sort
        ));
    }

    public function checkTestInProgress()
    {
        if (! empty($this->start_time) && empty($this->end_time) && empty($this->result) && Test::model()->checkTestTimeLimit(strtotime($this->start_time), $this->test->time_limit * 60)) {
            return true;
        }
        
        return false;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className
     *            active record class name.
     * @return StudentTest the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
