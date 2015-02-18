<?php

class Users extends CActiveRecord
{

    public $_type;
    
    protected $tableAlias;
    
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
    
    // максимальный размер аватара в мегабайтах
    public $avatarMaxSize = 2;

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
                'message' => 'Поле не должно быть пустым.'
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
                'maxSize' => $this->avatarMaxSize * 1024 * 1024,
                'tooLarge' => 'Размер картинки не должен превышать '. $this->avatarMaxSize .'МБ.',
                'wrongType' => 'Допустимые расширения аватара: jpg, gif, png.',
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
                'message' => 'Этот адрес уже используется.'
            ),
            
            array(
                'passwordText, password2, email',
                'required',
                'on' => 'register, changePass',
                'message' => 'Поле не должно быть пустым.'
            ),
            array(
                'passwordText, password2',
                'length',
                'min' => 6,
                'max' => 200,
                'tooShort' => 'Пароль слишком короткий (6 символов минимум).',
                'tooLong' => 'Пароль слишком длинный (200 символов максимум).'
            ),
            array(
                'password2',
                'compare',
                'compareAttribute' => 'passwordText',
                'on' => 'register, changePass',
                'message' => 'Введенные пароли не совпадают.'
            ),
            array(
                'name, surname',
                'length',
                'max' => 30,
                'message' => 'Имя или фамилия введены некорректно.'
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
                'message' => 'Email введен некорректно.'
            ),
            
            array(
                'name, surname',
                'match',
                'pattern' => '/[А-Яа-яёЁ]+/ui',
                'on' => 'register',
                'message' => 'Имя и фамилия могут содержать только кириллические символы.'
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
                'message' => 'Указаный пол не предусматривается правилами.'
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
            'email' => 'Email',
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
        
        if ($this->fullname) {
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
        
        $count = $this->count($criteria);
        
        $pages=new CPagination($count);
        $pages->pageSize=15;
        $pages->applyLimit($criteria);
        
        if($this->_type === 'teacher') {
            $teacherGroups = TeacherGroup::model()->count();
            $pages->pageSize = $teacherGroups + $count;
        }
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => $pages
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
            $imageDir = sprintf("%s" . "/" . "%d" . "/", Yii::getPathOfAlias('avatarDir'), $this->id);
            
            $imageDirObject = Yii::app()->file->set($imageDir, true);
            
            if (! $imageDirObject->exists) {
                $imageDirObject->createDir(0777, $imageDirObject->realpath);
            }
            
            $resolution = array_map('round', array(
                'width' => $this->avatarWidth,
                'height' => $this->avatarHeight
            ));
            
            if(!$imageDirObject->isEmpty) {
                $imageDirObject->purge();
            }
            
            $safeName = SHelper::getSafeImageName($this->newAvatar->name, 'avatar', $user->id);
            $this->newAvatar->saveAs($imageDir .'/'. $safeName);
            
            $avatarPath = Yii::app()->params['avatarRelativePath'] . '/' . $user->id;
            
            self::model()->updateByPk($user->id, array(
                'avatar' => $avatarPath . '/' . $safeName
            ));
            
            $croppedAvatarName = SHelper::getSafeImageName($this->newAvatar->name, 'cropped', $user->id);
            
            $avatarThumb = new SAvatarCropper($imageDir, true);
            $avatarThumb->newImageName = $croppedAvatarName;
            $avatarThumb->cropWithCoordinates($imageDir .'/'. $safeName, $this->avatarX, $this->avatarY, $resolution['width'], $resolution['height'], $resolution['width'], $resolution['height'], 'crop');
            
            self::model()->updateByPk($user->id, array(
                'avatarX' => $this->avatarX,
                'avatarY' => $this->avatarY,
                'avatarWidth' => $resolution['width'],
                'avatarHeight' => $resolution['height'],
                'cropped_avatar' => $avatarThumb->changedImageName
            ));
            
            
            $avatarDir = Yii::app()->request->baseUrl . Yii::app()->params['avatarRelativePath']. '/' .$user->id;
            $avatarCropper = new SAvatarCropper($avatarDir);
            
            Yii::app()->user->setState('__userMainAvatar', $avatarCropper->link($avatarThumb->changedImageName, Yii::app()->params['mainAvatarSize']['width'], Yii::app()->params['mainAvatarSize']['height'], 'crop'));
            Yii::app()->user->setState('__userMenuAvatar', $avatarCropper->link($avatarThumb->changedImageName, Yii::app()->params['menuAvatarSize']['width'], Yii::app()->params['menuAvatarSize']['height'], 'crop'));
        }
    }

    public function saveMainAvatar($user)
    {
        $avatarThumb = new SAvatarCropper(Yii::getPathOfAlias('avatarDir') . "/{$user->id}/");
        $avatarThumb->cropWithCoordinates($this->avatar, Yii::app()->request->getPost('x'), Yii::app()->request->getPost('y'), Yii::app()->request->getPost('width'), Yii::app()->request->getPost('height'), Yii::app()->request->getPost('width'), Yii::app()->request->getPost('height'), 'crop');
        
        // $this->newAvatar = CUploadedFile::getInstance($model, 'newAvatar');
    }

    public function addSearchConditions($criteria)
    {}

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
