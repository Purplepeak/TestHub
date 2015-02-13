<?php

class TestForewordImage extends TestImages
{

    public $_type = 'test';
    
    protected $imageDir;
    
    protected $idAttribute;
    
    protected $type;
    
    public function init()
    {
        $this->imageDir = Yii::getPathOfAlias('forewordImages');
        
        $this->idAttribute = 'test_id';
        
        parent::init();
    }

    public function defaultScope()
    {
        return array(
            'condition' => "t_image.type='{$this->_type}'",
            'alias' => 't_image'
        );
    }

    public function rules()
    {
        $rules = parent::rules();
        
        array_push($rules, array(
            'test_id',
            'required',
            'on' => 'saveRecord',
            'message' => 'Необходимо указать id теста.'
        ), array(
            'type',
            'default',
            'value' => self::TYPE_FOREWORD,
            'on' => 'saveRecord'
        ));
        
        return $rules;
    }

    public function relations()
    {
        return array(
            'test' => array(
                self::BELONGS_TO,
                'Test',
                'test_id'
            )
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
