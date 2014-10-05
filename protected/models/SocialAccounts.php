<?php

class SocialAccounts extends CActiveRecord
{
    public function tableName()
    {
        return 'social_accounts';
    }
    
    public function rules()
    {
        return array(
            array(
                'provider, social_user_id, info, url',
                'required'
            ),
        		/*
            array(
                'social_user_id',
                'unique',
                'criteria' => array(
                    'condition' => '`provider`=:provider',
                    'params' => array(
                        ':provider' => $this->provider
                    )
                ),
            	'message' => 'Поле не должно быть пустым'
            ),
            */
            array(
                'user_id',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'provider',
                'length',
                'max' => 8
            ),
            array(
                'social_user_id',
                'length',
                'max' => 20
            ),
            array(
                'id, provider, social_user_id, info, user_id',
                'safe',
                'on' => 'search'
            )
        );
    }
    
    public function relations()
    {
        return array(
            'user' => array(
                self::BELONGS_TO,
                'Users',
                'user_id'
            )
        );
    }
    
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'provider' => 'Provider',
            'social_user_id' => 'Social User',
            'info' => 'Info',
            'user_id' => 'User',
        	'url' => 'Url'
        );
    }
    
    /**
     * Метод проверяет, присутствует ли пользователь в таблице social_accounts.
     * Если присутствует, значит регистрация при помощи социального аккаунта была пройдена, 
     * все необходимые данные занесены в таблицы `users` и `social_accounts`. Залогиниваем юзера.
     * Иначе возвращаем модель для дальнейшей работы с ней в UsersController;
     */
    
    public function validateSocialModel()
    {
        $identity = new UserIdentity();
        $identity->userClass = $this;
    
        $identity->socialAuthenticate($this->provider, $this->social_user_id);
    
        if ($identity->errorCode === UserIdentity::ERROR_NONE) {
            //$identity->setUserData($model);
            Yii::app()->user->login($identity, Yii::app()->params['rememberMeTime']);
        }
    
        if ($identity->errorCode === UserIdentity::ERROR_UNKNOWN_IDENTITY) {
    
            $this->isNewRecord = true;
    
            if ($this->validate()) {
                return $this;
            } else {
                return false;
            }
        }
    }
    
    public function search()
    {
        $criteria = new CDbCriteria;
        
        $criteria->compare('id', $this->id);
        $criteria->compare('provider', $this->provider, true);
        $criteria->compare('social_user_id', $this->social_user_id, true);
        $criteria->compare('info', $this->info, true);
        $criteria->compare('user_id', $this->user_id);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }
    
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
