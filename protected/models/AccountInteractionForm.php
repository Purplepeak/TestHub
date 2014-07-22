<?php

class AccountInteractionForm extends CFormModel
{

    public $email;

    public $userClass;

    public $user;

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
                'email',
                'findUser',
                'on' => 'passRestore'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'email' => 'e-mail'
        );
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
