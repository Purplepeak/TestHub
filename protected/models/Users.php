<?php

class Users extends CActiveRecord
{

    public $_type;
    
    // Пароль
    public $passwordText;
    
    // Строка подтверждения пароля
    public $password2;
    
    // Поле для капчи
    public $captcha;
    
    // Через сколько дней удалять неактивированные аккаунты
    public $interval = 2;

    public $fullname = '';

    public $searchGroup;

    public $newAvatar;

    public $avatar190;

    public $avatar50;

    public $avatar30;
    
    public $avatarX;
    
    public $avatarY;
    
    public $avatarWidth;
    
    public $avatarHeight;

    const GENDER_MALE = 'male';

    const GENDER_FEMALE = 'female';

    const GENDER_UNDEFINED = 'undefined';

    public function tableName()
    {
        return 'users';
    }

    protected function instantiate($attributes)
    {
        switch ($attributes['type']) {
            case 'student':
                $class = 'Student';
                break;
            case 'teacher':
                $class = 'Teacher';
                break;
            case 'admin':
                $class = 'Admin';
                break;
            default:
                throw new CException('Nonexistent user type');
        }
        $model = new $class(null);
        return $model;
    }

    public function rules()
    {
        return array(
            array(
                'name, surname',
                'required',
                'on' => 'register',
                'message' => 'Поле не должно быть пустым'
            ),
            
            array(
                'group_id, time_registration',
                'numerical',
                'integerOnly' => true
            ),
            
            array(
                'newAvatar',
                'required',
                'message' => '',
                'on' => 'changeAvatar'
            ),
            
            array(
                'newAvatar',
                'file',
                'safe' => true,
                'types' => 'jpg, gif, png',
                'allowEmpty' => true,
                'maxSize' => 1000 * 1024,
                'tooLarge' => 'Размер картинки не должен превышать 1МБ',
                'wrongType' => 'Допустимые расширения аватара: jpg, gif, png',
                'on' => 'changeAvatar'
            ),
            
            array(
                'gender',
                'default',
                'value' => self::GENDER_UNDEFINED
            ),
            
            array(
                'gender, newAvatar, id, password',
                'safe'
            ),
            
            array(
                'avatarX, avatarY, avatarWidth, avatarHeight',
                'safe',
                'on' => 'changeAvatar'
            ),
            
            array(
                'email',
                'unique',
                'className' => 'Users',
                'attributeName' => 'email',
                'message' => 'Этот e-mail уже используется'
            ),
            
            array(
                'passwordText, password2, email',
                'required',
                'on' => 'register, changePass',
                'message' => 'Поле не должно быть пустым'
            ),
            array(
                'passwordText, password2',
                'length',
                'min' => 6,
                'max' => 200,
                'tooShort' => 'Пароль слишком короткий (6 символов минимум)',
                'tooLong' => 'Пароль слишком длинный (200 символов максимум)'
            ),
            array(
                'password2',
                'compare',
                'compareAttribute' => 'passwordText',
                'on' => 'register, changePass',
                'message' => 'Введенные пароли не совпадают'
            ),
            array(
                'name, surname',
                'length',
                'max' => 30,
                'message' => 'Имя или фамилия введены некорректно'
            ),
            array(
                'avatar',
                'length',
                'max' => 200
            ),
            array(
                'email',
                'length',
                'max' => 100
            ),
            array(
                'email',
                'email',
                'on' => 'register',
                'message' => 'e-mail введен некорректно'
            ),
            
            array(
                'name, surname',
                'match',
                'pattern' => '/[А-Яа-яёЁ]+/ui',
                'on' => 'register',
                'message' => 'Имя и фамилия можгут содержать только кириллические символы'
            ),
            array(
                'type',
                'default',
                'value' => "{$this->_type}"
            ),
            array(
                'gender',
                'in',
                'range' => array(
                    self::GENDER_MALE,
                    self::GENDER_FEMALE,
                    self::GENDER_UNDEFINED
                ),
                'allowEmpty' => true,
                'message' => 'Указаный пол не предусматривается правилами'
            ),
            array(
                'captcha',
                'ext.srecaptcha.SReCaptchaValidator',
                'privateKey' => Yii::app()->params['privateConfig']['recaptcha']['privateKey'],
                'on' => 'register'
            ),
            array(
                'active',
                'boolean'
            ),
            array(
                'id, password, email, time_registration, name, surname, gender, avatar, group_id',
                'safe',
                'on' => 'search'
            )
        );
    }

    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'passwordText' => 'Пароль',
            'password2' => 'Повторите пароль',
            'email' => 'e-mail',
            'time_registration' => 'Время регистрации',
            'name' => 'Имя',
            'surname' => 'Фамилия',
            'gender' => 'Пол',
            'avatar' => 'Аватар',
            'group_id' => 'ID группы',
            'captcha' => 'Введите символы с картинки',
            'newAvatar' => 'Выберите аватар'
        );
    }

    public function search()
    {
        $criteria = new CDbCriteria();
        
        $this->addSearchConditions($criteria);
        
        $criteria->order = 'surname ASC';
        $criteria->addInCondition('active', array(
            true
        ));
        
        if (! empty($this->fullname)) {
            $criteria->addSearchCondition('surname', $this->fullname);
            $criteria->addSearchCondition('name', $this->fullname, true, 'OR');
        }
        
        $criteria->compare('id', $this->id);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('time_registration', $this->time_registration);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('surname', $this->surname, true);
        $criteria->compare('gender', $this->gender, true);
        $criteria->compare('avatar', $this->avatar, true);
        $criteria->compare('group_id', $this->group_id);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20
            )
        ));
    }

    /**
     * Перед сохранением значений в базу данных, хешируем пароль, для повышения
     * уровня безопасности пользовательских аккаунтов
     */
    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                if ($this->scenario == 'register') {
                    $this->password = $this->hashPassword($this->passwordText);
                    
                    $formatName = array_map('mb_strtolower', array(
                        $this->name,
                        $this->surname
                    ));
                    $formatName = array_map('mb_convert_case', $formatName, array_fill(0, count($formatName), MB_CASE_TITLE));
                    
                    $this->name = $formatName[0];
                    $this->surname = $formatName[1];
                }
            }
            
            return true;
        }
        
        return false;
    }

    public function getFullName()
    {
        return "{$this->surname} {$this->name}";
    }

    /**
     * Метод используется в dev-версии.
     * Удаление не активированных пользовательских аккаунтов
     * Такие аккаунты хранятся в базе не дольше двух дней, если
     * их общее количество превышает 10.
     */
    public function deleteNotActivated()
    {
        if (Yii::app()->params['dev']) {
            $userCriteria = new CDbCriteria();
            $userCriteria->condition = "active=:active AND time_registration < DATE_SUB(NOW(), INTERVAL :interval DAY)";
            $userCriteria->params = array(
                ':active' => false,
                ':interval' => $this->interval
            );
            
            $count = $this->count($userCriteria);
            
            if ($count > 10) {
                $this->deleteAll($userCriteria);
            }
        }
    }

    public function validatePassword($password)
    {
        return CPasswordHelper::verifyPassword($password, $this->password);
    }

    public function hashPassword($password)
    {
        return CPasswordHelper::hashPassword($password);
    }

    public function uploadAvatar($user)
    {
        if ($this->scenario === 'changeAvatar') {
            $imageDir = sprintf("%s" . "/" . "%d" . "/", Yii::getPathOfAlias('avatarFolder'), $this->id);
            
            if(!file_exists($imageDir)) {
                mkdir($imageDir, 0777, true);
            }
            
            SHelper::deleteFolder($imageDir);
            
            $safeName = $this->getSafeImageName($this->newAvatar->name);
            $this->newAvatar->saveAs($imageDir . $safeName);
            
            $sitePath = Yii::app()->request->hostInfo . Yii::app()->request->baseUrl;
            $avatarPath = Yii::app()->params['avatarRelativePath'] . '/' . $user->id;
            
            self::model()->updateByPk($user->id, array(
                'avatar' => $avatarPath .'/'. $safeName
            ));
            
            $avatarThumb = new Thumbnail($imageDir);
            $avatarThumb->cropWithCoordinates($sitePath . $avatarPath . '/' . $safeName, $this->avatarX, $this->avatarY, $this->avatarWidth, $this->avatarHeight, $this->avatarWidth, $this->avatarHeight, 'crop');
            
            self::model()->updateByPk($user->id, array(
                'main_avatar' => $avatarPath .'/'. "{$this->avatarWidth}x$this->avatarHeight" . '/' . 'crop' . '/' . $safeName
            ));
            
            //$this->newAvatar = CUploadedFile::getInstance($model, 'newAvatar');
        }
    }

    private function getSafeImageName($name)
    {
        $nameReg = '{(.*)(\\..+)}ui';
        if (preg_match($nameReg, $name, $nameArray)) {
            $fileName = $nameArray[1];
            $fileExt = $nameArray[2];
        }
        
        $safeName = preg_replace('/[^a-zA-ZА-ЯЁа-яё0-9\+\-\)\(\)\]\[]/ui', '', $fileName) . "{$fileExt}";
        
        return $safeName;
    }

    public function saveMainAvatar($user)
    {
        $avatarThumb = new Thumbnail(Yii::getPathOfAlias('avatarFolder') . "/{$user->id}/");
        $avatarThumb->cropWithCoordinates($this->avatar, Yii::app()->request->getPost('x'), Yii::app()->request->getPost('y'), Yii::app()->request->getPost('width'), Yii::app()->request->getPost('height'), Yii::app()->request->getPost('width'), Yii::app()->request->getPost('height'), 'crop');
        
        //$this->newAvatar = CUploadedFile::getInstance($model, 'newAvatar');
    }

    public function addSearchConditions($criteria)
    {}

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
