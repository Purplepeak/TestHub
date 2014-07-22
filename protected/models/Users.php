<?php

/**
 * This is the model class for table "users".
 *
 * The followings are the available columns in table 'users':
 * @property integer $id
 * @property string $password
 * @property string $email
 * @property integer $time_registration
 * @property string $name
 * @property string $surname
 * @property string $gender
 * @property string $avatar
 * @property integer $group_id
 *
 * The followings are the available model relations:
 * @property Test[] $tests
 * @property Group[] $groups
 * @property Test[] $tests1
 * @property Group $group
 */
class Users extends CActiveRecord
{

    public $_type;

    public $passwordText;

    public $password2;

    public $captcha;

    public $interval = 2; // Через сколько дней удалять неактивированные аккаунты
    const GENDER_MALE = 'male';

    const GENDER_FEMALE = 'female';

    const GENDER_UNDEFINED = 'undefined';

    /**
     *
     * @return string the associated database table name
     */
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

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array(
                'name, surname',
                'required',
                'message' => 'Поле не должно быть пустым'
            ),
            
            array(
                'group_id, time_registration',
                'numerical',
                'integerOnly' => true
            ),
            
            array(
                'gender',
                'default',
                'value' => self::GENDER_UNDEFINED
            ),
            array(
                'avatar',
                'default',
                'value' => Yii::app()->request->baseUrl . Yii::app()->params['defaultAvatar']
            ),
            
            array(
                'gender, id, password',
                'safe'
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
                'privateKey' => Yii::app()->params['socialKeys']['recaptcha']['privateKey'],
                'on' => 'register'
            ),
            array(
                'active',
                'in',
                'range' => array(
                    0,
                    1
                )
            ),
            array(
                'id, password, email, time_registration, name, surname, gender, avatar, group_id',
                'safe',
                'on' => 'search'
            )
        );
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
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
            'captcha' => 'Введите код с картинки через пробел'
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     *         based on the search/filter conditions.
     */
    public function search()
    {
        // @todo Please modify the following code to remove attributes that should not be searched.
        $criteria = new CDbCriteria();
        
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
            'criteria' => $criteria
        ));
    }

    protected function beforeSave()
    {
        if (parent::beforeSave()) {
            if ($this->isNewRecord) {
                if ($this->scenario == 'register') {
                    $this->password = $this->hashPassword($this->passwordText);
                }
            }
            
            return true;
        }
        
        return false;
    }

    public function deleteNotActivated()
    {
        $userCriteria = new CDbCriteria();
        $userCriteria->condition = "active=:active AND time_registration < DATE_SUB(NOW(), INTERVAL :interval DAY)";
        $userCriteria->params = array(
            ':active' => 0,
            ':interval' => $this->interval
        );
        
        $confirmCriteria = new CDbCriteria();
        $confirmCriteria->condition = "scenario=:scenario AND time_registration < DATE_SUB(NOW(), INTERVAL :interval DAY)";
        $confirmCriteria->params = array(
            ':scenario' => 'confirm',
            ':interval' => $this->interval
        );
        
        $count = $this->count($userCriteria);
        
        if ($count > 10) {
            $this->deleteAll($userCriteria);
            $this->deleteAll($confirmCriteria);
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

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
