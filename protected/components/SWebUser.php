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
        $userData = $identity->getUserData();
        
        $this->setState('__userData', $userData);
        
        if(!isset($userData['avatar'])) {
            $mainAvatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultMainAvatar'];
            $menuAvatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultMenuAvatar'];
        } else {
            $avatarDir = Yii::app()->request->baseUrl . Yii::app()->params['avatarRelativePath']. '/' .$userData['id'];
            $avatarCropper = new SAvatarCropper($avatarDir);
            $mainAvatar = $avatarCropper->link($userData['cropped_avatar'], Yii::app()->params['mainAvatarSize']['width'], Yii::app()->params['mainAvatarSize']['height'], 'crop');
            $menuAvatar = $avatarCropper->link($userData['cropped_avatar'], Yii::app()->params['menuAvatarSize']['width'], Yii::app()->params['menuAvatarSize']['height'], 'crop');
        }
        
        $this->setState('__userMenuAvatar', $menuAvatar);
        $this->setState('__userMainAvatar', $mainAvatar);
        
        parent::login($identity, $duration);
    }
}