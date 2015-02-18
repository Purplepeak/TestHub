<?php

/**
 * Унаследованный класс, который позволит нам получать более подробную
 * информацию о пользователе.
 *
 */
class SWebUser extends CWebUser
{

    public $allowAutoLogin = true;
    
    private $_model = null;

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

    public function login($identity, $duration=0)
    {
        $userData = $identity->getUserData();
        
        if (! isset($userData['avatar'])) {
            $mainAvatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultMainAvatar'];
            $menuAvatar = Yii::app()->request->baseUrl . Yii::app()->params['defaultMenuAvatar'];
        } else {
            $avatarDir = Yii::app()->request->baseUrl . Yii::app()->params['avatarRelativePath'] . '/' . $userData['id'];
            $avatarCropper = new SAvatarCropper($avatarDir);
            $mainAvatar = $avatarCropper->link($userData['cropped_avatar'], Yii::app()->params['mainAvatarSize']['width'], Yii::app()->params['mainAvatarSize']['height'], 'crop');
            $menuAvatar = $avatarCropper->link($userData['cropped_avatar'], Yii::app()->params['menuAvatarSize']['width'], Yii::app()->params['menuAvatarSize']['height'], 'crop');
        }
        
        $states = array(
            '__userData' => $userData,
            '__userMenuAvatar' => $menuAvatar,
            '__userMainAvatar' => $mainAvatar
        );
        
        $identity->setPersistentStates($states);
        
        parent::login($identity, $duration);
    }
    
    public function getRole() {
        if($user = $this->getModel()){
            return $user->type;
        }
    }
    
    public function getModel(){
        if (!$this->isGuest && $this->_model === null){
            $this->_model = Users::model()->findByPk($this->id, array('select' => 'type'));
        }
        return $this->_model;
    }
}