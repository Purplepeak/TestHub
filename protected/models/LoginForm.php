<?php

class LoginForm extends CFormModel
{
	public $email;
	public $password;
	public $rememberMe;
	public $userClass;

	private $_identity;
	
	public function rules()
	{
		return array(
			array('email, password', 'required', 'message' => 'Поле не должно быть пустым'),
			array('rememberMe', 'boolean'),
			array('password', 'authenticate')
		);
	}

	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'Запомнить',
			'password' => 'Пароль',
			'email' => 'e-mail'
		);
	}

	public function authenticate()
	{
		if(!$this->hasErrors())
		{
			$this->_identity=new UserIdentity($this->email,$this->password);
			$this->_identity->userClass = $this->userClass;
			
			if($this->_identity->authenticate() === UserIdentity::ERROR_EMAIL_INVALID) {
				$this->addError('email','Указанный e-mail не зарегестрирован.');
			}
			
			if($this->_identity->authenticate() === UserIdentity::ERROR_PASSWORD_INVALID) {
				$this->addError('password','Неверно указан пароль.');
			}
			
			if($this->_identity->authenticate() === UserIdentity::ERROR_ACTIVATION_INVALID) {
				Yii::app()->user->setFlash('error', "Пожалуйста, активируйте свой аккаунт. Подробности активации были высланы на ваш почтовый ящик указанный при регистрации.");
			}
		}
	}

	public function login()
	{
		if($this->_identity===null)
		{
			$this->_identity=new UserIdentity($this->email,$this->password);
			$this->_identity->userClass = $this->userClass;
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? Yii::app()->params['rememberMeTime'] : 0; 
			Yii::app()->user->login($this->_identity,$duration);
			return true;
		}
		else
			return false;
	}
}
