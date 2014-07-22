<?php

class AccountInteraction extends CActiveRecord
{
	public $newPassword;
	
	public function tableName()
	{
		return 'account_interaction';
	}
	
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('user_name, user_id, key, email, scenario', 'required'),
			array('scenario', 'in', 'range' => array('confirm', 'restore')),
			array('user_id, create_time', 'numerical', 'integerOnly'=>true),
			array('create_time', 'safe'),
			array('user_name', 'length', 'max'=>30),
			array('key', 'length', 'max'=>128),
			array('email', 'length', 'max'=>250),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, user_name, user_id, key, email', 'safe', 'on'=>'search'),
		);
	}
	
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}
	
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'user_id' => 'User',
			'key' => 'Key',
			'email' => 'Email',
		);
	}
	
	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('user_name',$this->user_name);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('key',$this->key,true);
		$criteria->compare('email',$this->email,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	
	public function formatEmail($format, $template)
	{
		$root = $_SERVER['DOCUMENT_ROOT'] .Yii::app()->request->baseUrl. '/templates';
	
		$template = file_get_contents($root .$template. $format);
		
		$patterns = array('/{EMAIL}/', '/{KEY}/', '/{SITEPATH}/', '/{NAME}/', '/{BASE_URL}/', '/{PASSWORD}/');
		$replacements = array($this->email, $this->key, Yii::app()->request->hostInfo, $this->user_name, Yii::app()->request->baseUrl, $this->newPassword);
		
		$template = preg_replace($patterns, $replacements, $template);
		
		
		return $template;
	}
	
	public function sendEmail($title, $template) 
	{
		$bodyHtml = $this->formatEmail('html', $template);
		$bodyTxt = $this->formatEmail('txt', $template);
		
		//setup the mailer
		$transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587,'tls');
		$transport->setUsername(Yii::app()->params['siteEmail']['email']);
		$transport->setPassword(Yii::app()->params['siteEmail']['password']);
		
		$mailer = Swift_Mailer::newInstance($transport);
		$message = Swift_Message::newInstance();
		$message ->setSubject($title);
		$message ->setFrom(array(Yii::app()->params['siteEmail']['email'] => 'TestHub'));
		$message ->setTo(array($this->email => $this->user_name));
		 
		$message ->setBody($bodyTxt);
		$message ->addPart($bodyHtml, 'text/html');
		 
		$result = $mailer->send($message);
		 
		return $result;
	}
	
	public function saveAndSend($model, $scenario)
	{
		$this->user_id = $model->id;
		$this->user_name = $model->name;
		$this->email = $model->email;
		$this->scenario = $scenario;
		$this->key = md5($this->email . time());
		
		if ($this->validate()) {
			$exist = $this->find('email=:email AND scenario=:scenario', array(
					':email' => $this->email,
					':scenario' => $this->scenario,
			));
			
			if($exist != null) {
				$this->deleteByPk($exist->id);
			}
			
			$this->save(false);
			
			if($scenario == 'confirm') {
				$title = 'Добро пожаловать на TestHub';
				$template = '/signup_template.';
			} elseif($scenario == 'restore') {
				$title = "Здравствуйте, {$this->user_name}";
				$template = '/restore_template.';
			}
			
			$this->sendEmail($title, $template);
		}
	}
	
	public function findByEmailKey($email, $key, $scenario)
	{
		$account = $this->find('email=:email AND `key`=:key AND scenario=:scenario', array(
				':email' => $email,
				':key' => $key,
				':scenario' => $scenario
		));
		
		return $account;
	}
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
