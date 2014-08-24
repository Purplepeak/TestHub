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
	public $group_id ='';
	public $fullname ='';
    
	public function tableName()
	{
		return 'group';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number', 'required'),
			array('number', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, number, teacher_id', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'teacher' => array(self::MANY_MANY, 'Teacher', 'teacher_group(group_id, teacher_id)'),
			'student' => array(self::HAS_MANY, 'Student', 'group_id'),
		);
	}
	
	public function behaviors()
	{
		return array(
				'withRelated'=>array(
						'class'=>'ext.wr.WithRelatedBehavior',
				),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'number' => 'Группа',
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
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		$criteria=new CDbCriteria;
		
		$criteria->with = array(
		    'teacher' => array(
		        'select' => array('id', 'name', 'surname', 'type'),
		        'together' => true,
		        'condition'=>'active=true',
		    ),
		);
		
		if(!empty($this->fullname)){
		    $criteria->addSearchCondition('teacher.surname', $this->fullname);
		    $criteria->addSearchCondition('teacher.name', $this->fullname, true, 'OR');
		}

		$criteria->compare('number',$this->number,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		    'pagination'=>array(
		        'pageSize'=>20,
		    ),
		));
	}
	
	public function getForFilter(){
	    return CHtml::listData(
	        self::model()->findAll(array(
	            'select' => array('id', 'number')
	        )), 'id', 'name'
	    );
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Group the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
