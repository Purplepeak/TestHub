<?php

class LoginForm extends DefaultForm
{
    private $_identity;
    
    public function rules()
    {
        $rules = parent::rules();
        
        array_push($rules, array(
                'rememberMe',
                'boolean'
            ));
        
        return $rules;
    }

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
