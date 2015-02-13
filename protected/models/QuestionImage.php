<?php

class QuestionImage extends TestImages
{

    public $_type = 'question';
    
    public $imageDir;
    
    public $idAttribute;
    
    protected $type;
    
    public function init()
    {
        $this->imageDir = Yii::getPathOfAlias('questionImages');
    
        $this->idAttribute = 'question_id';
    
        parent::init();
    }

    public function defaultScope()
    {
        return array(
            'condition' => "q_image.type='{$this->_type}'",
            'alias' => 'q_image'
        );
    }

    public function rules()
    {
        $rules = parent::rules();
        
        array_push($rules, array(
            'question_id',
            'required',
            'on' => 'saveRecord',
            'message' => 'Необходимо указать id вопроса.'
        ), array(
            'type',
            'default',
            'value' => self::TYPE_QUESTION,
            'on' => 'saveRecord'
        ));
        
        return $rules;
    }

    public function relations()
    {
        return array(
            'question' => array(
                self::BELONGS_TO,
                'Question',
                'question_id'
            )
        );
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
