<?php

/**
 * This is the model class for table "admin".
 *
 * The followings are the available columns in table 'admin':
 * @property integer $id
 * @property string $password
 */
class Admin extends Users
{
	public function defaultScope(){
		return array(
				'condition'=>"type='admin'",
		);
	}
	
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('password',$this->password,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public static function model($className = __CLASS__)
	{
	    return parent::model($className);
	}
}
