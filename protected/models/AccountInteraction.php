<?php

/**
 * Класс предназначен для взаимодействия пользователя со своим
 * аккаунтом.
 * Сценарий confirm - активация аккаунта.
 * Сценарий restore - восстановление пароля для аккаунта.
 */
class AccountInteraction extends CActiveRecord
{
    /**
     * Свойство необходимое для отправки пользователю письма
     * с новым паролем.
     */
    public $newPassword;
    
    public function tableName()
    {
        return 'account_interaction';
    }

    public function rules()
    {
        return array(
            array(
                'user_id, key, email, scenario',
                'required'
            ),
            array(
                'scenario',
                'in',
                'range' => array(
                    'confirm',
                    'restore'
                )
            ),
            array(
                'user_id, create_time',
                'numerical',
                'integerOnly' => true
            ),
            array(
                'create_time',
                'safe'
            ),
            array(
                'key',
                'length',
                'max' => 128
            ),
            array(
                'email',
                'length',
                'max' => 250
            ),
            
            array(
                'id, user_id, key, email',
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
            'user_id' => 'User',
            'key' => 'Key',
            'email' => 'Email'
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        
        $criteria->compare('id', $this->id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('key', $this->key, true);
        $criteria->compare('email', $this->email, true);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    /**
     * Сохраняет данные в таблицу и отправляет пользователю e-mail, который
     * форматируется в зависимости от сценария
     */
    public function saveAndSend($model, $scenario)
    {
        $this->user_id = $model->id;
        $this->email = $model->email;
        $this->scenario = $scenario;
        $this->key = md5($this->email . time());
        
        if ($this->validate()) {
            $exist = $this->find('email=:email AND scenario=:scenario', array(
                ':email' => $this->email,
                ':scenario' => $this->scenario
            ));
            
            if ($exist != null) {
                $this->updateByPk($exist->id, array(
                    'key' => $this->key
                ));
            } else {
                $this->save(false);
            }
            
            if ($scenario == 'confirm') {
                $title = 'Добро пожаловать на TestHub';
                $template = '/signup_template.';
            } elseif ($scenario == 'restore') {
                $title = "Здравствуйте, {$this->user->name}";
                $template = '/restore_template.';
            }
            
            $this->sendEmail($scenario);
        }
    }
    
    public function sendEmail($scenario)
    {
        $mailer = new SMailer;
        $mailer->init($scenario, $this->user->name, $this->email, $this->key, $this->newPassword);
        
        try {
            $mailer->sendEmail();
        } catch (Swift_SwiftException $e) {
            Yii::log($e->getMessage(), 'error', 'application.models.accountinteraction');
            throw new Swift_SwiftException($e->getMessage());
        }
    }

    public function findByEmailKey($email, $key, $scenario)
    {
        $account = $this->find('email=:email AND `key`=:key AND scenario=:scenario', array(
            ':email' => $email,
            ':key' => $key,
            ':scenario' => $scenario
        ));
        
        return $account;
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
