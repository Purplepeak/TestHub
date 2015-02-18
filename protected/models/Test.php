<?php

/**
 * This is the model class for table "test".
 *
 * The followings are the available columns in table 'test':
 * @property integer $id
 * @property string $name
 * @property string $foreword
 * @property integer $minimum_score
 * @property integer $time_limit
 * @property integer $attempts
 * @property integer $create_time
 * @property integer $deadline
 * @property integer $teacher_id
 *
 * The followings are the available model relations:
 * @property Users[] $users
 * @property Users $teacher
 */
class Test extends CActiveRecord
{

    public $testGroups;

    private $testGroupId;

    protected $searchPageSize = 10;

    public $picture;

    public function tableName()
    {
        return 'test';
    }

    public function defaultScope()
    {
        return array(
            'alias' => $this->tableName()
        );
    }

    public function rules()
    {
        return array(
            array(
                'name, foreword, minimum_score, time_limit, attempts, deadline, testGroups',
                'required',
                'message' => 'Поле не должно быть пустым.'
            ),
            array(
                'minimum_score, time_limit, attempts, teacher_id',
                'numerical',
                'integerOnly' => true,
                'message' => 'Значение должно быть в виде числа.'
            ),
            array(
                'name',
                'length',
                'max' => 255,
                'tooLong' => 'Название теста не должно превышать 255 символов.'
            ),
            array(
                'testGroups',
                'match',
                'pattern' => '/^[0-9А-Яа-яёЁ\s,-]+$/ui',
                'message' => 'Необходимо указать номера групп через запятую или побел. Так же вы можете указать несколько подряд идущих номеров: 2450-2455.'
            ),
            array(
                'testGroups',
                'isTeacherGroupExist'
            ),
            array(
                'deadline',
                'validateDateTime'
            ),
            array(
                'id, name, foreword, minimum_score, time_limit, attempts, create_time, deadline, teacher_id',
                'safe',
                'on' => 'search'
            )
        );
    }

    public function relations()
    {
        return array(
            'student' => array(
                self::MANY_MANY,
                'Student',
                'student_test(test_id, student_id)'
            ),
            'teacher' => array(
                self::BELONGS_TO,
                'Teacher',
                'teacher_id'
            ),
            'groups' => array(
                self::MANY_MANY,
                'Group',
                'group_test(test_id, group_id)'
            ),
            'question' => array(
                self::HAS_MANY,
                'Question',
                'test_id'
            ),
            'foreword_images' => array(
                self::HAS_MANY,
                'TestForewordImage',
                'test_id'
            )
        );
    }

    public function behaviors()
    {
        return array(
            'CAdvancedArBehavior' => array(
                'class' => 'application.extensions.CAdvancedArBehavior'
            )
        );
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            
            $criteria = new CDbCriteria();
            $criteria->addInCondition('group_id', $this->testGroupId);
            $students = Student::model()->findAll($criteria);
            
            $testStudents = array();
            
            foreach ($students as $student) {
                array_push($testStudents, $student->id);
            }
            
            if (! $this->isNewRecord) {
                StudentTest::model()->deleteAll('test_id=:testId AND student_id NOT IN(:studentId)', array(
                    ':testId' => 11,
                    ':studentId' => implode(', ', $testStudents)
                ));
            }
            
            $this->student = $testStudents;
            
            $serverTime = strtotime(Yii::app()->params["dater"]->serverDatetime());
            $clientTime = strtotime(Yii::app()->params["dater"]->isoDatetime());
            $deadlineTime = strtotime($this->deadline);
            
            $resultTime = $deadlineTime;
            
            if ($clientTime < $serverTime) {
                $resultTime = $deadlineTime + ($serverTime - $clientTime);
            }
            
            if ($clientTime > $serverTime) {
                $resultTime = $deadlineTime - ($clientTime - $serverTime);
            }
            
            $this->create_time = date('Y-m-d H:i:s', time());
            $this->deadline = date('Y-m-d H:i', $resultTime);
            
            $this->foreword = SHelper::purifyHtml($this->foreword);
            
            return true;
        }
        
