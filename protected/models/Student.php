<?php

/**
 * This is the model class for table "student".
 *
 * The followings are the available columns in table 'student':
 * @property integer $id
 * @property string $password
 * @property string $email
 * @property string $time_registration
 * @property string $name
 * @property string $surname
 * @property integer $group
 * @property string $gender
 * @property string $avatar
 * @property integer $group_id
 *
 * The followings are the available model relations:
 * @property Group $group0
 * @property Test[] $tests
 */
class Student extends Users
{

    /**
     *
     * @return string the associated database table name
     */
    public $password2;

    public $group;

    public $_type = 'student';

    protected $searchPageSize = 20;

    public function defaultScope()
    {
        return array(
            'condition' => "student.type='{$this->_type}'",
            'alias' => 'student'
        );
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = parent::rules();
        
        array_push($rules, array(
            'group',
            'required',
            'on' => 'register, oauth',
            'message' => 'Поле не должно быть пустым'
        ), 

        array(
            'group',
            'match',
            'pattern' => '/^[0-9А-Яа-яёЁ]+$/ui',
            'message' => 'Номер группы указан неверно'
        ), array(
            'group',
            'isGroupExist'
        ));
        
        return $rules;
    }

    public function attributeLabels()
    {
        $studentLabels = parent::attributeLabels();
        $studentLabels['group'] = 'Группа';
        
        return $studentLabels;
    }

    /**
     *
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'student_group' => array(
                self::BELONGS_TO,
                'Group',
                'group_id'
            ),
            'tests' => array(
                self::MANY_MANY,
                'Test',
                'student_test(student_id, test_id)'
            )
        );
    }

    /**
     * В соответствии с указанной группой, студенту присваивается
     * id группы вычисленное методом $this->studentGroupId($number);
     */
    protected function afterSave()
    {
        parent::afterSave();
        if($this->isNewRecord) {
            $groupId = $this->studentGroupId($this->group);
            $this->updateByPk($this->id, array(
                'group_id' => $groupId
            ));
        }
    }

    public function studentGroupId($number)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = 'number=:number';
        $criteria->params = array(
            ':number' => $number
        );
        $group = Group::model()->find($criteria);
        
        return $group->id;
    }

    public function groupToString()
    {}

    /**
     * Проверяет, существует ли введенная студентом группа
     * в базе данных.
     */
    public function isGroupExist()
    {
        if ($this->group) {
            $normalizeGroup = mb_strtolower($this->group);
            $group = Group::model()->findByAttributes(array(
                'number' => $normalizeGroup
            ));
            
            if ($group === null) {
                $this->addError('group', 'Данной группы нет в списке');
            }
        }
    }

    public function addSearchConditions($criteria)
    {
        if (isset($this->searchGroup)) {
            $criteria->addInCondition('group_id', array(
                $this->searchGroup
            ));
        }
    }

    public function assignTests()
    {
        $group = $this->student_group;
        
        $test = Test::model();
        $criteria = new CDbCriteria();
        $criteria->addCondition($test->getTableAlias() . '.deadline > NOW()');
        
        $criteria->with = array(
            'groups' => array(
                'select' => array(
                    'groups.id'
                ),
                'together' => true
            )
        );
        
        $criteria->addCondition('groups.id=:groupId');
        
        $criteria->params = array(
            ':groupId' => $group->id
        );
        
        if($tests = $test->findAll($criteria)) {
            $studentTestData = array();
            
            foreach ($tests as $test) {
                $studentTestData[] = array(
                    'attempts' => $test->attempts,
                    'deadline' => $test->deadline,
                    'test_id' => $test->id,
                    'student_id' => $this->id
                );
            }
            
            $builder = Yii::app()->db->schema->commandBuilder;
            $command = $builder->createMultipleInsertCommand('student_test', $studentTestData);
            $command->execute();
        }
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
