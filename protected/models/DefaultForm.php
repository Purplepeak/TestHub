<?php

class DefaultForm extends CFormModel
{

    public $email;

    public $password;

    public $rememberMe;

    public $userClass;

    private $_identity;

    public function rules()
    {
        return array(
            array(
                'email',
                'required',
                'message' => 'Поле не должно быть пустым'
            ),
            array(
                'password',
                'required',
                'message' => 'Поле не должно быть пустым',
                'on' => 'login, newConfirm'
            ),
            array(
                'password',
                'authenticate',
                'on' => 'login, newConfirm'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'rememberMe' => 'Запомнить',
            'password' => 'Пароль',
            'email' => 'e-mail'
        );
    }

    public function authenticate()
    {
        if (! $this->hasErrors()) {
            $this->_identity = new UserIdentity($this->email, $this->password);
            $this->_identity->userClass = $this->userClass;
            
            if ($this->_identity->authenticate() === UserIdentity::ERROR_EMAIL_INVALID) {
                $this->addError('email', 'Указанный e-mail не зарегестрирован.');
            }
            
            if ($this->_identity->authenticate() === UserIdentity::ERROR_PASSWORD_INVALID) {
                $this->addError('password', 'Неверно указан пароль.');
            }
            
            if ($this->_identity->authenticate() === UserIdentity::ERROR_ACTIVATION_INVALID && $this->scenario === 'login') {
                Yii::app()->user->setFlash('confirmError', "Пожалуйста, активируйте свой аккаунт. Подробности активации были высланы на ваш почтовый ящик указанный при регистрации.");
            }
            
            if($this->_identity->authenticate() === UserIdentity::ERROR_NONE && $this->scenario === 'newConfirm') {
                $this->addError('email', '');
                Yii::app()->user->setFlash('newConfirmError', "Вы уже активировали свой аккаунт, нет необходимости подтверждать его снова.");
            }
        }
        
    }
}