<?php

class AccountInteractionForm extends DefaultForm
{

    public $user;

    private $_identity;

    public function rules()
    {
        $rules = parent::rules();
        
        array_push($rules, array(
            'email',
            'findUser',
            'on' => 'passRestore'
        ));
        
        return $rules;
    }

    public function findUser()
    {
        $this->_identity = new UserIdentity($this->email);
        $this->_identity->userClass = $this->userClass;
        
        $user = $this->_identity->getUser();
        $this->user = $user;
        
        if ($user === null) {
            $this->addError('email', 'Указанный почтовый адрес не зарегестрирован.');
        }
    }
}
