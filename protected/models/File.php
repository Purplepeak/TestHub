<?php

class File extends CActiveRecord
{
    public $image;
    
    public $imageFormat = array('image/jpeg', 'image/gif', 'image/gif');

    public function tableName()
    {
        return 'teacher_group';
    }

    /**
     *
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            
            array(
                'image',
                'file',
                'safe' => true,
                'types' => 'jpg, gif, png',
                'allowEmpty' => false,
                'tooLarge' => 'Размер картинки не должен превышать 1МБ.',
                'wrongType' => 'Допустимые расширения аватара: jpg, gif, png.',
            ),
            
            
        );
    }

    /**
     *
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array();
    }

    /**
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'image' => 'File[image]'
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
