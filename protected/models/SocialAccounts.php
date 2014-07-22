<?php

/**
 * This is the model class for table "social_accounts".
 *
 * The followings are the available columns in table 'social_accounts':
 * @property integer $id
 * @property string $provider
 * @property string $social_user_id
 * @property string $info
 * @property integer $user_id
 *
 * The followings are the available model relations:
 * @property Users $user
 */
class SocialAccounts extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'social_accounts';
    }
    
    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
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
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, provider, social_user_id, info, user_id',
                'safe',
                'on' => 'search'
            )
        );
    }
    
    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'user' => array(
                self::BELONGS_TO,
                'Users',
                'user_id'
            )
        );
    }
    
    /**
     * @return array customized attribute labels (name=>label)
     */
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
    
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        
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
