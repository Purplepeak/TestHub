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
     * @return string the associated database table name
     */
    public $password2;
    public $accessCode;
    public $groups;
    public $_type = 'teacher';
    public $group_id ='';
    
    public function defaultScope()
    {
        return array(
            'condition' => "type='{$this->_type}'"
        );
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = parent::rules();
        
        array_push($rules, array(
            'groups',
            'required',
            'on' => 'register, oauth',
            'message' => 'Поле не должно быть пустым'
        ), array(
            'groups',
            'match',
            'pattern' => '/^[0-9А-Яа-яёЁ\s,-]+$/ui',
            'message' => 'Необходимо указать номера групп через запятую или побел. Так же вы можете указать несколько подряд идущих номеров: 2450-2455'
        ), array(
            'accessCode',
            'required',
            'on' => 'register, oauth',
            'message' => 'Пожалуйста, введите код доступа для регистрации.'
        ), array(
            'accessCode',
            'compare',
            'compareValue' => 'testaccess',
            'on' => 'register, oauth',
            'message' => 'Введен неверный код доступа.'
        ),
        array(
            'groups',
            'isGroupExist'
        ));
        
        return $rules;
        
    }
    
    public function getGroupsToString(){
        $t = CHtml::listData( $this->groups1, 'id', 'number' );
        return implode(',', $t);
    }
    
    public function search()
    {
        $criteria = new CDbCriteria();
    
        $criteria->with = array(
            'groups1' => array(
                'select' => array('id', 'number')
            ),
        );
        
        $criteria->together = true;
        
        if(isset($this->group_id) && !empty($this->group_id)){
            $criteria->compare('groups1.id', '='.$this->groups1, true);
        }
    
        $criteria->compare('id', $this->id);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('time_registration', $this->time_registration);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('surname', $this->surname, true);
        $criteria->compare('gender', $this->gender, true);
        $criteria->compare('avatar', $this->avatar, true);
        $criteria->compare('group_id', $this->group_id);
    
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
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
   
    public function behaviors(){
    	return array( 'CAdvancedArBehavior' => array(
    			'class' => 'application.extensions.CAdvancedArBehavior'));
    }
    
    public function attributeLabels()
    {
    	$attributeLabels = array(
    			'groups' => 'Группы',
    			'accessCode' => 'Код доступа'
    	);
    	
    	return CMap::mergeArray(parent::attributeLabels(), $attributeLabels);
    }
    
    public function tt() {
        return $this->groups1[0]->number;
    }
    
    /**
     * Группы можно указывать через: пробел, запятую, несколько подряд идущих групп
     * через дефис.
     * Задача этого метода - отформатировать строку с группами и превратить ее
     * в массив с номерами отдельных групп, к которым преподаватель имеет отношение.
     */
    
    public function normalizeGroups($string)
    {
    	$groupStringReg = '/[0-9-]+[А-Яа-яёЁ\s]*/ui';
    	$dashReg = '/(-\s*[0-9]+)\s+([^,])/';
    	
    	$newString = preg_replace($dashReg, '$1,$2', $string);
    	preg_match_all($groupStringReg, $newString, $matches);
    	$manyGroups = array();
    	$singleGroups = array();
    	foreach($matches[0] as $number) {
    		$separator = '-';
    		$normalizeString = str_replace(' ', '', $number);
    			
    			
    		if(mb_strpos($normalizeString, $separator) !== false) {
    			$list = explode('-', $normalizeString);
    	
    			if(ctype_digit($list[1]) and ctype_digit($list[0])) {
    				$numberOfGroups = $list[1]-$list[0];
    			}
    	
    			if($numberOfGroups < 1) {
    				$this->addError('groups', 'Некорректо указаны несколько подряд идущих групп');
    			}
    	
    			for($i = 0; $i <= $numberOfGroups; $i++) {
    				array_push($manyGroups , $list[0] + $i);
    			}
    	
    		} else {
    			array_push($singleGroups, $normalizeString);
    		}
    	}
    	
    	$result = array_merge($singleGroups, $manyGroups );
    	
    	return $result;
    }
    
    /**
     * Проверяет, существуют ли введенные преподавателем группы
     * в базе данных.
     */
    
    public function isGroupExist()
    {
    	if(!empty($this->groups)) {
    		$groupArray = $this->normalizeGroups($this->groups);
    		$teacherGroups = array();
    		
    		$criteria = new CDbCriteria();
    		$criteria->addInCondition('number', $groupArray);
    		$groups =  Group::model()->findAll($criteria);
    		$matches = array();
    		
    		foreach($groups as $object) {
    			array_push($matches, $object->number);
    			array_push($teacherGroups, $object->id);
    		}
    		
    		$incorrectGroups = array_diff($groupArray, $matches);
    		
    		if (count($incorrectGroups) > 7) {
    			$incorrectGroups = array_slice($incorrectGroups, 0, 7);
    			$incorrectList = implode(', ', $incorrectGroups) . ' и т.д.';
    		} else {
    			$incorrectList = implode(', ', $incorrectGroups);
    		}
    		
    		if(!empty($incorrectGroups))
    		{
    			$this->addError('groups', 'Следующих из указанных вами групп не существует: ' . $incorrectList);
    		}
    		
    		$this->groups1 = $teacherGroups;
    	}
    }
   /*
   
    protected function afterSave()
    {
    	parent::afterSave();
    	
    	$criteria = new CDbCriteria();
    	$criteria->addInCondition('number', $this->teacherGroups);
    	$groups = Group::model()->findAll($criteria);
    	
    	$this->groups1 = $groups;
    	$this->withRelated->save(true,array('groups1'));
    }
    */
    
}
