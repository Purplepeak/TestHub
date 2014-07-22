<?php

/**
 * This is the model class for table "test".
 *
 * The followings are the available columns in table 'test':
 * @property integer $id
 * @property string $name
 * @property string $foreword
 * @property string $rules
 * @property integer $minimum_score
 * @property integer $time_limit
 * @property integer $attempts
 * @property string $difficulty
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
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'test';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, foreword, rules, minimum_score, time_limit, attempts, difficulty, create_time, deadline, teacher_id', 'required'),
			array('minimum_score, time_limit, attempts, create_time, deadline, teacher_id', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('difficulty', 'length', 'max'=>20),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, foreword, rules, minimum_score, time_limit, attempts, difficulty, create_time, deadline, teacher_id', 'safe', 'on'=>'search'),
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
			'users' => array(self::MANY_MANY, 'Student', 'student_test(test_id, student_id)'),
			'teacher' => array(self::BELONGS_TO, 'Teacher', 'teacher_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'foreword' => 'Foreword',
			'rules' => 'Rules',
			'minimum_score' => 'Minimum Score',
			'time_limit' => 'Time Limit',
			'attempts' => 'Attempts',
			'difficulty' => 'Difficulty',
			'create_time' => 'Create Time',
			'deadline' => 'Deadline',
			'teacher_id' => 'Teacher',
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
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('foreword',$this->foreword,true);
		$criteria->compare('rules',$this->rules,true);
		$criteria->compare('minimum_score',$this->minimum_score);
		$criteria->compare('time_limit',$this->time_limit);
		$criteria->compare('attempts',$this->attempts);
		$criteria->compare('difficulty',$this->difficulty,true);
		$criteria->compare('create_time',$this->create_time);
		$criteria->compare('deadline',$this->deadline);
		$criteria->compare('teacher_id',$this->teacher_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Test the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
