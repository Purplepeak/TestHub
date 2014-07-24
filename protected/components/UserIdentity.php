<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{

    /**
     * Authenticates a user.
     * The example implementation makes sure if the email and password
     * are both 'demo'.
     * In practical applications, this should be changed to authenticate
     * against some persistent user identity storage (e.g. database).
     * 
     * @return boolean whether authentication succeeds.
     */
    private $_id;

    public $userClass;

    public $email;

    public $ouathProvider;

    public $oauthId;

    const ERROR_EMAIL_INVALID = 3;

    const ERROR_ACTIVATION_INVALID = 4;

    public function __construct($email = null, $password = null)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function authenticate()
    {
        $user = $this->getUser();
        
        if ($user === null) {
            return $this->errorCode = self::ERROR_EMAIL_INVALID;
        } else 
            if (! $user->validatePassword($this->password)) {
                return $this->errorCode = self::ERROR_PASSWORD_INVALID;
            }
        
        if ($user->active == 0) {
            return $this->errorCode = self::ERROR_ACTIVATION_INVALID;
        }
        
        $this->_id = $user->id;
        $this->username = $user->name;
        return $this->errorCode = self::ERROR_NONE;
    }

    public function getUser()
    {
        $email = strtolower($this->email);
        $user = $this->userClass->find('email=?', array(
            $email
        ));
        
        return $user;
    }

    public function getId()
    {
        return $this->_id;
    }

    public static function forceLogin($model)
    {
        $identity = new self(null, $model->password);
        
        $identity->_id = $model->id;
        $identity->username = $model->name;
        
        return $identity;
    }
    
    /**
     * Проверяет наличие социального пользователя в базе
     */

    public function socialAuthenticate($ouathProvider, $oauthId)
    {
        $user = $this->userClass->find('social_user_id=:social_user_id AND provider=:provider', array(
            ':provider' => $ouathProvider,
            ':social_user_id' => $oauthId
        ));
        
        if ($user === null) {
            $this->errorCode = self::ERROR_UNKNOWN_IDENTITY;
        } else {
            $this->_id = $user->user_id;
            $this->username = $user->user->name;
            $this->errorCode = self::ERROR_NONE;
        }
    }
}