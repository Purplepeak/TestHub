<?php

class ConfirmForm extends CFormModel
{

    public $confirmClass;

    public $email;

    public $key;

    public $confirmedAccountId;

    public $temp;

    public $captcha;

    const ERROR_EMAIL_INVALID = 0;

    const ERROR_KEY_INVALID = 1;

    private $_identity;

    public function rules()
    {
        return array(
            // email and password are required
            array(
                'email, key',
                'required',
                'message' => 'Поле не должно быть пустым'
            ),
            // password needs to be authenticated
            array(
                'key',
                'confirmation'
            )
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return array(
            'email' => 'Email',
            'key' => 'Ключ',
            'captcha' => 'Введите код с картинки'
        );
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function confirmation()
    {
        if (! $this->hasErrors()) {
            $email = $this->confirmClass->find('email=:email', array(
                ':email' => $this->email
            ));
            $key = $this->confirmClass->find('`key`=:key', array(
                ':key' => $this->key
            ));
            
            if ($email === null) {
                $this->addError('email', 'Email не зарегестрирован');
            }
            
            if ($key === null) {
                $this->addError('key', 'Неверный или недействительный ключ');
            }
            
            if ($email && $key) {
                $this->temp = $key;
                return true;
            }
        }
    }

    protected function afterValidate()
    {
        parent::afterValidate();
        
        $this->confirmedAccountId = $this->temp->user_id;
    }

    /**
     * Logs in the user using the given email and password in the model.
     * 
     * @return boolean whether login is successful
     */
    public function login()
    {
        if ($this->_identity === null) {
            $this->_identity = new UserIdentity($this->email, $this->password);
            $this->_identity->userClass = $this->userClass;
            $this->_identity->authenticate();
        }
        if ($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            $duration = $this->rememberMe ? Yii::app()->params['rememberMeTime'] : 0;
            Yii::app()->user->login($this->_identity, $duration);
            return true;
        } else
            return false;
    }
}
