<?php

/**
 * This is the model class for table "teacher".
 *
 * The followings are the available columns in table 'teacher':
 * @property integer $id
 * @property string $password
 * @property string $email
 * @property string $time_registration
 * @property string $name
 * @property string $surname
 * @property string $groups
 * @property string $gender
 * @property string $avatar
 *
 * The followings are the available model relations:
 * @property Group[] $groups0
 * @property Test[] $tests
 */
class Teacher extends Users
{

    /**
     *
     * @return string the associated database table name
     */
    public $password2;

    public $accessCode;

    public $groups;

    public $_type = 'teacher';

    public $groupNumber = '';

    protected $searchPageSize = 80;
    
    public $testName;

    public function defaultScope()
    {
        return array(
            'condition' => "teacher.type='{$this->_type}'",
            'alias' => 'teacher',
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
            'groups',
            'required',
            'on' => 'register, oauth',
            'message' => 'Поле не должно быть пустым.'
        ), array(
            'groups',
            'match',
            'pattern' => '/^[0-9А-Яа-яёЁ\s,-]+$/ui',
            'message' => 'Необходимо указать номера групп через запятую или побел. Вы также можете указать несколько подряд идущих номеров: 2450-2455.'
        ), array(
            'accessCode',
            'required',
            'on' => 'register, oauth',
            'message' => 'Пожалуйста, введите код доступа для регистрации.'
        ), array(
            'accessCode',
            'compare',
            'compareValue' => Yii::app()->params['teacherAccessCode'],
            'on' => 'register, oauth',
            'message' => 'Введен неверный код доступа.'
        ), array(
            'groups',
            'isGroupExist'
        ), array(
            'fullname',
            'safe',
            'on' => 'search'
        ));
        
        return $rules;
    }

    public function relations()
    {
        return array(
            'groups1' => array(
                self::MANY_MANY,
                'Group',
                'teacher_group(teacher_id, group_id)'
            ),
            'tests1' => array(
                self::HAS_MANY,
                'Test',
                'teacher_id'
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

    public function attributeLabels()
    {
        $attributeLabels = array(
            'groups' => 'Группы',
            'accessCode' => 'Код доступа'
        );
        
        return CMap::mergeArray(parent::attributeLabels(), $attributeLabels);
    }

    /**
     * Проверяет, существуют ли введенные преподавателем группы
     * в базе данных.
     */
    public function isGroupExist()
    {
        if (! empty($this->groups)) {
            $groupArray = Group::model()->normalizeGroups($this->groups, $this, 'groups');
            $teacherGroups = array();
            
            $criteria = new CDbCriteria();
            $criteria->addInCondition('number', $groupArray);
            $groups = Group::model()->findAll($criteria);
            $matches = array();
            
            foreach ($groups as $object) {
                array_push($matches, $object->number);
                array_push($teacherGroups, $object->id);
            }
            
            Group::model()->findIncorrectGroups($groupArray, $matches, $this, 'groups', 'Следующих из указанных вами групп не существует: ');
            
            $this->groups1 = $teacherGroups;
        }
    }

    public function addSearchConditions($criteria)
    {
        $criteria->with = array(
            'groups1' => array(
                'select' => array(
                    'id',
                    'number'
                ),
                'together' => true
            )
        );
        
        if (isset($this->groupNumber) && ! empty($this->groupNumber)) {
            $criteria->compare('groups1.number', '=' . $this->groupNumber, true);
        }
    }

    public function searchTests()
    {
        $criteria = new CDbCriteria();
        
        $criteria->with = array(
            'tests1' => array(
                'select' => array(
                    'id',
                    'name',
                    'deadline'
                ),
                'together' => true
            ),
        );
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => $this->searchPageSize
            )
        ));
    }

    public static function groupsToString($groups)
    {
        $groupLinks = array();
        foreach ($groups as $group) {
            $groupLinks[] = GxHtml::link(GxHtml::encode($group->number), array(
                'student/list',
                'id' => GxActiveRecord::extractPkValue($group, true)
            ));
        }
        
        if (empty($groups)) {
            return 'N/A';
        }
        
        return implode(', ', $groupLinks);
    }
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