        return false;
    }
    
    public function beforeDelete()
    {
        $file = Yii::app()->file->set(Yii::getPathOfAlias('forewordImages') .'/'. $this->id, true);
    
        if($file->exists) {
            $file->delete();
        }
    
        return parent::beforeDelete();
    }

    protected function afterSave()
    {
        parent::afterSave();
        
        StudentTest::model()->updateAll(array(
            'attempts' => $this->attempts,
            'deadline' => $this->deadline
        ), 'test_id=:testId', array(
            'testId' => $this->id
        ));
        
        $testImages = new TestForewordImage('saveRecord');
        $testImages->saveTestImages($this, 'foreword', $this->foreword_images);
    }

    protected function afterFind()
    {
        parent::afterFind();
        
        if ($this->groups) {
            $groupNumbers = array();
            
            foreach ($this->groups as $group) {
                $groupNumbers[] = $group->number;
            }
            
            asort($groupNumbers);
            
            $formattedGroupNumbers = array_values($groupNumbers);
            
            $resultArray = array();
            $currentGroup = $formattedGroupNumbers[0];
            $index = 0;
            
            foreach ($formattedGroupNumbers as $groupNumber) {
                
                if ($currentGroup + 1 == $groupNumber) {
                    $resultArray[$index][] = $groupNumber;
                    $currentGroup ++;
                } else {
                    $currentGroup = $groupNumber;
                    $index ++;
                    $resultArray[$index][] = $groupNumber;
                }
            }
            
            foreach ($resultArray as $key => $subArray) {
                if (count($subArray) > 2) {
                    end($subArray);
                    $resultArray[$key] = "{$subArray[0]}-{$subArray[key($subArray)]}";
                } else {
                    $resultArray[$key] = implode(', ', $subArray);
                }
            }
            
            $this->testGroups = implode(', ', $resultArray);
        }
        
        $this->deadline = Yii::app()->params["dataHandler"]->handleDataTimezone($this->deadline . '[Y-m-d H:i]');
    }
    
    /**
     * Метод возвращает массив из ID вопросов теста на которые студент уже ответил
     */
    
    public function getStudentAnswersByQuestionsId($questionNumberIdPair)
    {
        $studentAnswersCriteria = new CDbCriteria();
        $studentAnswersCriteria->addInCondition('question_id', $questionNumberIdPair);
        $studentAnswers = StudentAnswer::model()->findAll($studentAnswersCriteria);
        
        $studentAnswersQuestionId = array();
        
        foreach($studentAnswers as $studentAnswer) {
            $studentAnswersQuestionId[] = $studentAnswer->question_id;
        }
        
        return $studentAnswersQuestionId;
    }

    public function validateDateTime()
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}\s*\d{2}:\d{2}$/', $this->deadline, $match)) {
            $this->addError('deadline', 'Убедитесь, что вы указали дату в правильном формате. Пример: ' . date('Y-m-d H:i') . '.');
        } else {
            if (strtotime($match[0]) < time()) {
                $this->addError('deadline', 'Дата указанная вами уже истекла или указанный год является слишком далеким будушим.');
            }
        }
    }
    
    /**
     * Метод проверяет существует ли группа и преподает ли в ней пользователь
     */

    public function isTeacherGroupExist()
    {
        if ($this->testGroups) {
            $testGroups = Group::model()->normalizeGroups($this->testGroups, $this, 'testGroups');
            
            $teacher = Teacher::model()->findByPk($this->teacher_id);
            
            $teacherGroupsNumbers = array();
            $teacherGroupsId = array();
            
            foreach ($teacher->groups1 as $group) {
                $teacherGroupsNumbers[] = $group->number;
                $teacherGroupsId[] = $group->id;
            }
            
            Group::model()->findIncorrectGroups($testGroups, $teacherGroupsNumbers, $this, 'testGroups', 'Вы не преподаете следующим группам : ');
            
            $criteria = new CDbCriteria();
            $criteria->addInCondition('number', $testGroups);
            $groups = Group::model()->findAll($criteria);
            
            $testGroupsId = array();
            
            foreach ($groups as $group) {
                $testGroupsId[] = $group->id;
            }
            
            $this->testGroupId = $testGroupsId;
            $this->groups = $testGroupsId;
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
            'name' => 'Название',
            'foreword' => 'Предисловие',
            'minimum_score' => 'Минимальный балл',
            'time_limit' => 'Время на прохождение теста (мин)',
            'attempts' => 'Попыток',
            'create_time' => 'Create Time',
            'deadline' => 'Срок сдачи',
            'teacher_id' => 'Teacher',
            'testGroups' => 'Группы'
        );
    }

    public function searchTeacherTests()
    {
        $criteria = new CDbCriteria();
        
        $sort = new CSort();
        $sort->defaultOrder = array(
            'name' => CSort::SORT_ASC
        );
        $sort->attributes = array(
            'name' => array(
                'asc' => $this->getTableAlias() . '.name',
                'desc' => $this->getTableAlias() . '.name DESC'
            ),
            'deadline' => array(
                'asc' => $this->getTableAlias() . '.deadline',
                'desc' => $this->getTableAlias() . '.deadline DESC'
            )
        );
        
        $criteria->addInCondition('teacher_id', array(
            Yii::app()->user->id
        ));
        
        $criteria->with = array(
            'teacher' => array(
                'select' => array(
                    '*'
                ),
                'together' => true
            ),
            'groups' => array(
                'select' => array(
                    '*'
                ),
                'together' => true
            )
        );
        
        if ($this->testGroups) {
            $criteria->compare('groups.number', '=' . $this->testGroups, true);
        }
        
        $criteria->compare($this->getTableAlias() . '.name', $this->name, true);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'sort' => $sort,
            'pagination' => array(
                'pageSize' => 80 // $this->searchPageSize
                        )
        ));
    }
    
    /**
     * Метод вернет false, если время на выполнение теста вышло
     */
    
    public function checkTestTimeLimit($startTime, $timeLimit)
    {
        if (time() >= ($startTime + $timeLimit)) {
            return false;
        }
    
        return true;
    }

    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria();
        
        $criteria->compare('id', $this->id);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('foreword', $this->foreword, true);
        $criteria->compare('minimum_score', $this->minimum_score);
        $criteria->compare('time_limit', $this->time_limit);
        $criteria->compare('attempts', $this->attempts);
        $criteria->compare('create_time', $this->create_time);
        $criteria->compare('deadline', $this->deadline);
        $criteria->compare('teacher_id', $this->teacher_id);
        /*
         * <ul>
    <?php foreach($questions as $key=>$question):?>
      <li><a class="fa fa-angle-right" href="#Question<?= $key+1 ?>">Вопрос №<?= $key+1 ?></a></li>
    <?php endforeach;?>
  </ul>
         * 
         * */
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
