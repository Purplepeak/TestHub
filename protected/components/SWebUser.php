<?php

/**
 * Унаследованный класс, который позволит нам получать более подробную
 * информацию о пользователе.
 *
 */

class SWebUser extends CWebUser
{

    public function __get($name)
    {
        if ($this->hasState($name)) {
            $user = $this->getState($name, array());
            if (isset($user)) {
                return $user;
            }
        }
        
        return parent::__get($name);
    }

    public function login($identity, $duration)
    {
        $this->setState('__userData', $identity->getUserData());
        parent::login($identity, $duration);
    }
}