<?php

/**
 * This is the model class for table "group".
 *
 * The followings are the available columns in table 'group':
 * @property integer $id
 * @property string $number
 *
 * The followings are the available model relations:
 * @property Users[] $users
 * @property Users[] $users1
 */
class Group extends CActiveRecord
{

    public $group_id = '';

    public $fullname = '';

    public $groupFilter;

    public $numberOfStudents;

    public function tableName()
    {
        return 'group';
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
                'number',
                'required'
            ),
            array(
                'number',
                'length',
                'max' => 45
            ),
            array(
                'fullname',
                'safe'
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, number, teacher_id',
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
            'teacher' => array(
                self::MANY_MANY,
                'Teacher',
                'teacher_group(group_id, teacher_id)'
            ),
            'student' => array(
                self::HAS_MANY,
                'Student',
                'group_id'
            ),
            'tests' => array(
                self::MANY_MANY,
                'Test',
                'group_test(group_id, test_id)'
            ),
        );
    }

    public function behaviors()
    {
        return array(
            'withRelated' => array(
                'class' => 'ext.wr.WithRelatedBehavior'
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
            'number' => 'Группа'
        );
    }

    /**
     * Группы можно указывать через: пробел, запятую, несколько подряд идущих групп
     * через дефис.
     * Задача этого метода - отформатировать строку с группами и превратить ее
     * в массив с номерами отдельных групп, к которым преподаватель имеет отношение.
     */
    public function normalizeGroups($string, $model, $attribute)
    {
        $groupStringReg = '/[0-9-]+[А-Яа-яёЁ\s]*/ui';
        $dashReg = '/(-\s*[0-9]+)\s+([^,])/';
        
        $newString = preg_replace($dashReg, '$1,$2', $string);
        preg_match_all($groupStringReg, $newString, $matches);
        $manyGroups = array();
        $singleGroups = array();
        foreach ($matches[0] as $number) {
            $separator = '-';
            $normalizeString = str_replace(' ', '', $number);
            
            if (mb_strpos($normalizeString, $separator) !== false) {
                $list = explode('-', $normalizeString);
                
                if (ctype_digit($list[1]) and ctype_digit($list[0])) {
                    $numberOfGroups = $list[1] - $list[0];
                }
                
                if ($numberOfGroups < 1) {
                    $model->addError($attribute, 'Некорректо указаны несколько подряд идущих групп');
                }
                
                for ($i = 0; $i <= $numberOfGroups; $i ++) {
                    array_push($manyGroups, $list[0] + $i);
                }
            } else {
                array_push($singleGroups, $normalizeString);
            }
        }
        
        $result = array_merge($singleGroups, $manyGroups);
        
        return $result;
    }

    public function findIncorrectGroups($groupArray, $matches, $model, $modelAttribute, $errorMessage)
    {
        $incorrectGroups = array_diff($groupArray, $matches);
        
        if (count($incorrectGroups) > 7) {
            $incorrectGroups = array_slice($incorrectGroups, 0, 7);
            $incorrectList = implode(', ', $incorrectGroups) . ' и т.д.';
        } else {
            $incorrectList = implode(', ', $incorrectGroups);
        }
        
        if (! empty($incorrectGroups)) {
            $model->addError($modelAttribute, $errorMessage . $incorrectList);
            // var_dump($errorMessage . $incorrectList);
        }
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
            )
        )
        ;
        
        if ($this->groupFilter === 'mygroups' && ! empty(Yii::app()->user->__userData) && Yii::app()->user->__userData['type'] === 'teacher') {
            $criteria->addCondition('teacher.id=:teacherId');
            $criteria->params = array(
                ':teacherId' => Yii::app()->user->id
            );
        }
        
        if (! empty($this->fullname)) {
            $criteria->addSearchCondition('teacher.surname', $this->fullname);
            $criteria->addSearchCondition('teacher.name', $this->fullname, true, 'OR');
        }
        
        $criteria->compare('number', $this->number, true);
        
        $count = $this->count($criteria);
        
        $pages = new CPagination($count);
        $pages->pageSize = 10;
        $pages->applyLimit($criteria);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pages
        ));
    }

    public function getForFilter()
    {
        return CHtml::listData(self::model()->findAll(array(
            'select' => array(
                'id',
                'number'
            )
        )), 'id', 'name');
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     *
     * @param string $className
     *            active record class name.
     * @return Group the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
