<?php

/**
 * This is the model class for table "answer_options".
 *
 * The followings are the available columns in table 'answer_options':
 * @property integer $id
 * @property integer $question_id
 * @property integer $answer
 *
 * The followings are the available model relations:
 * @property Question $question
 * @property Question[] $questions
 */
class AnswerOptions extends CActiveRecord
{

    /**
     *
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'answer_options';
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
                'question_id, option_text, option_number',
                'required'
            ),
            array(
                'question_id, option_number',
                'numerical',
                'integerOnly' => true
            ),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array(
                'id, question_id, option_text, option_number',
                'safe',
                'on' => 'search'
            )
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
        return array(
            'question' => array(
                self::BELONGS_TO,
                'Question',
                'question_id'
            ),
            'correct_answer' => array(
                self::MANY_MANY,
                'Question',
                'correct_answers(c_answer, question_id)'
            ),
            'studentManyOptions' => array(
                self::MANY_MANY,
                'StudentAnswer',
                's_many_answers(s_answer, answer_id)'
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
            'question_id' => 'Question',
            'option_text' => 'Option Text',
            'option_number' => 'Option Number'
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
        $criteria->compare('question_id', $this->question_id);
        $criteria->compare('option_text', $this->option_text);
        $criteria->compare('option_number', $this->option_number);
        
        return new CActiveDataProvider($this, array(
            'criteria' => $criteria
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * 
     * @param string $className
     *            active record class name.
     * @return AnswerOptions the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
