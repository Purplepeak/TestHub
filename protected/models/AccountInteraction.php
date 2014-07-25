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
     * Новый пароль, необходимый при работе со сценарием restore.
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
                'user_name, user_id, key, email, scenario',
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
                'user_name',
                'length',
                'max' => 30
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
                'id, user_name, user_id, key, email',
                'safe',
                'on' => 'search'
            )
        );
    }

    public function relations()
    {
        return array();
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
        $criteria->compare('user_name', $this->user_name);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('key', $this->key, true);
        $criteria->compare('email', $this->email, true);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    public function formatEmail($format, $template)
    {
        $root = $_SERVER['DOCUMENT_ROOT'] . Yii::app()->request->baseUrl . '/templates';
        
        $template = file_get_contents($root . $template . $format);
        
        $patterns = array(
            '/{EMAIL}/',
            '/{KEY}/',
            '/{SITEPATH}/',
            '/{NAME}/',
            '/{BASE_URL}/',
            '/{PASSWORD}/'
        );
        $replacements = array(
            $this->email,
            $this->key,
            Yii::app()->request->hostInfo,
            $this->user_name,
            Yii::app()->request->baseUrl,
            $this->newPassword
        );
        
        $template = preg_replace($patterns, $replacements, $template);
        
        return $template;
    }

    public function sendEmail($title, $template)
    {
        $bodyHtml = $this->formatEmail('html', $template);
        $bodyTxt = $this->formatEmail('txt', $template);
        
        $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 587, 'tls');
        $transport->setUsername(Yii::app()->params['siteEmail']['email']);
        $transport->setPassword(Yii::app()->params['siteEmail']['password']);
        
        $mailer = Swift_Mailer::newInstance($transport);
        $message = Swift_Message::newInstance();
        $message->setSubject($title);
        $message->setFrom(array(
            Yii::app()->params['siteEmail']['email'] => 'TestHub'
        ));
        $message->setTo(array(
            $this->email => $this->user_name
        ));
        
        $message->setBody($bodyTxt);
        $message->addPart($bodyHtml, 'text/html');
        
        $result = $mailer->send($message);
        
        return $result;
    }

    /**
     * Сохраняет данные в таблицу и отправляет пользователю e-mail, который
     * форматируется в зависимости от сценария
     */
    
    public function saveAndSend($model, $scenario)
    {
        $this->user_id = $model->id;
        $this->user_name = $model->name;
        $this->email = $model->email;
        $this->scenario = $scenario;
        $this->key = md5($this->email . time());
        
        if ($this->validate()) {
            $exist = $this->find('email=:email AND scenario=:scenario', array(
                ':email' => $this->email,
                ':scenario' => $this->scenario
            ));
            
            if ($exist != null) {
                $this->updateByPk($exist->id, array('key' => $this->key));
            } else {
                $this->save(false);
            }
            
            if ($scenario == 'confirm') {
                $title = 'Добро пожаловать на TestHub';
                $template = '/signup_template.';
            } elseif ($scenario == 'restore') {
                $title = "Здравствуйте, {$this->user_name}";
                $template = '/restore_template.';
            }
            try {
                $this->sendEmail($title, $template);
            } catch(Swift_SwiftException $e) {
                Yii::log($e->getMessage(), 'error', 'application.models.accountinteraction');
                throw new Swift_SwiftException($e->getMessage());
            }
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
