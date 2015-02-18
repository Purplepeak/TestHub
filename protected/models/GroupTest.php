<?php

class GroupTest extends CActiveRecord
{
	public function tableName()
	{
		return 'group_test';
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
			array('test_id, group_id', 'required'),
			array('test_id, group_id', 'numerical', 'integerOnly'=>true),
			array('test_id, group_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'test_id' => 'Test',
			'group_id' => 'Group',
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('test_id',$this->test_id);
		$criteria->compare('group_id',$this->group_id);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
